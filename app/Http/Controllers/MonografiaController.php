<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

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
use App\Models\Comissao;
use App\Models\Defesa;
use App\Models\Banca;

use Uspdev\Replicado\Pessoa;

use App\Rules\CountWord500;
use App\Rules\CountWord250;
use App\Rules\VerificaDatas;

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
        $dadosParam = Parametro::getDadosParam();
        if ($dadosParam === false && (auth()->user()->hasRole('orientador') || auth()->user()->hasRole('aluno') || auth()->user()->hasRole('avaliador'))) {
            return "<script> alert('M17-Sistema não parametrizado. Entre em contato com a Graduação'); 
                                        window.location.assign('".route('home')."');
                        </script>";
        }

        $dataAtual = date_create('now');
        $uploadTcc = false;
        $readonly = true;
        $publicar = false;
        $userLogado = null;
        $msgAbertura = null;
        $avaliar = false;
        $aprovOrientador = false;
        $edicao = false;
        $validacaoTcc = false;
        
        if (auth()->user()->hasRole('orientador') && !auth()->user()->can('admin')) {
            $userLogado = "Orientador";

            if ($dataAtual >= $dadosParam->dataAberturaDocente && 
                $dataAtual <= $dadosParam->dataFechamentoDocente ) {
                $userLogado = "Orientador";
                $aprovOrientador = true;
                $readonly = false;
            } 

        } elseif (auth()->user()->hasRole('avaliador')) {
            $userLogado = "Avaliador";

            if ($dataAtual >= $dadosParam->dataAberturaAvaliacao && 
                $dataAtual <= $dadosParam->dataFechamentoAvaliacao ) {
             
                $avaliar = true;
            }
            
        } elseif (auth()->user()->hasRole('aluno')) {
            $userLogado = "Aluno";
            if ($dataAtual >= $dadosParam->dataAberturaUploadTCC && $dataAtual <= $dadosParam->dataFechamentoUploadTCC) {
                $uploadTcc = true;
            }

            if ($dataAtual >= $dadosParam->dataAberturaDiscente || 
                $dataAtual <= $dadosParam->dataFechamentoDiscente) {
                $readonly = false;
            }
            
        } elseif (auth()->user()->hasRole('graduacao')) {
            $userLogado = "Graduacao";
            $publicar = true;
            $readonly = false;
            $edicao = true;
        } elseif (auth()->user()->can('admin')) {
            $userLogado = "Admin";
            $publicar = true;
            $readonly = false;
        }

        $numUSPAluno = 0;
        $nomeAluno = null;
        $dadosMonografia = null;
        $dadosAlunoGrupo = null;
        $indicarParecerista = false;
        $dadosMonografia = [];
        $dadosParecerista = [];
        $idParecerista = 0;
        $correcaoSolicitada = null;
        $dadosBanca = null;
        $dadosUnitermos = null;

        $dadosDefesa = Defesa::where("monografia_id",0)->get();
        
        if ($monografia_id > 0) {
            if (empty($ano)) {
                $dadosMonografia = Monografia::with(['alunos','orientadores','unitermos'])->orderBy('ano')->where('id',$monografia_id)->get();
            } else {
                $dadosMonografia = Monografia::with(['alunos','orientadores','unitermos'])->orderBy('ano')->where('id',$monografia_id)->where('ano',$ano)->get();
            }

            if ($dadosMonografia->isEmpty()) {
                return "<script> alert('M7-Erro ao informar monografia. Entre em contato com a Graduação'); 
                                 window.location.assign('".route('home')."');
                        </script>"; 
            }

            $dadosAlunoGrupo = Aluno::where('monografia_id',$monografia_id)->get();
            $dadosDefesa = Defesa::where("monografia_id",$monografia_id)->get();
            $dadosUnitermos = $dadosMonografia->first()->unitermos;

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

                if (MonoOrientadores::where('monografia_id',$monografia_id)->where('status','APROVADO')->count() > 0) {
                    if ($dadosMonografia->first()->status <> "AGUARDANDO CORRECAO DO PROJETO") {
                        $readonly = true;
                        $edicao = true;
                    } else {
                        $uploadTcc = false;
                        $edicao = false;
                    }
                }

            } elseif (auth()->user()->hasRole('orientador') && !auth()->user()->can('admin')) {
                $ddOrientador = Orientador::where('email',auth()->user()->email)->get();
                $monoOrientador = MonoOrientadores::where('orientadores_id',$ddOrientador->first()->id)->where('monografia_id',$dadosMonografia->first()->id)->get();
                
                if ($monoOrientador->isEmpty()) {
                    return "<script> alert('M5-Monografia não está sob sua orientação. Entre em contato com a Graduação'); 
                                     window.location.assign('".route('home')."');
                            </script>"; 

                } elseif ($monoOrientador->first()->status == "APROVADO") {
                    $aprovOrientador = false;
                    $readonly = true;
                    $edicao = false;
                }
                $numUSPAluno = $dadosAlunoGrupo->first()->id;
                $nomeAluno = $dadosAlunoGrupo->first()->nome;

                $dadosAlunoGrupo = Aluno::where('monografia_id',$dadosMonografia[0]->id)->whereNotIn('id',[$dadosAlunoGrupo->first()->id])->get();
            } elseif (auth()->user()->hasRole('graduacao') || auth()->user()->can('admin')) {
                if ($dadosMonografia->first()->status == "AGUARDANDO AVALIACAO" &&
                    Avaliacao::where('monografia_id',$dadosMonografia->first()->id)->count() == 0) {
                    $indicarParecerista = true;
                    $edicao = false;
                    $dadosParecerista = Comissao::all();
                }
            }
        } elseif (auth()->user()->hasRole('aluno')) {
            if (Aluno::where('id',auth()->user()->codpes)->count() == 0) {
                $dadosReplicado = Aluno::getDadosAluno(auth()->user()->codpes);
                if (empty($dadosReplicado)) {
                    return "<script> alert('M25-Aluno sem vínculo com a FCF'); 
                                        window.location.assign('".route('home')."');
                            </script>"; 
                }
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
                if (MonoOrientadores::where('monografia_id',$mono_id)->where('status','APROVADO')->count() > 0) {
                    if ($dadosMonografia->first()->status <> "AGUARDANDO CORRECAO DO PROJETO") {
                        $readonly = true;
                        $edicao = true;
                    } else {
                        $uploadTcc = false;
                        $edicao = false;
                    }
                }
                $dadosUnitermos = $dadosMonografia->first()->unitermos;
                $dadosDefesa = Defesa::where("monografia_id",$mono_id)->get();
                $dadosAlunoGrupo = Aluno::where('monografia_id',$dadosMonografia->first()->id)->whereNotIn('id',[auth()->user()->codpes])->get();
            
            } else {
                $dadosAlunoGrupo = Aluno::where('id',[auth()->user()->codpes])->get();
                $uploadTcc = false;
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
        $dadosAvaliacoes = null;
        
        if (!is_array($dadosMonografia) && !$dadosMonografia->isEmpty()) {
            $dadosMonografia = $dadosMonografia->first();
            
            foreach ($dadosMonografia->orientadores as $listOrient) {
                if (!$listOrient->pivot->principal) {
                    if ($listOrient->externo)
                        $orientadorSecundario[] = "CPF: ".$listOrient->CPF." Nome: ".$listOrient->nome;
                    else
                        $orientadorSecundario[] = "Número USP :".$listOrient->codpes." Nome: ".$listOrient->nome;
                } else {
                    $orientadorId = $listOrient->id;
                }
            }
            
            $maxDtAvaliacao = Avaliacao::where("monografia_id",$dadosMonografia->id)
                                       ->get()
                                       ->max("dataAvaliacao");

            $dadosAvaliacoes = Avaliacao::where("monografia_id",$dadosMonografia->id)
                                        ->where("dataAvaliacao",$maxDtAvaliacao)
                                        ->get();
            
            $dadosDefesa = Defesa::where("monografia_id",$dadosMonografia->id)->get();
            $dadosBanca = Banca::where("monografia_id",$dadosMonografia->id)->orderBy('ordem')->get();
            $dadosOrientadores = MonoOrientadores::with('orientadores')->where("monografia_id",$dadosMonografia->id)->get();
            
            if (!$dadosAvaliacoes->isEmpty()) {
                if (auth()->user()->hasRole('avaliador')) {
                    if ($dadosAvaliacoes->first()->status == "CORRIGIDO" || 
                        $dadosAvaliacoes->first()->status == "AGUARDANDO") {
                    
                        $avaliar = true;
                    } else {
                        $avaliar = false;
                    }
                    /*$maxDtAvaliacao = Avaliacao::where("monografia_id",$dadosMonografia->id)
                                               ->whereIn("status",["CORRIGIDO","AGUARDANDO"])
                                               ->whereRelation("comissoes","codpes",auth()->user()->codpes)
                                               ->get()
                                               ->max("dataAvaliacao");

                    $dadosAvaliacoes = Avaliacao::where("monografia_id",$dadosMonografia->id)
                                                ->whereIn("status",["CORRIGIDO","AGUARDANDO"])
                                                ->where("dataAvaliacao",$maxDtAvaliacao)
                                                ->whereRelation("comissoes","codpes",auth()->user()->codpes)
                                                ->get();*/
                    
                    
                } elseif (auth()->user()->hasRole("aluno") && $dadosAvaliacoes->first()->status == "DEVOLVIDO") {
                    $readonly = false;
                    $edicao = true;
                }
            } else {
                $dadosAvaliacoes = Avaliacao::where("monografia_id",$dadosMonografia->id)
                                            ->where("status","APROVADO")
                                            ->get();
            }
            
            if (!$dadosAvaliacoes->isEmpty()) {
                $avParecerista = Comissao::find($dadosAvaliacoes->first()->comissoes_id);
                $dadosAvaliacoes->first()->dataAvaliacao = date_create($dadosAvaliacoes->first()->dataAvaliacao);
                $dadosAvaliacoes->_parecerista = $avParecerista->codpes." ".$avParecerista->nome;
                $idParecerista = $avParecerista->id; 
            }

            if (auth()->user()->hasRole("graduacao")) {
                $validacaoTcc = ($dadosMonografia->status == "AGUARDANDO VALIDACAO DA BANCA")?true:false;
                $readonly = ($dadosMonografia->status == "CONCLUIDO")?true:false;
            }
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
                      ,"uploadTcc"          => $uploadTcc
                      ,"aprovOrientador"    => $aprovOrientador
                      ,"indicarParecerista" => $indicarParecerista
                      ,"dadosParecerista"   => $dadosParecerista
                      ,"idParecerista"      => $idParecerista
                      ,"correcaoSolicitada" => $correcaoSolicitada
                      ,"dadosDefesa"        => $dadosDefesa
                      ,"dadosBanca"         => $dadosBanca
                      ,"validacaoTcc"       => $validacaoTcc
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
        /*if ($request->input('dupla')) {
            $rules['pessoaDupla'] = "required";
            $messages['pessoaDupla.required'] = "Favor informar o componente do grupo de trabalho.";
        }*/

        $rules['orientador_id']     = "required";
        $rules['titulo']            = ["required","min:3","max:255"];
        $rules['resumo']            = ["required","min:3","max:255"];
        $rules['introducao']        = ["required","min:3",new CountWord500];
        $rules['objetivo']          = ["required","min:3",new CountWord250];
        $rules['material_metodo']   = ["required","min:3",new CountWord500];
        $rules['resultado_esperado']= ["required","min:3",new CountWord500];
        $rules['aspecto_etico']     = ["required","min:3",new CountWord250];
        if (!$request->filled('txtUnitermo1'))
            $rules['unitermo1']     = ["required", "exists:unitermos,id"];
        if (!$request->filled('txtUnitermo2')) 
            $rules['unitermo2']     = ["required", "exists:unitermos,id"];
        if (!$request->filled('txtUnitermo3'))
            $rules['unitermo3']     = ["required", "exists:unitermos,id"];
        //$rules['unitermo4']         = ["exists:unitermos,id"];
        //$rules['unitermo5']         = ["exists:unitermos,id"];
        //$rules['template_apres']   = ["file","required"];
        $rules['cod_area_tematica'] = ["required", "exists:areastematicas,id"];
        
        $messages['required']                 = "Favor informar o :attribute da monografia.";
        $messages['min']                      = "O :attribute deve conter no mínimo :min caracteres";
        $messages['max']                      = "O :attribute deve conter no máximo :max caracteres";
        $messages['cod_area_tematica.exists'] = "A área temática deve estar previamente cadastrada no sistema";
        $messages['exists']                   = "O :attribute deve estar previamente cadastrado no sistema.";
        //$messages['template_apres.file']      = "O arquivo da monografia não é válido";
        //$messages['template_apres.required']  = "O arquivo da monografia deve ser informado.";

        $request->validate($rules,$messages);
        
        /*$arquivo = $request->file('template_apres');
        $arquivo->move(public_path('upload'),$arquivo->getClientOriginalName());

        $dadosParam = Parametro::where("ano",date("Y"))->whereNull('codpes')->get();
        if (!$dadosParam->isEmpty()) {
            $dadosParam->dataAberturaDocente = date_create($dadosParam->dataAberturaDocente);
            $dataAtual = date_create('now');
        }*/
        
        $mensagem = null;

        try {

            $dadosParam = Parametro::getDadosParam();

            $monografia = new Monografia();
            $monografia->status            = 'AGUARDANDO APROVACAO ORIENTADOR';
            $monografia->titulo            = $request->input('titulo');
            $monografia->resumo            = $request->input('resumo');
            $monografia->introducao        = $request->input('introducao');
            $monografia->objetivo          = $request->input('objetivo');
            $monografia->material_metodo   = $request->input('material_metodo');
            $monografia->resultado_esperado= $request->input('resultado_esperado');
            $monografia->aspecto_etico     = $request->input('aspecto_etico');
            $monografia->referencias       = $request->input('referencias');
            //$monografia->template_apres    = $arquivo->getClientOriginalName();
            $monografia->areastematicas_id = $request->input('cod_area_tematica');
            $monografia->ano = date('Y');

            //rever parâmetros, devem ser por semestre
            $dataAtual = date_create('now');
            if ($dataAtual->format('n') <= 6 ) {
                $monografia->semestre = '1';
            } else {
                $monografia->semestre = '2';
            }

            $monografia->save();

            $aluno = new Aluno;
            $aluno->id = auth()->user()->codpes;
            $aluno->nome = auth()->user()->name;
            $aluno->monografia_id = $monografia->id;

            $aluno->firstOrCreate(['id'=>auth()->user()->codpes]
                                 ,['id'=>auth()->user()->codpes
                                  ,'nome'=>auth()->user()->name
                                  ,'monografia_id'=>$monografia->id]);

            /*
            //Essa parte somente precisa se for implantar trabalho em dupla
            if (!empty($request->input('dupla')) && $request->input('dupla') == 1) {
                $dadosAlunoDupla = Aluno::getDadosAluno($request->input('pessoaDupla'));

                $aluno->firstOrCreate(['id'=>$request->input('pessoaDupla')]
                                     ,['id'=>$request->input('pessoaDupla')
                                     ,'nome'=>$dadosAlunoDupla[0]->nome
                                     ,'monografia_id'=>$monografia->id]);
            }*/

            $orientadores = array();
            $orientadores[] = Orientador::find($request->input('orientador_id')); 

            /*$indOS = 1;
            //Essa parte somente é necessária se for preciso mais de um orientador
            $campoOrientSec = "orientador_secundario_id_".$indOS;

            while ( !empty($request->input($campoOrientSec)) ) {
                $orientadores[] = Orientador::find($request->input($campoOrientSec));
                $indOS++;
                $campoOrientSec = "orientador_secundario_id_".$indOS;
            } */

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
                $unitermo1 = Unitermo::create(['unitermo'=>$request->input('txtUnitermo1')]);
            } else {
                $unitermo1 = Unitermo::find($request->input('unitermo1'));
            }

            if ($request->filled('txtUnitermo2')) {
                $unitermo2 = Unitermo::create(['unitermo'=>$request->input('txtUnitermo2')]);
            } else {
                $unitermo2 = Unitermo::find($request->input('unitermo2'));
            }

            if ($request->filled('txtUnitermo3')) {
                $unitermo3 = Unitermo::create(['unitermo'=>$request->input('txtUnitermo3')]);
            } else {
                $unitermo3 = Unitermo::find($request->input('unitermo3'));
            }            

            if ($request->filled('unitermo4')) {
                $unitermo4 = Unitermo::find($request->input('unitermo4'));
            } elseif ($request->filled('txtUnitermo4')) {
                $unitermo4 = Unitermo::create(['unitermo'=>$request->input('txtUnitermo4')]);
            }

            if ($request->filled('unitermo5')) {
                $unitermo5 = Unitermo::find($request->input('unitermo5'));
            } elseif ($request->filled('txtUnitermo5')) {
                $unitermo5 = Unitermo::create(['unitermo'=>$request->input('txtUnitermo5')]);
            }

            if ($unitermo1->unitermo <> $unitermo2->unitermo &&
                $unitermo1->unitermo <> $unitermo3->unitermo  
            ) {
                if((isset($unitermo4->unitermo) && $unitermo1->unitermo <> $unitermo4->unitermo) ||
                   (isset($unitermo5->unitermo) && $unitermo1->unitermo <> $unitermo5->unitermo) ||
                   (!isset($unitermo4->unitermo) && !isset($unitermo5->unitermo))) {
                    $unitermo1->monografia()->save($monografia);
                } else {
                    return Redirect::back()->withErrors(['unitermo1'=>'O campo Palavra-chave 1 não pode ser repetido']);
                } 
            } else {
                return Redirect::back()->withErrors(['unitermo1'=>'O campo Palavra-chave 1 não pode ser repetido']);
            }
            if ($unitermo2->unitermo <> $unitermo1->unitermo &&
                $unitermo2->unitermo <> $unitermo3->unitermo  
            ) {
                if((isset($unitermo4->unitermo) && $unitermo2->unitermo <> $unitermo4->unitermo) ||
                   (isset($unitermo5->unitermo) && $unitermo2->unitermo <> $unitermo5->unitermo) ||
                   (!isset($unitermo4->unitermo) && !isset($unitermo5->unitermo))) {
                    $unitermo2->monografia()->save($monografia);
                } else {
                    return Redirect::back()->withErrors(['unitermo2'=>'O campo Palavra-chave 2 não pode ser repetido']);
                }
                 
            } else {
                return Redirect::back()->withErrors(['unitermo2'=>'O campo Palavra-chave 2 não pode ser repetido']);
            }
            if ($unitermo3->unitermo <> $unitermo1->unitermo &&
                $unitermo3->unitermo <> $unitermo2->unitermo  
            ) {
                if((isset($unitermo4->unitermo) && $unitermo3->unitermo <> $unitermo4->unitermo) ||
                   (isset($unitermo5->unitermo) && $unitermo3->unitermo <> $unitermo5->unitermo) ||
                   (!isset($unitermo4->unitermo) && !isset($unitermo5->unitermo))) {
                    $unitermo3->monografia()->save($monografia);
                } else {
                    return Redirect::back()->withErrors(['unitermo3'=>'O campo Palavra-chave 3 não pode ser repetido']);
                }
            } else {
                return Redirect::back()->withErrors(['unitermo3'=>'O campo Palavra-chave 3 não pode ser repetido']);
            }
            if (isset($unitermo4->unitermo) &&
                $unitermo4->unitermo <> $unitermo1->unitermo &&
                $unitermo4->unitermo <> $unitermo2->unitermo &&
                $unitermo4->unitermo <> $unitermo3->unitermo) {

                if((isset($unitermo5->unitermo) && $unitermo4->unitermo <> $unitermo5->unitermo) ||
                   (!isset($unitermo5->unitermo))) {
                    $unitermo4->monografia()->save($monografia);
                }
            }
            if (isset($unitermo5->unitermo) &&
                $unitermo5->unitermo <> $unitermo1->unitermo &&
                $unitermo5->unitermo <> $unitermo2->unitermo &&
                $unitermo5->unitermo <> $unitermo3->unitermo) {

                if((isset($unitermo4->unitermo) && $unitermo5->unitermo <> $unitermo4->unitermo) ||
                   (!isset($unitermo4->unitermo))) {
                    $unitermo5->monografia()->save($monografia);
                }
            }

            //Envio de e-mail para todos os Orientadores
            $textoOrientador = "Prezad@ [NOMEORIENTADOR]                                                                          
            ";
            $textoOrientador.= "Você recebeu em sua área de ORIENTADOR o projeto abaixo                                           
            ";
            $textoOrientador.= "Aluno: ".$aluno->nome."                                                                           
            ";
            $textoOrientador.= "Titulo do Projeto: ".$monografia->titulo."                                                        
            ";
            $textoOrientador.= "Estando de acordo, faça o envio para a CTCC - FCF.                                                
            ";
            $textoOrientador.= "Caso seja necessária alguma alteração, acesse o campo, altere, salve e envie para a CTCC.         
            ";

            /*if (count($orientadores) > 1) {
                $textoOrientador.= "Segue a lista de Orientadores:                                                                   
                ";
                foreach ($orientadores as $orientador) {
                    $textoOrientador.= $orientador->nome." - ".$orientador->email."                                                                   
                    ";
                }
            } */
            if ($dataAtual < $dadosParam->dataAberturaDocente) {
                $textoOrientador.= "A data de abertura do sistema para aprovação é ".$dadosParam->dataAberturaDocente->format('d/m/Y')."                                                                   
                ";
            }

            foreach ($orientadores as $key=>$orientador) {
                $body = str_replace("[NOMEORIENTADOR]",$orientador->nome,$textoOrientador);
                /*if ($key == 0 && count($orientadores) > 1) {
                    $body.="
                    ";
                    $body.="**Você é o Orientador Principal.**                                                                   
                    ";
                }*/
                Mail::to("pcalves@usp.br", $orientador->nome)
                    ->send(new NotificacaoOrientador($body,"[".config('app.url')." - ORIENTADOR] Novo Projeto Cadastrado",$orientador->nome));
                /*Mail::to($orientador->email, $orientador->nome)
                    ->send(new NotificacaoOrientador($body,"[".config('app.url')." - ORIENTADOR] Novo Projeto Cadastrado",$orientador->nome));*/
            }

            $txtMsgAluno = "Você está recebendo a confirmação de inclusão do Projeto de Conclusão de Curso, realizado
            no sistema em ".$dataAtual->format('d/m/Y H:i:s')." e enviado para o seu orientador.
            ";

            $emailAluno = Pessoa::email($aluno->id);

            Mail::to("pcalves@usp.br", $aluno->nome)
                    ->send(new NotificacaoAluno($txtMsgAluno,"[".config('app.url')." - ALUNO] Novo Projeto Cadastrado",$aluno->nome));
            /*Mail::to($emailAluno, $aluno->nome)
                    ->send(new NotificacaoAluno($txtMsgAluno,"[".config('app.url')." - ALUNO] Novo Projeto Cadastrado",$aluno->nome));*/

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
    public function show($id, $msg = null)
    {
        if (!empty($msg)) {
            print "<script> alert('$msg'); </script>";
        }
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
        /*if ($request->input('dupla')) {
            $rules['pessoaDupla'] = "required";
            $messages['pessoaDupla.required'] = "Favor informar o componente do grupo de trabalho.";
        }*/
        $dadosParam = Parametro::getDadosParam();
        $dataAtual = date_create('now');

        $rules['orientador_id']     = "required";
        $rules['titulo']            = ["required","min:3","max:255"];
        $rules['resumo']            = ["required","min:3","max:255"];
        $rules['introducao']        = ["required","min:3",new CountWord500];
        $rules['objetivo']          = ["required","min:3",new CountWord250];
        $rules['material_metodo']   = ["required","min:3",new CountWord500];
        $rules['resultado_esperado']= ["required","min:3",new CountWord500];
        $rules['aspecto_etico']     = ["required","min:3",new CountWord250];
        if (!$request->filled('txtUnitermo1'))
            $rules['unitermo1']     = ["required", "exists:unitermos,id"];
        if (!$request->filled('txtUnitermo2')) 
            $rules['unitermo2']     = ["required", "exists:unitermos,id"];
        if (!$request->filled('txtUnitermo3'))
            $rules['unitermo3']     = ["required", "exists:unitermos,id"];
        //$rules['unitermo4']         = ["exists:unitermos,id"];
        //$rules['unitermo5']         = ["exists:unitermos,id"];
        if ($dataAtual >= $dadosParam->dataAberturaUploadTCC && 
            $dataAtual <= $dadosParam->dataFechamentoUploadTCC &&
            empty($objMonografia->path_arq_tcc) &&
            $objMonografia->status <> "AGUARDANDO CORRECAO DO PROJETO" &&
            auth()->user()->hasRole('aluno')) {

            $rules['path_arq_tcc']  = ["file","required","mimes:application/pdf"];         
        }
        
        $rules['cod_area_tematica'] = ["required", "exists:areastematicas,id"];

        $messages['required']                 = "Favor informar o :attribute da monografia.";
        $messages['min']                      = "O :attribute deve conter no mínimo :min caracteres";
        $messages['max']                      = "O :attibute deve conter no máximo :max caracteres";
        $messages['cod_area_tematica.exists'] = "A área temática deve estar previamente cadastrada no sistema";
        $messages['path_arq_tcc.file']        = "O arquivo da monografia informado não é válido";
        $messages['path_arq_tcc.mimes']       = "O arquivo deve ser do tipo PDF";

        $request->validate($rules,$messages);

        if (isset($rules['path_arq_tcc']) && $request->hasFile('path_arq_tcc') && $request->file('path_arq_tcc')->isValid()) {
            $arquivo = $request->file('path_arq_tcc');

            if (!$arquivo->move(public_path('upload'),$arquivo->getClientOriginalName())) {
                return "<script> alert('M26-Erro ao copiar o arquivo.'); 
                                       window.location.assign('".route('home')."');
                    </script>";
            }
            $nomeArq = $arquivo->getClientOriginalName();
        } else {
            if (isset($rules['path_arq_tcc'])) {
                return "<script> alert('M16-Erro de upload de arquivo'); 
                                       window.location.assign('".route('home')."');
                    </script>";
            }
            $nomeArq = null;
        }
        
        try {
            $objMonografia->titulo = $request->input('titulo');
            $objMonografia->resumo = $request->input('resumo');
            $objMonografia->areastematicas_id = $request->input('cod_area_tematica');
            if (!empty($nomeArq)) {
                if (!empty($objMonografia->path_arq_tcc) && $nomeArq != $objMonografia->path_arq_tcc) {
                    File::delete('upload/'.$objMonografia->path_arq_tcc);
                }    
                $objMonografia->path_arq_tcc = $nomeArq;
            }
            $objMonografia->publicar = ($request->has('publicar'))?$request->input('publicar'):null;
            
            if (isset($rules['path_arq_tcc']))
                $objMonografia->status = "AGUARDANDO VALIDACAO DE BANCA";

            $objMonografia->update();

            if ($request->filled('txtUnitermo1')) {
                $unitermo1 = Unitermo::create(['unitermo'=>$request->input('txtUnitermo1')]);
            } else {
                $unitermo1 = Unitermo::find($request->input('unitermo1'));
            }

            if ($request->filled('txtUnitermo2')) {
                $unitermo2 = Unitermo::create(['unitermo'=>$request->input('txtUnitermo2')]);
            } else {
                $unitermo2 = Unitermo::find($request->input('unitermo2'));
            }

            if ($request->filled('txtUnitermo3')) {
                $unitermo3 = Unitermo::create(['unitermo'=>$request->input('txtUnitermo3')]);
            } else {
                $unitermo3 = Unitermo::find($request->input('unitermo3'));
            }            

            if ($request->filled('unitermo4')) {
                $unitermo4 = Unitermo::find($request->input('unitermo4'));
            } elseif ($request->filled('txtUnitermo4')) {
                $unitermo4 = Unitermo::create(['unitermo'=>$request->input('txtUnitermo4')]);
            }

            if ($request->filled('unitermo5')) {
                $unitermo5 = Unitermo::find($request->input('unitermo5'));
            } elseif ($request->filled('txtUnitermo5')) {
                $unitermo5 = Unitermo::create(['unitermo'=>$request->input('txtUnitermo5')]);
            }
            
            $noDelete  = [0];
            $unitermos = array();

            if (empty($unitermo1->id)) {
                $mensagem.= "A Palavra Chave 1 não foi alterado pois o selecionado foi excluído das opções do sistema.";
                $noDelete[] = $request->input('unitermo1');
            } else {
                if ($unitermo1->unitermo <> $unitermo2->unitermo &&
                    $unitermo1->unitermo <> $unitermo3->unitermo  
                ) {
                    if((isset($unitermo4->unitermo) && $unitermo1->unitermo <> $unitermo4->unitermo) ||
                    (isset($unitermo5->unitermo) && $unitermo1->unitermo <> $unitermo5->unitermo) ||
                    (!isset($unitermo4->unitermo) && !isset($unitermo5->unitermo))) {
                        $unitermos[] = $unitermo1;
                    } else {
                        return Redirect::back()->withErrors(['unitermo1'=>'O campo Palavra-chave 1 não pode ser repetido']);
                    } 
                } else {
                    return Redirect::back()->withErrors(['unitermo1'=>'O campo Palavra-chave 1 não pode ser repetido']);
                }
            }
            if (empty($unitermo2->id)) {
                $mensagem.= "A Palavra Chave 2 não foi alterado pois o selecionado foi excluído das opções do sistema.";
                $noDelete[] = $request->input('unitermo2');
            } else {
                if ($unitermo2->unitermo <> $unitermo1->unitermo &&
                    $unitermo2->unitermo <> $unitermo3->unitermo  
                ) {
                    if((isset($unitermo4->unitermo) && $unitermo2->unitermo <> $unitermo4->unitermo) ||
                    (isset($unitermo5->unitermo) && $unitermo2->unitermo <> $unitermo5->unitermo) ||
                    (!isset($unitermo4->unitermo) && !isset($unitermo5->unitermo))) {
                        $unitermos[] = $unitermo2;
                    } else {
                        return Redirect::back()->withErrors(['unitermo2'=>'O campo Palavra-chave 2 não pode ser repetido']);
                    }
                } else {
                    return Redirect::back()->withErrors(['unitermo2'=>'O campo Palavra-chave 2 não pode ser repetido']);
                }
            }
            if (empty($unitermo3->id)) {
                $mensagem.= "A Palavra Chave 3 não foi alterado pois o selecionado foi excluído das opções do sistema.";
                $noDelete[] = $request->input('unitermo3');
            } else {
                if ($unitermo3->unitermo <> $unitermo1->unitermo &&
                    $unitermo3->unitermo <> $unitermo2->unitermo  
                ) {
                    if((isset($unitermo4->unitermo) && $unitermo3->unitermo <> $unitermo4->unitermo) ||
                    (isset($unitermo5->unitermo) && $unitermo3->unitermo <> $unitermo5->unitermo) ||
                    (!isset($unitermo4->unitermo) && !isset($unitermo5->unitermo))) {
                        $unitermos[] = $unitermo3;
                    } else {
                        return Redirect::back()->withErrors(['unitermo3'=>'O campo Palavra-chave 3 não pode ser repetido']);
                    }
                } else {
                    return Redirect::back()->withErrors(['unitermo3'=>'O campo Palavra-chave 3 não pode ser repetido']);
                }
            }
            if (!$request->filled('unitermo4')) {
                $mensagem.= "A Palavra Chave 4 não foi alterado pois o selecionado foi excluído das opções do sistema.";
                $noDelete[] = $request->input('unitermo4');
            } elseif (!empty($unitermo4->id)) {
                if ($unitermo4->unitermo <> $unitermo1->unitermo &&
                    $unitermo4->unitermo <> $unitermo2->unitermo &&
                    $unitermo4->unitermo <> $unitermo3->unitermo) {

                    if((!empty($unitermo5->unitermo) && $unitermo4->unitermo <> $unitermo5->unitermo) ||
                        (empty($unitermo5->unitermo))) {
                        $unitermos[] = $unitermo4;
                    }
                }
            }
            if (!$request->filled('unitermo5')) {
                $mensagem.= "A Palavra Chave 5 não foi alterado pois o selecionado foi excluído das opções do sistema.";
                $noDelete[] = $request->input('unitermo5');
            } elseif (!empty($unitermo5->id)) {
                if ($unitermo5->unitermo <> $unitermo1->unitermo &&
                    $unitermo5->unitermo <> $unitermo2->unitermo &&
                    $unitermo5->unitermo <> $unitermo3->unitermo) {

                    if((!empty($unitermo4->unitermo) && $unitermo5->unitermo <> $unitermo4->unitermo) ||
                        (empty($unitermo4->unitermo))) {
                        $unitermos[] = $unitermo5;
                    }
                }
                
            }

            $numExcluidos = MonoUnitermos::excluirRegistroByMonografia($objMonografia->id, []);
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

                $objMonografia->status = "AGUARDANDO AVALIACAO";
                $objMonografia->update();
            }

            $assuntoMsg = null;
            $txt = null;
            if (auth()->user()->hasRole('aluno') && $envioEmailOrientador) {
                //enviar e-mail para o orientador caso exista alguma avaliação
                $mensagem = "Monografia corrigida e enviada para avaliação";
                
                $textoMensagem = "O projeto de TCC título **".$objMonografia->titulo."** foi corrigida.           
                ";
                $textoMensagem.= "Aluno que cadastrou a correção: **".auth()->user()->name. "**                                        
                ";
                $textoMensagem.= "Correção que foi solicitada: *".$dadosAvaliacao->parecer."*                                                                                            
                ";
                $textoMensagem.= "Entre no sistema para re-avaliar o projeto de TCC.                               
                ";
                $assuntoMsg = "TCC com título ".$objMonografia->titulo." corrigida";

                $comissao = Comissao::find($dadosAvaliacao->comissoes_id);

                Mail::to("pcalves@usp.br", $comissao->nome)
                    ->send(new NotificacaoOrientador($textoMensagem, $assuntoMsg, $comissao->nome));
                /*Mail::to($objOrientador->email, $orientadores->nome)
                      ->send(new NotificacaoOrientador($textoMensagem, $assuntoMsg, $orientadores->nome));*/
 

                /*foreach($objMonografia->orientadores()->get() as $orientadores) {
                    if ($orientadores->pivot->principal) {
                        $txt.= "**Você é o Orientador Principal**.                               
                        ";
                    } else {
                        $txt = null;
                    }
                    $textoMensagem.= $txt;
                    Mail::to("pcalves@usp.br", $orientadores->nome)
                        ->send(new NotificacaoOrientador($textoMensagem, $assuntoMsg, $orientadores->nome));
                    /*Mail::to($objOrientador->email, $orientadores->nome)
                        ->send(new NotificacaoOrientador($textoMensagem, $assuntoMsg, $orientadores->nome));*/
                //}*/
                
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
    public function listMonografia(int $id_orientador = 0, $ano = null, $status= null, $filtro=null) {
        
        if (!auth()->user()->hasRole('orientador') && 
            !auth()->user()->hasRole('graduacao') && 
            !auth()->user()->hasRole('avaliador') &&
            !auth()->user()->can('admin')) {
            return "<script> alert('M7-Você não tem acesso a esta parte do Sistema.'); 
                                window.location.assign('".route('home')."');
                    </script>";
        }
        $dadosParam = Parametro::getDadosParam();
        $sistema_aberto = null;
        $dataAtual = date_create('now');
        if (!isset($dadosParam->dataAberturaDocente)) {
            if (!auth()->user()->can('admin') && !auth()->user()->hasRole('graduacao')) {
                return "<script> alert('M30-O Sistema não foi aberto para edições. Aguarde comunicado da Graduação.'); 
                                window.location.assign('".route('home')."');
                        </script>";
            }
        } elseif (auth()->user()->hasRole('orientador')) {
    
            if ($dataAtual <= $dadosParam->dataAberturaDocente ) {
                $sistema_aberto = "Aguarde abertura do Sistema em ".$dadosParam->dataAberturaDocente->format('d/m/Y'); 
            }

            if ($dataAtual > $dadosParam->dataFechamentoDocente ) {
                $sistema_aberto = "Sistema fechado em ".$dadosParam->dataFechamentoDocente->format('d/m/Y'); 
            } 
        } elseif (auth()->user()->hasRole('avaliador')) {
            if ($dataAtual <= $dadosParam->dataAberturaAvaliacao ) {
                $sistema_aberto = "Aguarde abertura do Sistema em ".$dadosParam->dataAberturaDocente->format('d/m/Y'); 
            }

            if ($dataAtual > $dadosParam->dataFechamentoAvaliacao ) {
                $sistema_aberto = "Sistema fechado em ".$dadosParam->dataFechamentoDocente->format('d/m/Y'); 
            } 
        }
        $userLogado = null;
        $userLogado = (auth()->user()->hasRole('orientador'))?"Orientador":$userLogado;
        $userLogado = (auth()->user()->hasRole('graduacao'))?"Graduacao":$userLogado;
        $userLogado = (auth()->user()->hasRole('avaliador'))?"Avaliador":$userLogado;
        $userLogado = (auth()->user()->can('admin'))?"Admin":$userLogado;
        
        $dadosMonografias = array();
        $grupoAlunos = array();
        $dadosOrientadores = array();

        if ($id_orientador > 0 || $userLogado == "Orientador" ) {
            
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
                if (empty($status)) {
                    $Monografias = Monografia::with(['alunos','orientadores'])
                                            ->whereRelation('orientadores', 'orientadores_id', $id_orientador)
                                            ->where('status','<>','CONCLUÍDO')
                                            ->orderBy('ano','desc')
                                            ->paginate(30);
                } else {
                    $Monografias = Monografia::with(['alunos','orientadores'])
                                            ->whereRelation('orientadores', 'orientadores_id', $id_orientador)
                                            ->where('status',$status)
                                            ->orderBy('ano','desc')
                                            ->paginate(30);
                }
            } else {
                if (empty($status)) {
                    $Monografias = Monografia::with(['alunos','orientadores'])
                                             ->where('status','<>','CONCLUÍDO')
                                             ->whereRelation('orientadores', 'orientadores_id', $id_orientador)
                                             ->whereRelation('alunos','nome','like',"%$filtro%")
                                             ->whereOr('titulo','like',"%$filtro%")
                                             ->whereOr('ano','like',"%$filtro%")
                                             ->orderBy('ano','desc')
                                             ->get();
                } else {
                    $Monografias = Monografia::with(['alunos','orientadores'])
                                             ->where('status',$status)
                                             ->whereRelation('orientadores', 'orientadores_id', $id_orientador)
                                             ->whereRelation('alunos','nome','like',"%$filtro%")
                                             ->whereOr('titulo','like',"%$filtro%")
                                             ->whereOr('ano','like',"%$filtro%")
                                             ->orderBy('ano','desc')
                                             ->get();
                }
                
            }
        } elseif ($userLogado == "Avaliador") {
            $comissao = Comissao::where('codpes',auth()->user()->codpes)->get();
            if ($comissao->isEmpty()) {
                return "<script> alert('M41-Você não tem permissão para acessar essa área do sistema.'); 
                            window.location.assign('".route('home')."');
                    </script>";
            }
            $id_orientador = $comissao->first()->id;

            if (empty($filtro)) {
                if (empty($status)) {
                    $Monografias = Monografia::with(['alunos','avaliacoes'])
                                             ->whereRelation('avaliacoes', 'comissoes_id', $comissao->first()->id)
                                             /*->whereHas('avaliacoes', function (Builder $query) {
                                                                        $query->whereIn('status', ['AGUARDANDO','CORRIGIDO']);
                                                                    }) */
                                             ->where('status','<>','CONCLUIDO')
                                             ->orderBy('ano','desc')
                                             ->paginate(30);
                } else {
                    $Monografias = Monografia::with(['alunos','avaliacoes'])
                                             ->whereRelation('avaliacoes', 'comissoes_id', $comissao->first()->id)
                                             /*->whereHas('avaliacoes', function (Builder $query) {
                                                                        $query->whereIn('status', ['AGUARDANDO','CORRIGIDO']);
                                                                    }) */
                                             ->where('status',$status)
                                             ->orderBy('ano','desc')
                                             ->paginate(30);
                }
                
            } else {
                if (empty($status)) {
                    $Monografias = Monografia::with(['alunos','avaliacoes'])
                                             ->where('status','<>','CONCLUIDO')
                                             ->whereRelation('avaliacoes', 'comissoes_id', $comissao->first()->id)
                                             /*->whereHas('avaliacoes', function (Builder $query) {
                                                                        $query->whereIn('status', ['AGUARDANDO','CORRIGIDO']);
                                                                    }) 
                                             ->whereDoesntHave('avaliacoes', function (Builder $query) {
                                                                                $query->where('status', 'APROVADO');
                                                                            }) */
                                             ->whereRelation('alunos','nome','like',"%$filtro%")
                                             ->whereOr('titulo','like',"%$filtro%")
                                             ->whereOr('ano','like',"%$filtro%")
                                             ->orderBy('ano','desc')
                                             ->get();
                } else {
                    $Monografias = Monografia::with(['alunos','avaliacoes'])
                                             ->where('status',$status)
                                             ->whereRelation('avaliacoes', 'comissoes_id', $comissao->first()->id)
                                             /*->whereHas('avaliacoes', function (Builder $query) {
                                                                        $query->whereIn('status', ['AGUARDANDO','CORRIGIDO']);
                                                                    }) 
                                             ->whereDoesntHave('avaliacoes', function (Builder $query) {
                                                                                $query->where('status', 'APROVADO');
                                                                            }) */
                                             ->whereRelation('alunos','nome','like',"%$filtro%")
                                             ->whereOr('titulo','like',"%$filtro%")
                                             ->whereOr('ano','like',"%$filtro%")
                                             ->orderBy('ano','desc')
                                             ->get();
                }
            }
        } else {
            if (empty($filtro)) {
                if (empty($status)) {
                    $Monografias = Monografia::with(['alunos','orientadores'])
                                             ->where('status','<>','CONCLUIDO')
                                             ->orderBy('ano','desc')->paginate(30);
                } else {
                    $Monografias = Monografia::with(['alunos','orientadores'])
                                             ->where('status',$status)
                                             ->orderBy('ano','desc')->paginate(30);
                }
            } else {
                $ObjMonografias = new Monografia;
                $Monografias = $ObjMonografias->getMonografiaByFiltro($filtro,0,0,$status);

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
                      ,"status"           => $status
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

        if ($request->filled('filtro'))
            return $this->listMonografia($request->input('id_orientador'), null, null, $request->input('filtro'));
        
        if ($request->filled('filtroStatus')) 
            return $this->listMonografia($request->input('id_orientador'), null, $request->input('filtroStatus'));

        return redirect()->route('orientador.lista_monografia');
        //return view('cadastro-banca', ['listBanca'=>$listBanca, 'buscaRegistro' => 1, 'filtro' => $request->input('filtro')]);
    }

    /**
     * Salva dados de aprovação de projeto de monografia
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function aprovaProjeto (Request $request) {

        if (!auth()->user()->hasRole('orientador') &&
            !auth()->user()->can('admin')) {
            return "<script> alert('M27-Você não tem acesso a esta parte do Sistema.'); 
                                window.location.assign('".route('home')."');
                    </script>";
        }

        $monoOrientadores = MonoOrientadores::where('monografia_id',$request->input('idTcc'))->get();
        $monoOrientador = MonoOrientadores::find($monoOrientadores->first()->id);

        if ($request->input('aprovacao_projeto')) {
            $monoOrientador->status = "APROVADO";
            $monoOrientador->update();
            
            $monografia = Monografia::find($request->input('idTcc'));
            $monografia->status="AGUARDANDO AVALIACAO";
            $monografia->update();

            $aluno = Aluno::where('monografia_id',$request->input('idTcc'))->get();

            $txtEmailComissao = "O projeto ".$monografia->titulo.", cadastrado pelo aluno ".$aluno->first()->nome." foi incluído no sistema de TCC. 
            ";
            $txtEmailComissao = "Encaminhe para avaliação, indicando um parecerista no Sistema                                                      
            ";

            Mail::to("pcalves@usp.br","Comissão de TCC")
                    ->send(new NotificacaoAluno($txtEmailComissao,"Comissão de TCC"));
            /*Mail::to("ctcc.fcf@usp.br", "Comissão de TCC")
                    ->send(new NotificacaoAluno($txtEmailComissao,"Comissão de TCC"));*/
        }

        return redirect()->route('orientador.edicao',['idMono'=>$monografia->id]);
    }

    /**
     * Método para indicar parecerista
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function indicaParecerista(Request $request) {
        $rule     = [];
        $messages = [];
        $rules['idTcc']       = ["required","exists:monografias,id"];
        $rules['parecerista'] = ["required","exists:comissoes,id"];
        $messages['required'] = "Favor informar o :attribute do TCC.";

        $request->validate($rules,$messages);

        $msg        = null;
        $monografia = Monografia::find($request->input('idTcc'));

        if ($monografia->status == "AGUARDANDO AVALIACAO") {
            $avaliacoes = new Avaliacao;
            $avaliacoes->monografia_id = $request->input('idTcc');
            $avaliacoes->comissoes_id  = $request->input('parecerista');
            $avaliacoes->dataAvaliacao = date_create('now');
            $avaliacoes->status = "AGUARDANDO";
            $avaliacoes->save();

            $aluno = Aluno::where("monografia_id",$monografia->id)->get();
            $orientador = Orientador::whereRelation("monografias","monografia_id",$monografia->id)->get();
            $comissao = Comissao::find($request->input('parecerista'));

            $assunto = "[".config('app.name')."] Você acaba de receber um TCC para avaliação.";

            $txtMsg = "Você está recebendo o projeto abaixo para avaliação:               
            ";
            $txtMsg.= "**Alun@:** ".$aluno->first()->nome."                                     
            ";
            $txtMsg.= "**Orientador(a):** ".$orientador->first()->nome."                                
            ";
            $txtMsg.= "**Título:** ".$monografia->titulo."                                       
            ";
            $txtMsg.= "**Você tem o prazo de 3 dias úteis.**                          
            ";

            Mail::to("pcalves@usp.br", $comissao->nome)
                ->send(new NotificacaoOrientador($txtMsg, $assunto, $comissao->nome));
            /*Mail::to($comissao->email, $comissao->nome)
                    ->send(new NotificacaoOrientador($txtMsg, $assunto, $comissao->nome));*/
        } else {
            $msg = "O Projeto ainda não foi aprovado pelo Orientador.";
        }

        return redirect()->route('orientador.edicao',['idMono'=>$request->input('idTcc'), 'msg' => $msg]);

    }

    /**
     * Valida data da Defesa
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function validaDefesa(Request $request) {

        if ($request->filled('cadData')) {
            $rule['txtData'] = ['required', new VerificaDatas()];
            $rule['txtHora'] = ['required'];
            $dataEscolhida = $request->input('txtData')." ".$request->input('txtHora');
        } else {
            $rule['dataEscolhida'] = ['required'];
            $dataEscolhida = $request->input('dataEscolhida');
        }

        $messages['required'] = "Favor informar o campo :attribute.";

        $request->validate($rule,$messages);

        $textoMensagem    = null;
        $assunto          = null;
        $dataEscolhida    = explode("/",$dataEscolhida);
        $hora             = explode(" ",$dataEscolhida[2]);
        $dataEscolhida[2] = $hora[0];
        $hora             = $hora[1];
        $dtEscolhida      = date_create($dataEscolhida[2]."/".$dataEscolhida[1]."/".$dataEscolhida[0]." ".$hora);
        
        $defesa = Defesa::find($request->input('idDefesa'));
        $defesa->dataEscolhida = $dtEscolhida;
        $defesa->user_data = auth()->user()->codpes;
       
        if ($defesa->update()) {
            $monografia = Monografia::find($request->input('monografiaId'));
            $monografia->status = 'AGUARDANDO DEFESA';
            $monografia->update();

            $aluno = Aluno::where('monografia_id',$monografia->id)->get();
    
            $assunto = "Defesa Agendada, Projeto: ".$monografia->titulo;
    
            $textoMensagem = "A data da apresentação do TCC para a Banca foi agendada.             
            ";
            $textoMensagem.= "**Aluno:** ".$aluno->first()->nome."                                 
            ";
            $textoMensagem.= "**Título do Trabalho:** ".$monografia->titulo."                            
            ";
            $textoMensagem.= "**Data e horário:** ".$dtEscolhida->format('d/m/Y H:i')."                  
            ";
            $textoMensagem.= "Consulte o link abaixo que contém a data e o horário, bem como as normas da defesa virtual:           
            ";
            $textoMensagem.= "http://www.fcf.usp.br/graduacao/subpagina.php?menu=51&subpagina=351             
            ";

            $banca = Banca::where('monografia_id',$monografia->id)->get();
            $arquivoTcc = public_path()."/upload/".$monografia->path_arq_tcc;

            foreach ($banca as $membro) {
                Mail::to("pcalves@usp.br", $membro->nome)
                    ->send(new NotificacaoOrientador($textoMensagem, $assunto, $membro->nome, $arquivoTcc));
                /*Mail::to($membro->email, $membro->nome)
                      ->send(new NotificacaoOrientador($textoMensagem, $assunto, $membro->nome, $arquivoTcc));*/
            }  
    
            print "<script> alert('Defesa validadada com Sucesso'); </script>";
        } else {
            print "<script> alert('Erro na validação da Banca'); </script>";
        }
        
        return redirect(route('orientador.edicao',['idMono'=>$request->input('monografiaId')]));
    }

}
