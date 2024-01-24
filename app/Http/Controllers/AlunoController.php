<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use App\Models\Aluno;
use App\Models\Parametro;
use App\Models\Defesa;
use App\Models\Banca;
use App\Models\Monografia;
use App\Models\Orientador;

use App\Jobs\EnviarEmailOrientador;

use Uspdev\Replicado\Pessoa;

use App\Rules\VerificaDatas;

class AlunoController extends Controller
{
    
    public function __construct() {
        if (!auth()->check()) {
            return redirect('home');
        } else {
            $this->autenticacao = auth()->user()->verificaIdentidade();
            if (!auth()->user()->hasRole('aluno') && !auth()->user()->can('admin')) {
                print("<script>alert('Você não tem acesso a esta parte do sistema');</script>");
                return redirect('home');
            }
        }
            
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($monografia_id = 0, $ano = null, $mensagem = null)
    {
        $parametroSistema = Parametro::where('ano',date('Y'))->whereNull('codpes')->get();
        if ($parametroSistema->isEmpty() && auth()->user()->hasRole('aluno')) {
            print("<script>alert('Sistema ainda não parametrizado. Entre em contato com o Serviço de Graduação.');</script>");
            return redirect('home');
        }
        
        $monografia = new MonografiaController();
        return $monografia->index($monografia_id, $ano, $mensagem);
    }

    /**
     * Mostra as disciplinas do aluno
     */
    public function disciplinas($numUsp) {
        dd(Aluno::disciplinasMatricula($numUsp));
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
        return $this->index();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->index();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return $this->index();
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
        return $this->index();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->index();
    }

    /**
     * Salvar dados da Banca
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function salvarBanca(Request $request)
    {
        $rules['data1']                 = ["required", new VerificaDatas];
        $rules['data2']                 = ["required", new VerificaDatas];
        $rules['data3']                 = ["required", new VerificaDatas];
        $rules['horario1']              = ["required"];
        $rules['horario2']              = ["required"];
        $rules['horario3']              = ["required"];
        $rules['telefone1']             = ["required","min:10"];
        $rules['membroBanca2']          = ["required","min:3","max:100"];
        $rules['emailBanca2']           = ["required","email"];
        $rules['telefone2']             = ["required","min:10"];
        $rules['instituicao2']          = ["required","min:3"];
        $rules['membroBanca3']          = ["required","min:3","max:100"];
        $rules['emailBanca3']           = ["required","email"];
        $rules['telefone3']             = ["required","min:10"];
        $rules['instituicao3']          = ["required","min:3"];
        $rules['suplente']              = ["required","min:3","max:100"];
        $rules['emailSuplente']         = ["required","email"];
        $rules['telefoneSuplente']      = ["required","min:10"];
        $rules['instituicaoSuplente']   = ["required","min:3"];
        $rules['path_arq_tcc']          = ["file","required","mimes:pdf"];
        $rules['aluno_autoriza_publicar']= "required";

        $messages["required"] = "O campo :attribute é obrigatório";
        $messages["min"]      = "O campo :attribute precisa conter no mínimo :min caracteres";
        $messages["max"]      = "O campo :attribute deverá conter no máximo :max caracteres";
        $messages["file"]     = "O arquivo não é válido";

        $request->validate($rules,$messages);

        if ($request->hasFile('path_arq_tcc') && $request->file('path_arq_tcc')->isValid()) {
            $arquivo = $request->file('path_arq_tcc');

            if (!$arquivo->move(public_path('upload'),$arquivo->getClientOriginalName())) {
                return "<script> alert('M26-Erro ao copiar o arquivo.'); 
                                       window.location.assign('".route('alunos.index', 
                                                                    ['monografiaId' => $request->input('monografiaId')
                                                                    ,'ano' => date('Y')
                                                                    ,'mensagem' => $mensagem
                                                                    ])."');
                    </script>";
            }
            $nomeArq = $arquivo->getClientOriginalName();
        } else {
            if (isset($rules['path_arq_tcc'])) {
                return "<script> alert('M16-Erro de upload de arquivo'); 
                                 window.location.assign('".route('alunos.index', 
                                                        ['monografiaId' => $request->input('monografiaId')
                                                        ,'ano' => date('Y')
                                                        ,'mensagem' => $mensagem
                                                        ])."');
                    </script>";
            }
            $nomeArq = null;
        }

        $monografia = Monografia::find($request->input('monografiaId'));

        $dataDefesa1 = explode("/",$request->input('data1'));
        $dataDefesa2 = explode("/",$request->input('data2'));
        $dataDefesa3 = explode("/",$request->input('data3'));

        $defesa = new Defesa();
        $defesa->monografia_id = $request->input('monografiaId');
        $defesa->dataDefesa1 = date_create($dataDefesa1[2]."/".$dataDefesa1[1]."/".$dataDefesa1[0]." ".$request->input('horario1').":00");
        $defesa->dataDefesa2 = date_create($dataDefesa2[2]."/".$dataDefesa2[1]."/".$dataDefesa2[0]." ".$request->input('horario2').":00");
        $defesa->dataDefesa3 = date_create($dataDefesa3[2]."/".$dataDefesa3[1]."/".$dataDefesa3[0]." ".$request->input('horario3').":00");
        
        if($defesa->save()) {

           $dadosOrientador = Orientador::find($request->input('orientadorId'));

           $banca = new Banca();
           $banca->monografia_id       = $request->input('monografiaId');
           $banca->codpes              = $dadosOrientador->codpes;
           $banca->nome                = $request->input('membroBanca1');
           $banca->email               = $dadosOrientador->email;
           $banca->telefone            = $request->input('telefone1');
           $banca->instituicao_vinculo = $dadosOrientador->instituicao_vinculo;
           $banca->papel               = "PRESIDENTE";
           $banca->ordem               = 1;
           $banca->ano                 = date('Y');
           $banca->save();

           $banca = new Banca();
           $banca->monografia_id        = $request->input('monografiaId');
           $banca->codpes               = $request->input('nusp2');
           $banca->nome                 = $request->input('membroBanca2');
           $banca->email                = $request->input('emailBanca2');
           $banca->telefone             = $request->input('telefone2');
           $banca->instituicao_vinculo  = $request->input('instituicao2');
           $banca->papel                = "MEMBRO";
           $banca->ordem                = 2;
           $banca->ano                  = date('Y');
           $banca->save();

           $banca = new Banca();
           $banca->monografia_id        = $request->input('monografiaId');
           $banca->codpes               = $request->input('nusp3');
           $banca->nome                 = $request->input('membroBanca3');
           $banca->email                = $request->input('emailBanca3');
           $banca->telefone             = $request->input('telefone3');
           $banca->instituicao_vinculo  = $request->input('instituicao3');
           $banca->papel                = "MEMBRO";
           $banca->ordem                = 3;
           $banca->ano                  = date('Y');
           $banca->save();

           $banca = new Banca();
           $banca->monografia_id        = $request->input('monografiaId');
           $banca->codpes               = $request->input('nuspSuplente');
           $banca->nome                 = $request->input('suplente');
           $banca->email                = $request->input('emailSuplente');
           $banca->telefone             = $request->input('telefoneSuplente');
           $banca->instituicao_vinculo  = $request->input('instituicaoSuplente');
           $banca->papel                = "SUPLENTE";
           $banca->ordem                = 4;
           $banca->ano                  = date('Y');
           $banca->save();

           $monografia->path_arq_tcc = $nomeArq;
           $monografia->aluno_autoriza_publicar = $request->input('aluno_autoriza_publicar');
           $monografia->status       = "AGUARDANDO VALIDACAO DA BANCA";
           $monografia->update();

           $aluno = Aluno::where('monografia_id',$monografia->id)->get();
           $orientador = Orientador::whereRelation('monografias','monografia_id',$monografia->id)->get();

           $assunto = "Você acaba de receber uma banca para ciência e aprovação.";

           $txtMensagem = "Você recebeu a indicação da Comissão Julgadora do Trabalho de Conclusão de Curso abaixo:               
           ";
           $txtMensagem.= "**Alun@:** ".$aluno->first()->nome."                                     
           ";
           $txtMensagem.= "**Orientador(a):** ".$orientador->first()->nome."                                
           ";
           $txtMensagem.= "**Título:** ".$monografia->titulo."                                       
           ";
           $txtMensagem.= "**Acesse o sistema, coloque o seu DE ACORDO e encaminhe para a CTCC**     
           ";
           $txtMensagem.= "**Você tem o prazo de 3 dias úteis.**                          
           ";

           EnviarEmailOrientador::dispatch(['email'         => $orientador->first()->email
                                            ,'textoMsg'     => $txtMensagem
                                            ,'assuntoMsg'   => $assunto
                                            ,'nome'         => $orientador->first()->nome 
                                            ]);

           $mensagem = "Defesa indicada, aguarde validação do Orientador";
        } else {
            $mensagem = "Erro no cadastro da Defesa";
        }

        print "<script>alert('$mensagem'); </script>";

        return redirect()->route('alunos.index', 
                                ['monografiaId' => $request->input('monografiaId')
                                ,'ano' => date('Y')
                                ,'mensagem' => $mensagem
                                ]);
    }

    /**
     * Corrigir dados de banca
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function corrigirBanca(Request $request) {

        $rules['data1']             = ["required", new VerificaDatas];
        $rules['data2']             = ["required", new VerificaDatas];
        $rules['data3']             = ["required", new VerificaDatas];
        $rules['horario1']          = ["required"];
        $rules['horario2']          = ["required"];
        $rules['horario3']          = ["required"];
        $rules['telefone1']         = ["required","min:10"];
        $rules['membroBanca2']      = ["required","min:3","max:100"];
        $rules['emailBanca2']       = ["required","email"];
        $rules['telefone2']         = ["required","min:10"];
        $rules['instituicao2']      = ["required","min:3"];
        $rules['membroBanca3']      = ["required","min:3","max:100"];
        $rules['emailBanca3']       = ["required","email"];
        $rules['telefone3']         = ["required","min:10"];
        $rules['instituicao3']      = ["required","min:3"];
        $rules['suplente']          = ["required","min:3","max:100"];
        $rules['emailSuplente']     = ["required","email"];
        $rules['telefoneSuplente']  = ["required","min:10"];
        $rules['instituicaoSuplente']= ["required","min:3"];
        
        $messages["required"] = "O campo :attribute é obrigatório";
        $messages["min"]      = "O campo :attribute precisa conter no mínimo :min caracteres";
        $messages["max"]      = "O campo :attribute deverá conter no máximo :max caracteres";
        
        $request->validate($rules,$messages);

        $monografia = Monografia::with(['orientadores', 'alunos'])
                                   ->find($request->input('monografiaId'));     
        
        if ($request->hasFile('path_arq_tcc') && $request->file('path_arq_tcc')->isValid()) {
            
            $arquivo = $request->file('path_arq_tcc');
            $nomeArq = $arquivo->getClientOriginalName();

            if (!$arquivo->move(public_path('upload'),$nomeArq)) {
                $mensagem = "Erro ao copiar o arquivo";
                return "<script> alert('M26-Erro ao copiar o arquivo.'); 
                                       window.location.assign('".route('alunos.index', 
                                                                    ['monografiaId' => $request->input('monografiaId')
                                                                    ,'ano' => date('Y')
                                                                    ,'mensagem' => $mensagem
                                                                    ])."');
                    </script>";
            } else {
                if (!empty($monografia->path_arq_tcc) && $monografia->path_arq_tcc <> $nomeArq) {
                    File::delete('upload/'.$objMonografia->path_arq_tcc);
                }                
                $monografia->path_arq_tcc = $nomeArq; 
            }
        }
        if ($request->filled('aluno_autoriza_publicar'))
            $monografia->aluno_autoriza_publicar = $request->input('aluno_autoriza_publicar');
        
        $monografia->update();   

        $dataDefesa1 = explode("/",$request->input('data1'));
        $dataDefesa2 = explode("/",$request->input('data2'));
        $dataDefesa3 = explode("/",$request->input('data3'));

        $defesa = Defesa::where('monografia_id',$request->input('monografiaId'))->get();
        $defesa->first()->dataDefesa1 = date_create($dataDefesa1[2]."/".$dataDefesa1[1]."/".$dataDefesa1[0]." ".$request->input('horario1').":00");
        $defesa->first()->dataDefesa2 = date_create($dataDefesa2[2]."/".$dataDefesa2[1]."/".$dataDefesa2[0]." ".$request->input('horario2').":00");
        $defesa->first()->dataDefesa3 = date_create($dataDefesa3[2]."/".$dataDefesa3[1]."/".$dataDefesa3[0]." ".$request->input('horario3').":00");
        $defesa->first()->aprovacao_orientador = null;

        if($defesa->first()->update()) {

           $dadosOrientador = Orientador::find($request->input('orientadorId'));
           $banca = Banca::where('monografia_id',$request->input('monografiaId'))->orderBy('ordem')->get();

           foreach($banca as $objBanca) {
                if ($objBanca->papel == "PRESIDENTE")
                    continue;

                if ($objBanca->ordem == 2) {
                    $objBanca->codpes               = $request->input('nusp2');
                    $objBanca->nome                 = $request->input('membroBanca2');
                    $objBanca->email                = $request->input('emailBanca2');
                    $objBanca->telefone             = $request->input('telefone2');
                    $objBanca->instituicao_vinculo  = $request->input('instituicao2');
                }
                if ($objBanca->ordem == 3) {
                    $objBanca->codpes               = $request->input('nusp3');
                    $objBanca->nome                 = $request->input('membroBanca3');
                    $objBanca->email                = $request->input('emailBanca3');
                    $objBanca->telefone             = $request->input('telefone3');
                    $objBanca->instituicao_vinculo  = $request->input('instituicao3');
                }
                if ($objBanca->ordem == 4) {
                    $objBanca->codpes               = $request->input('nuspSuplente');
                    $objBanca->nome                 = $request->input('suplente');
                    $objBanca->email                = $request->input('emailSuplente');
                    $objBanca->telefone             = $request->input('telefoneSuplente');
                    $objBanca->instituicao_vinculo  = $request->input('instituicaoSuplente');
                }
                $objBanca->update();
           }     

           $assunto = "O aluno realizou a correção da banca";

           $txtMensagem = "Você acaba de receber a correção da banca realizada pelo aluno conforme suas orientações para ciência e aprovação referente ao projeto abaixo:               
           ";
           $txtMensagem.= "**Alun@:** ".$monografia->alunos->first()->nome."                                     
           ";
           $txtMensagem.= "**Orientador(a):** ".$monografia->orientadores->first()->nome."                                
           ";
           $txtMensagem.= "**Título:** ".$monografia->titulo."                                       
           ";
           $txtMensagem.= "**Você tem o prazo de 3 dias úteis.**                          
           ";

           EnviarEmailOrientador::dispatch(['email'         => $monografia->orientadores->first()->email
                                            ,'textoMsg'     => $txtMensagem
                                            ,'assuntoMsg'   => $assunto
                                            ,'nome'         => $monografia->orientadores->first()->nome 
                                            ]);

           $mensagem = "Defesa corrigida, aguarde validação do Orientador";
           
        } else {
            $mensagem = "Erro no cadastro da Defesa";
        }

        print "<script>alert('$mensagem'); </script>";

        return redirect()->route('alunos.index', 
                                ['monografiaId' => $request->input('monografiaId')
                                ,'ano' => date('Y')
                                ,'mensagem' => $mensagem
                                ]);
    }
    
}
