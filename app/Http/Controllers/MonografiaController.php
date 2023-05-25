<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\DB;

use App\Models\Monografia;
use App\Models\User;
use App\Models\Unitermo;
use App\Models\Aluno;
use App\Models\Orientador;
use App\Models\MonoOrientadores;
use App\Models\Parametro;
use App\Models\Avaliacao;
use App\Models\AreasTematica;
use App\Models\MonoUnitermos;

use App\Rules\CountWord;

use App\Mail\NotificacaoOrientador;
use App\Mail\NotificacaoAluno;

class MonografiaController extends Controller
{    
    private $autenticacao;

    /**
     * Autenticação
     */
    public function __contruct() {

        if (!auth()->check()) {
            return redirect(route('home'));
        }

        $this->autenticacao = auth()->user()->verificaIdentidade();
    }

    /**
     * Display a listing of the resource.
     * Mostra o cadastro da monografia
     *
     * @return \Illuminate\Http\Response
     */
    public function index($monografia_id = 0, $ano = null, $msg = null, $acao = null)
    {        
        $dadosParam = Parametro::where("ano",date("Y"))->whereNull('codpes')->get();
        if (!$dadosParam->isEmpty()) {
            $dadosParam->first()->dataAberturaDiscente = date_create($dadosParam->first()->dataAberturaDiscente);
            $dadosParam->first()->dataFechamentoDiscente = date_create($dadosParam->first()->dataFechamentoDiscente);
            $dadosParam->first()->dataAberturaDocente = date_create($dadosParam->first()->dataAberturaDocente);
            $dadosParam->first()->dataFechamentoDocente = date_create($dadosParam->first()->dataFechamentoDocente);
            $dataAtual = date_create('now');
        }
        $readonly = false;
        $publicar = false;
        $userLogado = null;
        $msgAbertura = null;
        $avaliar = false;
        if (!empty($acao))
            $avaliar = true;
        
        if (auth()->user()->hasRole('orientador') && !auth()->user()->can('admin')) {
            $userLogado = "Orientador";

            if ($dataAtual >= $dadosParam->first()->dataAberturaDocente && 
                $dataAtual <= $dadosParam->first()->dataFechamentoDocente ) {
                    $readonly = true;
                    $userLogado = "Orientador";
            } else {
                return "<script> alert('M7-Abertura do Sistema entre ".$dadosParam->first()->dataAberturaDocente->format('d/m/Y')." e ".$dadosParam->first()->dataFechamentoDocente->format('d/m/Y')."'); 
                                        window.location.assign('".route('home')."');
                        </script>";
            }

        } elseif (auth()->user()->hasRole('aluno')) {
            $userLogado = "Aluno";
            
            if ($dataAtual < $dadosParam->first()->dataAberturaDiscente) {
                return "<script> alert('M8-Aguarde abertura do Sistema a partir de ".$dadosParam->first()->dataAberturaDiscente->format('d/m/Y')."'); 
                                        window.location.assign('".route('home')."');
                        </script>";
            }

            if ($dataAtual > $dadosParam->first()->dataFechamentoDiscente) {
                return "<script> alert('M9-Sistema fechado para cadastro da monografia desde o dia ".$dadosParam->first()->dataFechamentoDiscente->format('d/m/Y')."'); 
                                        window.location.assign('".route('home')."');
                        </script>"; 
            }

        } elseif (auth()->user()->hasRole('graduacao')) {
            $userLogado = "Graduacao";
            $publicar = true;
        } elseif (auth()->user()->can('admin')) {
            $userLogado = "Admin";
            $publicar = true;
        }

        $numUSPAluno = 0;
        $nomeAluno = null;
        $dadosMonografia = null;
        $dadosAlunoGrupo = null;
        $edicao = false;
        $dadosMonografia = [];
        
        if ($monografia_id > 0) {
            if (empty($ano)) {
                $dadosMonografia = Monografia::with(['alunos','orientadores','unitermos'])->orderBy('ano')->where('id',$monografia_id)->get();
            } else {
                $dadosMonografia = Monografia::with(['alunos','orientadores','unitermos'])->orderBy('ano')->where('id',$monografia_id)->where('ano',$ano)->get();
            }

            if (!$dadosMonografia->isEmpty()) {
                $dadosAlunoGrupo = Aluno::where('monografia_id',$monografia_id)->get();
            } else {
                return "<script> alert('M7-Erro ao informar monografia. Entre em contato com a Graduação'); 
                                 window.location.assign('".route('home')."');
                        </script>"; 

            }
            $numUSPAluno = $dadosAlunoGrupo->first()->id;
            $nomeAluno = $dadosAlunoGrupo->first()->nome;
            
            if (auth()->user()->hasRole('aluno')) {
                if (Aluno::where('id',auth()->user()->codpes)->where('monografia_id',$monografia_id)->count() == 0) {
                    return "<script> alert('M3-Monografia não está no seu nome. Entre em contato com a Graduação'); 
                                     window.location.assign('".route('home')."');
                            </script>"; 

                }
                $dadosAlunoGrupo = Aluno::where('monografia_id',$dadosMonografia->first()->id)->whereNotIn('id',[auth()->user()->codpes])->get();
                $numUSPAluno = auth()->user()->codpes;
                $nomeAluno = auth()->user()->name;
            } elseif (auth()->user()->hasRole('orientador') && !auth()->user()->can('admin')) {

                $ddOrientador = Orientador::where('codpes',auth()->user()->codpes)->get();
                if (MonoOrientadores::where('orientadores_id',$ddOrientador->first()->id)->where('monografia_id',$dadosMonografia->first()->id)->count() == 0) {
                    return "<script> alert('M5-Monografia não está sob sua orientação. Entre em contato com a Graduação'); 
                                     window.location.assign('".route('home')."');
                            </script>"; 

                }
                $numUSPAluno = $dadosAlunoGrupo->first()->id;
                $nomeAluno = $dadosAlunoGrupo->first()->nome;

                $dadosAlunoGrupo = Aluno::where('monografia_id',$dadosMonografia[0]->id)->whereNotIn('id',[$dadosAlunoGrupo->first()->id])->get();
            } 
        } elseif (auth()->user()->hasRole('aluno')) {
            if (Aluno::where('id',auth()->user()->codpes)->count() == 0) {
                $dadosReplicado = Aluno::getDadosAluno(auth()->user()->codpes);
                $dadosAluno = new Aluno;
                $dadosAluno->id = $dadosReplicado[array_key_first($dadosReplicado)]->numUSP;
                $dadosAluno->nome = $dadosReplicado[array_key_first($dadosReplicado)]->nome;
                $mono_id = 0;
            } else {
                $dadosAluno = Aluno::find(auth()->user()->codpes);
                $mono_id = $dadosAluno->monografia_id;
            }
            
            if ($mono_id > 0) {
                if (empty($ano)) {
                    $dadosMonografia = Monografia::with(['alunos', 'orientadores','unitermos'])->orderBy('ano')->where('id',$mono_id)->get();
                } else {
                    $dadosMonografia = Monografia::with(['alunos', 'orientadores','unitermos'])->orderBy('ano')->where('id',$mono_id)->where('ano',$ano)->get();
                }

                $dadosAlunoGrupo = Aluno::where('monografia_id',$dadosMonografia->first()->id)->whereNotIn('id',[auth()->user()->codpes])->get();
            } else {
                $dadosAlunoGrupo = Aluno::where('id',[auth()->user()->codpes])->get();
            }
 
            $numUSPAluno = auth()->user()->codpes;
            $nomeAluno = auth()->user()->name;
        } elseif (!auth()->user()->can('admin')) {
            return "<script> alert('M6-Você não pode acessar essa parte do sistema diretamente.'); 
                                     window.location.assign('".route('home')."');
                    </script>";
        }
        
        $dadosOrientadores = array();
        $unitermos = Unitermo::where('unitermo','like','%')->orderBy('unitermo')->get();
        $areas_tematicas = AreasTematica::where('descricao','like','%')->orderBy('descricao')->get();
        
        $orientadorId = 0;
        $orientadorSecundario = array();
        $dadosUnitermos = null;
        $dadosAvaliacoes = null;
        
        if (!is_array($dadosMonografia) && !$dadosMonografia->isEmpty()) {
            $dadosMonografia = $dadosMonografia->first();
            
            foreach ($dadosMonografia->orientadores as $listOrient) {
                if ($listOrient->pivot->principal != 1) {
                    if ($listOrient->externo)
                        $orientadorSecundario[] = "CPF: ".$listOrient->CPF." Nome: ".$listOrient->nome;
                    else
                        $orientadorSecundario[] = "Número USP :".$listOrient->codpes." Nome: ".$listOrient->nome;
                } else {
                    $orientadorId = $listOrient->id;
                }
            }
            
            $dadosUnitermos = $dadosMonografia->unitermos;
            $edicao = true;

            if ($dadosMonografia->status == "CONCLUIDO") {
                $dadosAvaliacoes = Avaliacao::where("monografia_id",$dadosMonografia->id)
                                            ->where("status","APROVADO")
                                            ->orWhere("status","REPROVADO")
                                            ->get();

                if (!$dadosAvaliacoes->isEmpty()) {
                    $avOrientador = Orientador::find($dadosAvaliacoes->first()->orientadores_id);
                    $dadosAvaliacoes->first()->dataAvaliacao = date_create($dadosAvaliacoes->first()->dataAvaliacao);
                    $dadosAvaliacoes->_orientador = $avOrientador->codpes." ".$avOrientador->nome;
                }
            } else {
                $dadosAvaliacoes = Avaliacao::where("monografia_id",$dadosMonografia->id)
                                            ->where("status","DEVOLVIDO")->get();
                
                if (!$dadosAvaliacoes->isEmpty()) {
                    if (auth()->user()->hasRole("aluno"))
                        $readonly = false;
                    
                    $avOrientador = Orientador::find($dadosAvaliacoes->first()->orientadores_id);
                    $dadosAvaliacoes->first()->dataAvaliacao = date_create($dadosAvaliacoes->first()->dataAvaliacao);
                    $dadosAvaliacoes->_orientador = $avOrientador->codpes." ".$avOrientador->nome;
                }
            }
            
        } else {
            $dadosAvaliacoes = Avaliacao::where("monografia_id",0)
                                        ->where("status","DEVOLVIDO")->get();
        }
        
        $listaAlunosDupla = Aluno::getDadosAluno();
        $listaOrientadores = Orientador::where('nome','like','%')->orderBy('nome')->get();

        $parametros = ["numUSPAluno"        => $numUSPAluno
                      ,"nomeAluno"          => $nomeAluno
                      ,"listaAlunosDupla"   => $listaAlunosDupla
                      ,"listaOrientadores"  => $listaOrientadores
                      ,"unitermos"          => $unitermos
                      ,"readonly"           => $readonly
                      ,"publicar"           => $publicar
                      ,"dadosMonografia"    => $dadosMonografia
                      ,"dadosAlunoGrupo"    => (!empty($dadosAlunoGrupo))?$dadosAlunoGrupo->first():null
                      ,"dadosOrientadores"  => $dadosOrientadores
                      ,"userLogado"         => $userLogado
                      ,"orientadorSecundario"=>$orientadorSecundario
                      ,"orientadorId"       => $orientadorId
                      ,"mensagem"           => $msg
                      ,"edicao"             => $edicao
                      ,"dadosUnitermos"     => $dadosUnitermos
                      ,"acao"               => $acao
                      ,"avaliar"            => $avaliar
                      ,"monografiaId"       => (empty($dadosMonografia))?0:$dadosMonografia->id
                      ,"dadosAvaliacoes"    => $dadosAvaliacoes
                      ,"areas_tematicas"    => $areas_tematicas
                      ];
        
        return view('formMonografia',$parametros);
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return $this->index();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [];
        if ($request->input('dupla')) {
            $rules['pessoaDupla'] = "required";
            $messages['pessoaDupla.required'] = "Favor informar o componente do grupo de trabalho.";
        }

        $rules['orientador_id']     = "required";
        $rules['titulo']            = ["required","min:3","max:255"];
        $rules['resumo']            = ["required","min:3",new CountWord];
        if (!$request->filled('txtUnitermo1'))
            $rules['unitermo1']     = ["required", "exists:unitermos,id"];
        if (!$request->filled('txtUnitermo2')) 
            $rules['unitermo2']     = ["required", "exists:unitermos,id"];
        if (!$request->filled('txtUnitermo3'))
            $rules['unitermo3']     = ["required", "exists:unitermos,id"];
        //$rules['unitermo4']         = ["exists:unitermos,id"];
        //$rules['unitermo5']         = ["exists:unitermos,id"];
        $rules['template_apres']   = ["file","required"];
        $rules['cod_area_tematica'] = ["required", "exists:areastematicas,id"];
        
        $messages['required']                 = "Favor informar o :attribute da monografia.";
        $messages['min']                      = "O :attribute deve conter no mínimo 3 caracteres";
        $messages['titulo.max']               = "O título deve conter no máximo 255 caracteres";
        $messages['cod_area_tematica.exists'] = "A área temática deve estar previamente cadastrada no sistema";
        $messages['exists']                   = "O :attribute deve estar previamente cadastrado.";
        $messages['template_apres.file']      = "O arquivo da monografia não é válido";
        $messages['template_apres.required']  = "O arquivo da monografia deve ser informado.";

        $request->validate($rules,$messages);
        
        $arquivo = $request->file('template_apres');
        $arquivo->move(public_path('upload'),$arquivo->getClientOriginalName());

        $dadosParam = Parametro::where("ano",date("Y"))->whereNull('codpes')->get();
        if (!$dadosParam->isEmpty()) {
            $dadosParam->first()->dataAberturaDocente = date_create($dadosParam->first()->dataAberturaDocente);
            $dataAtual = date_create('now');
        }
        
        $mensagem = null;

        try {

            $monografia = new Monografia();
            $monografia->dupla             = empty($request->input('dupla'))?0:1;
            $monografia->titulo            = $request->input('titulo');
            $monografia->resumo            = $request->input('resumo');
            $monografia->template_apres    = $arquivo->getClientOriginalName();
            $monografia->areastematicas_id = $request->input('cod_area_tematica');
            $monografia->ano = date('Y');

            $monografia->save();

            $aluno = new Aluno;
            $aluno->id = auth()->user()->codpes;
            $aluno->nome = auth()->user()->name;
            $aluno->monografia_id = $monografia->id;

            $aluno->firstOrCreate(['id'=>auth()->user()->codpes]
                                 ,['id'=>auth()->user()->codpes
                                  ,'nome'=>auth()->user()->name
                                  ,'monografia_id'=>$monografia->id]);

            if (!empty($request->input('dupla')) && $request->input('dupla') == 1) {
                $dadosAlunoDupla = Aluno::getDadosAluno($request->input('pessoaDupla'));

                $aluno->firstOrCreate(['id'=>$request->input('pessoaDupla')]
                                     ,['id'=>$request->input('pessoaDupla')
                                     ,'nome'=>$dadosAlunoDupla[0]->nome
                                     ,'monografia_id'=>$monografia->id]);
            }

            $orientadores = array();
            $orientadores[] = Orientador::find($request->input('orientador_id')); 

            $indOS = 1;
            $campoOrientSec = "orientador_secundario_id_".$indOS;

            while ( !empty($request->input($campoOrientSec)) ) {
                $orientadores[] = Orientador::find($request->input($campoOrientSec));
                $indOS++;
                $campoOrientSec = "orientador_secundario_id_".$indOS;
            } 

            foreach ($orientadores as $key => $orientador) {
                $mono_orientador = new MonoOrientadores;
                $mono_orientador->orientadores_id = $orientador->id;
                $mono_orientador->monografia_id = $monografia->id;
                $mono_orientador->principal = false;
                
                if ($key==0)
                    $mono_orientador->principal = true;

                $mono_orientador->save();
                $mo[] = $mono_orientador;
            }

            if ($request->filled('txtUnitermo1')) {
                $unitermo = Unitermo::create(['unitermo'=>$request->input('txtUnitermo1')]);
                $unitermo->monografia()->save($monografia);
            } else {
                $unitermo = Unitermo::find($request->input('unitermo1'));
                $unitermo->monografia()->save($monografia);
            }

            if ($request->filled('txtUnitermo2')) {
                $unitermo = Unitermo::create(['unitermo'=>$request->input('txtUnitermo2')]);
                $unitermo->monografia()->save($monografia);
            } else {
                $unitermo = Unitermo::find($request->input('unitermo2'));
                $unitermo->monografia()->save($monografia);
            }

            if ($request->filled('txtUnitermo3')) {
                $unitermo = Unitermo::create(['unitermo'=>$request->input('txtUnitermo3')]);
                $unitermo->monografia()->save($monografia);
            } else {
                $unitermo = Unitermo::find($request->input('unitermo3'));
                $unitermo->monografia()->save($monografia);
            }            

            if ($request->filled('unitermo4')) {
                $unitermo = Unitermo::find($request->input('unitermo4'));
                $unitermo->monografia()->save($monografia);
            } elseif ($request->filled('txtUnitermo4')) {
                $unitermo = Unitermo::create(['unitermo'=>$request->input('txtUnitermo4')]);
                $unitermo->monografia()->save($monografia);
            }

            if ($request->filled('unitermo5')) {
                $unitermo = Unitermo::find($request->input('unitermo5'));
                $unitermo->monografia()->save($monografia);
            } elseif ($request->filled('txtUnitermo5')) {
                $unitermo = Unitermo::create(['unitermo'=>$request->input('txtUnitermo5')]);
                $unitermo->monografia()->save($monografia);
            }

            //Envio de e-mail para todos os Orientadores
            $textoOrientador = "Prezad@ [NOMEORIENTADOR]                                                                   
            ";
            $textoOrientador.= "A monografia **$monografia->titulo** foi cadastrada no Sistema de Depósito.                                                                   
            ";
            if (count($orientadores) > 1) {
                $textoOrientador.= "Você foi cadastrado como um dos orientadores desta monografia                                                                   
                ";
                $textoOrientador.= "Segue a lista de Orientadores:                                                                   
                ";
                foreach ($orientadores as $orientador) {
                    $textoOrientador.= $orientador->nome." - ".$orientador->email."                                                                   
                    ";
                }
            } else {
                $textoOrientador.= "Você foi cadastrado como único orientador desta monografia.                                                                   
                ";
            }
            if ($dataAtual < $dadosParam->first()->dataAberturaDocente) {
                $textoOrientador.= "A data de abertura do sistema para avaliação é ".$dadosParam->first()->dataAberturaDocente->format('d/m/Y')."                                                                   
                ";
            }

            foreach ($orientadores as $key=>$orientador) {
                $body = str_replace("[NOMEORIENTADOR]",$orientador->nome,$textoOrientador);
                if ($key == 0 && count($orientadores) > 1) {
                    $body.="
                    ";
                    $body.="**Você é o Orientador Principal.**                                                                   
                    ";
                }
                /*Mail::to("pcalves@usp.br", $orientador->nome)
                    ->send(new NotificacaoOrientador($body,"Nova monografia Cadastrada",$orientador->nome));*/
                Mail::to($orientador->email, $orientador->nome)
                    ->send(new NotificacaoOrientador($body,"Nova monografia Cadastrada",$orientador->nome));
            }

            $mensagem = "Monografia cadastrada";

        } catch (Exception $e) {
            $mensagem = "Erro no cadastro da Monografia. ".$e->message();
        }

        return redirect()->route('alunos.index', 
                                ['monografiaId' => $monografia->id
                                ,'ano' => date('Y')
                                ,'mensagem' => $mensagem
                                ]); //$this->index($monografia->id,date('Y'),$mensagem);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->index($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return $this->index($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $objMonografia = Monografia::find($id);
        $mensagem = null;
        $rules = [];
        
        $rules['titulo']            = ["required","min:3","max:255"];
        $rules['resumo']            = ["required","min:3",new CountWord];
        if (!$request->filled('txtUnitermo1'))
            $rules['unitermo1']     = ["required", "exists:unitermos,id"];
        if (!$request->filled('txtUnitermo2')) 
            $rules['unitermo2']     = ["required", "exists:unitermos,id"];
        if (!$request->filled('txtUnitermo3'))
            $rules['unitermo3']     = ["required", "exists:unitermos,id"];
        //$rules['unitermo4']         = ["exists:unitermos,id"];
        //$rules['unitermo5']         = ["exists:unitermos,id"];
        $rules['template_apres']    = ["file"];
        $rules['cod_area_tematica'] = ["required", "exists:areastematicas,id"];

        $messages['required']                 = "Favor informar o :attribute da monografia.";
        $messages['min']                      = "O :attribute deve conter no mínimo :min caracteres";
        $messages['max']                      = "O :attibute deve conter no máximo :max caracteres";
        $messages['cod_area_tematica.exists'] = "A área temática deve estar previamente cadastrada no sistema";
        $messages['template_apres.file']      = "O arquivo da monografia informado não é válido";

        $request->validate($rules,$messages);

        if ($request->hasFile('template_apres') && $request->file('template_apres')->isValid()) {
            $arquivo = $request->file('template_apres');

            $arquivo->move(public_path('upload'),$arquivo->getClientOriginalName());
            $nomeArq = $arquivo->getClientOriginalName();
        } else {
            $nomeArq = null;
        }
        
        try {
            $objMonografia->titulo = $request->input('titulo');
            $objMonografia->resumo = $request->input('resumo');
            $objMonografia->areastematicas_id = $request->input('cod_area_tematica');
            if (!empty($nomeArq)) {
                if (!empty($objMonografia->template_apres) && $nomeArq != $objMonografia->template_apres) {
                    File::delete('upload/'.$objMonografia->template_apres);
                }    
                $objMonografia->template_apres = $nomeArq;
            }
            $objMonografia->publicar = ($request->has('publicar'))?$request->input('publicar'):null;
            
            $objMonografia->update();

            if (!$request->filled('txtUnitermo1')) {
                $unitermo1 = Unitermo::find($request->input('unitermo1'));
            } else {
                $unitermo1 = Unitermo::create(['unitermo' => $request->input('txtUnitermo1')]);
            }

            if (!$request->filled('txtUnitermo2')) {
                $unitermo2 = Unitermo::find($request->input('unitermo2'));
            } else {
                $unitermo2 = Unitermo::create(['unitermo' => $request->input('txtUnitermo2')]);
            }

            if (!$request->filled('txtUnitermo3')) {
                $unitermo3 = Unitermo::find($request->input('unitermo3'));
            } else {
                $unitermo3 = Unitermo::create(['unitermo' => $request->input('txtUnitermo3')]);
            }

            if (!$request->filled('txtUnitermo4')) {
                if ($request->filled('unitermo4')) {
                    $unitermo4 = Unitermo::find($request->input('unitermo4'));
                }
            } else {
                $unitermo4 = Unitermo::create(['unitermo' => $request->input('txtUnitermo4')]);
            }

            if (!$request->filled('txtUnitermo5')) {
                if ($request->filled('unitermo5')) {
                    $unitermo5 = Unitermo::find($request->input('unitermo5'));
                }
            } else {
                $unitermo5 = Unitermo::create(['unitermo' => $request->input('txtUnitermo5')]);
            }
            
            $noDelete  = [0];
            $unitermos = array();

            if (empty($unitermo1->id)) {
                $mensagem.= "O Descritor 1 não foi alterado pois o selecionado foi excluído das opções do sistema.";
                $noDelete[] = $request->input('unitermo1');
            } else {
                $unitermos[] = $unitermo1;
            }
            if (empty($unitermo2->id)) {
                $mensagem.= "O Descritor 2 não foi alterado pois o selecionado foi excluído das opções do sistema.";
                $noDelete[] = $request->input('unitermo2');
            } else {
                $unitermos[] = $unitermo2;
            }
            if (empty($unitermo3->id)) {
                $mensagem.= "O Descritor 3 não foi alterado pois o selecionado foi excluído das opções do sistema.";
                $noDelete[] = $request->input('unitermo3');
            } else {
                $unitermos[] = $unitermo3;
            }
            if ($request->filled('unitermo4') && empty($unitermo4->id)) {
                $mensagem.= "O Descritor 4 não foi alterado pois o selecionado foi excluído das opções do sistema.";
                $noDelete[] = $request->input('unitermo4');
            } elseif (!empty($unitermo4->id)) {
                $unitermos[] = $unitermo4;
            }
            if ($request->filled('unitermo5') && empty($unitermo5->id)) {
                $mensagem.= "O Descritor 5 não foi alterado pois o selecionado foi excluído das opções do sistema.";
                $noDelete[] = $request->input('unitermo5');
            } elseif (!empty($unitermo5->id)) {
                $unitermos[] = $unitermo5;
            }

            $numExcluidos = MonoUnitermos::excluirRegistroByMonografia($id, $noDelete);
            foreach ($unitermos as $unitermo) {
                $unitermo->monografia()->save($objMonografia);
            }

            $dadosAvaliacao = Avaliacao::where('status','DEVOLVIDO')->where('id',$request->input('av_id'))->get();
            $envioEmailOrientador = false;
            if (isset($dadosAvaliacao->first()->status) && auth()->user()->hasRole('aluno')) {
                $dadosAvaliacao = $dadosAvaliacao->first();
                $dadosAvaliacao->status = "CORRIGIDO";
                $dadosAvaliacao->update();
                $envioEmailOrientador = true;
            }

            $assuntoMsg = null;
            if (auth()->user()->hasRole('aluno') && $envioEmailOrientador) {
                //enviar e-mail para o orientador caso exista alguma avaliação
                $mensagem = "Monografia corrigida e enviada para aprovação do Orientador";
                
                $textoMensagem = "A monografia título **".$objMonografia->titulo."** foi corrigida.           
                ";
                $textoMensagem.= "Aluno que cadastrou a correção: **".auth()->user()->name. "**                                        
                ";
                $textoMensagem.= "Correção que foi solicitada: *".$dadosAvaliacao->parecer."*                                                                                            
                ";
                $textoMensagem.= "Entre no sistema para re-avaliar a monografia.                               
                ";
                $assuntoMsg = "Monografia com título ".$objMonografia->titulo." corrigida";

                foreach($objMonografia->orientadores()->get() as $orientadores) {
                    if ($orientadores->pivot->principal) {
                        $txt.= "**Você é o Orientador Principal**.                               
                        ";
                    } else {
                        $txt = null;
                    }
                    $textoMensagem.= $txt;
                    /*Mail::to("pcalves@usp.br", $orientadores->nome)
                        ->send(new NotificacaoOrientador($textoMensagem, $assuntoMsg, $orientadores->nome));*/
                    Mail::to($objOrientador->email, $orientadores->nome)
                        ->send(new NotificacaoOrientador($textoMensagem, $assuntoMsg, $orientadores->nome));
                }
                
            } else {
                $mensagem = "Monografia corrigida";
            }
            
        } catch (Exception $e) {
            $mensagem = "Erro ao Corrigir a Monografia. ".$e->message;
        }     
        
        print "<script>alert('".$mensagem."'); </script>";

        if (auth()->user()->hasRole('aluno')) {
            return redirect(route('alunos.index'));
        } else
            return redirect(route('orientador.edicao',['idMono'=>$id]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->hasRole('graduacao') && !auth()->user()->can('admin')) {
            return "<script> alert('M8-Você não tem acesso a esta parte do Sistema.'); 
                                window.location.assign('".route('home')."');
                    </script>";
        }

        $monografia = Monografia::find($id);
        if ($monografia->status == "CONCLUIDO") {
            return "<script> alert('M9-Monografias concluídas não podem ser apagadas do sistema.'); 
                                window.location.assign('".route('orientador.lista_monografia')."');
                    </script>";
        }
        if (Avaliacao::where('monografia_id',$id)->count() > 0) {
            return "<script> alert('M10-Existem pareceres para esta monografia, não é possível excluir do sistema.'); 
                                window.location.assign('".route('orientador.lista_monografia')."');
                    </script>";
        }

        $delAlunos = Aluno::excluirRegistroByMonografiaId($id);
        $delUnitermos = Unitermo::excluirRegistroByMonografia($id, [0]);
        $delOrientador = MonoOrientadores::excluirRegistroByMonografia($id);
        $monografia->delete();

        print "<script>alert('Monografia Excluída do Sistema.');</script>";

        return redirect()->route('orientador.lista_monografia');
    }

    /**
     * Lista todas as monografias cadastradas conforme Orientador, 
     * ou caso passado como 0, todas as monografias
     */
    public function listMonografia(int $id_orientador = 0, $ano = null, $filtro=null) {

        if (!auth()->user()->hasRole('orientador') && !auth()->user()->hasRole('graduacao') && !auth()->user()->can('admin')) {
            return "<script> alert('M7-Você não tem acesso a esta parte do Sistema.'); 
                                window.location.assign('".route('home')."');
                    </script>";
        }
        $dadosParam = Parametro::where("ano",date("Y"))->whereNull('codpes')->get();
        $sistema_aberto = null;
        if ($dadosParam->isEmpty()) {
            if (!auth()->user()->can('admin') && !auth()->user()->hasRole('graduacao')) {
                return "<script> alert('M30-O Sistema não foi aberto para edições. Aguarde comunicado da Graduação.'); 
                                window.location.assign('".route('home')."');
                        </script>";
            }
        } elseif (auth()->user()->hasRole('orientador')) {
            $dadosParam->first()->dataAberturaDocente = date_create($dadosParam->first()->dataAberturaDocente);
            $dadosParam->first()->dataFechamentoDocente = date_create($dadosParam->first()->dataFechamentoDocente);
            $dataAtual = date_create('now');
    
            if ($dataAtual <= $dadosParam->first()->dataAberturaDocente ) {
                $sistema_aberto = "Aguarde abertura do Sistema em ".$dadosParam->first()->dataAberturaDocente->format('d/m/Y'); 
            }

            if ($dataAtual > $dadosParam->first()->dataFechamentoDocente ) {
                $sistema_aberto = "Sistema fechado em ".$dadosParam->first()->dataFechamentoDocente->format('d/m/Y'); 
            } 
        } 
        $userLogado = null;
        $userLogado = (auth()->user()->hasRole('orientador'))?"Orientador":$userLogado;
        $userLogado = (auth()->user()->hasRole('graduacao'))?"Graduacao":$userLogado;
        $userLogado = (auth()->user()->can('admin'))?"Admin":$userLogado;
        
        $dadosMonografias = array();
        $grupoAlunos = array();
        $dadosOrientadores = array();
       

        if ($id_orientador > 0 || auth()->user()->hasRole('orientador') && !auth()->user()->can('admin')) {
            
            if ($id_orientador == 0) {
                $Orientador = Orientador::where('email', auth()->user()->email)->get();
                if ($Orientador->isEmpty()) {
                    return "<script> alert('M40-Você não tem permissão para acessar essa área do sistema.'); 
                                window.location.assign('".route('home')."');
                        </script>";
                }
                $id_orientador = $Orientador->first()->id;
            } 
            if (empty($filtro)) {
                $Monografias = Monografia::with(['alunos','orientadores'])
                                         ->whereRelation('orientadores', 'orientadores_id', $id_orientador)
                                         ->orderBy('ano','desc')
                                         ->paginate(30);
            } else {
                $Monografias = Monografia::with(['alunos','orientadores'])
                                         ->whereRelation('orientadores', 'orientadores_id', $id_orientador)
                                         ->whereRelation('alunos','nome','like',"%$filtro%")
                                         ->whereOr('titulo','like',"%$filtro%")
                                         ->whereOr('ano','like',"%$filtro%")
                                         ->orderBy('ano','desc')
                                         ->get();
            }
        } else {
            if (empty($filtro)) {
                $Monografias = Monografia::with(['alunos','orientadores'])->orderBy('ano','desc')->paginate(30);
            } else {
                $ObjMonografias = new Monografia;
                $Monografias = $ObjMonografias->getMonografiaByFiltro($filtro);

                /*$Monografias = Monografia::with(['alunos','orientadores'])
                                         #->whereRelation('alunos','nome','like',"%$filtro%")
                                         ->whereOr('titulo','like',"%$filtro%")
                                         ->whereOr('ano','like',"%$filtro%")
                                         ->orderBy('ano','desc')
                                         ->paginate(30);*/
            }
           
        }
        $dadosMonografias = $Monografias;

        foreach ($dadosMonografias as $dadosM) {
           $collectionM = Monografia::with(['alunos','orientadores'])->where('id', $dadosM->id)->get();
           $grupoAlunos[$dadosM->id] = $collectionM->first()->alunos()->get();
           $dadosOrientadores[$dadosM->id] = $collectionM->first()->orientadores()->get();
           $idMono = $dadosM->id;
        }
        
        $parametros = ["dadosMonografias" => $dadosMonografias
                      ,"grupoAlunos"      => $grupoAlunos
                      ,"dadosOrientadores"=> $dadosOrientadores
                      ,"sistema_aberto"   => $sistema_aberto
                      ,"userLogado"       => $userLogado
                      ,"id_orientador"    => $id_orientador
                      ,"filtro"           => $filtro
                      ];
        
        return view('listMonografia',$parametros);
    }

    /**
     * Busca as monografias baseado no filtro informado
     * @param  \Illuminate\Http\Request  $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function buscaRegistroMonografia(Request $request) {

        return $this->listMonografia($request->input('id_orientador'), null, $request->input('filtro'));

        //return view('cadastro-banca', ['listBanca'=>$listBanca, 'buscaRegistro' => 1, 'filtro' => $request->input('filtro')]);
    }

}
