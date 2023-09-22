<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificacaoOrientador;

use App\Models\Aluno;
use App\Models\Parametro;
use App\Models\Defesa;
use App\Models\Banca;
use App\Models\Monografia;
use App\Models\Orientador;

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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function salvarBanca(Request $request)
    {
        $rules['data1']           = ["required", new VerificaDatas];
        $rules['data2']           = ["required", new VerificaDatas];
        $rules['data3']           = ["required", new VerificaDatas];
        $rules['horario1']        = ["required"];
        $rules['horario2']        = ["required"];
        $rules['horario3']        = ["required"];
        $rules['telefone1']       = ["required","min:10"];
        $rules['membroBanca2']    = ["required","min:3","max:100"];
        $rules['emailBanca2']     = ["required","email"];
        $rules['telefone2']       = ["required","min:10"];
        $rules['membroBanca3']    = ["required","min:3","max:100"];
        $rules['emailBanca3']     = ["required","email"];
        $rules['telefone3']       = ["required","min:10"];
        $rules['suplente']        = ["required","min:3","max:100"];
        $rules['emailSuplente']   = ["required","email"];
        $rules['telefoneSuplente']= ["required","min:10"];
        $rules['cienteOrientador']= ["required"];
        $rules['path_arq_tcc']    = ["file","required","mimes:pdf"];

        $messages["required.cienteOrientador"]  = "O Orientador precisa estar ciente da composição da banca.";
        $messages["required"]                   = "O campo :attribute é obrigatório";
        $messages["min"]                        = "O campo :attribute precisa conter no mínimo :min caracteres";
        $messages["max"]                        = "O campo :attribute deverá conter no máximo :max caracteres";
        $messages["file"]                       = "O arquivo não é válido";

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

           $monografia = Monografia::find($request->input('monografiaId'));
           $monografia->path_arq_tcc = $nomeArq;
           $monografia->status       = "AGUARDANDO VALIDACAO DA BANCA";
           $monografia->update();

           $txtMensagem = "Foi indicada banca para apresentação de TCC.                            
           ";
           $txtMensagem.= "Entre no sistema para validar a Defesa         
           ";

           Mail::to("pcalves@usp.br", "Comissão TCC")
                    ->send(new NotificacaoOrientador($txtMensagem, "[".config('app.name')."] Nova Defesa cadastrada para TCC titulo ".$monografia->titulo, "Comissão TCC"));
           /*Mail::to("ctcc.fcf@usp.br", "Comissão TCC")
                    ->send(new NotificacaoOrientador($txtMensagem, "[".config('app.name')."] Nova Defesa cadastrada para TCC titulo ".$monografia->titulo, "Comissão TCC"));*/

           $mensagem = "Defesa indicada, aguarde validação da Comissão de TCC";
        } else {
            $mensagem = "Erro no cadastro da Defesa";
        }

        return redirect()->route('alunos.index', 
                                ['monografiaId' => $request->input('monografiaId')
                                ,'ano' => date('Y')
                                ,'mensagem' => $mensagem
                                ]);
        
        return var_dump($request->all());
    }
    
}
