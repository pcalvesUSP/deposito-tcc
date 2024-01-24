<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;

use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\Comissao;
use App\Models\Banca;
use App\Models\Monografia;
use App\Models\Aluno;
use App\Models\Defesa;

use Uspdev\Replicado\Pessoa;

use App\Rules\verificaBanca;
use App\Rules\VerificaDatas;

use App\Jobs\EnviarEmailOrientador;
use App\Jobs\EnviarEmailAluno;

class ComissaoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(String $msg = null)
    {
        $listComissao = Comissao::where('dtInicioMandato','<=', date_create('now')->format('Y-m-d'))
                                ->where('dtFimMandato', '>=', date_create('now')->format('Y-m-d'))
                                ->get();
        
        return view('cadastro-comissao', ['mensagem' => $msg, 'listComissao' => $listComissao]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('form-cadastro-comissao');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rule = ["nuspComissao"     => ["required","numeric"]
                ,"nomeComissao"     => ["required","min:3","max:100"]
                ,"emailComissao"    => ["required","email","max:100"]
                ,"papelComissao"    => ["required"]
                ,"dtInicioMandato"  => ["required","date_format:d/m/Y"]
                ,"dtFimMandato"     => ["required","date_format:d/m/Y"]
                ,"assinatura"       => ["image","file"]
                ];

        $messages = ["required"         => "O campo :attribute deve ser preenchido"
                    ,"min"              => "O campo :attribute deve ter no mínimo :min caracteres"
                    ,"max"              => "O campo :attribute deve ter no máximo :max caracteres"
                    ,"email"            => "O campo :attribute deve ser um e-mail válido"
                    ,"assinatura.image" => "O arquivo de assinatura deve ser do tipo JPG ou PNG"
                    ,"assinatura.file"  => "O upload do arquivo de assinatura falhou"
                    ];

        $request->validate($rule,$messages);

        $upload = false;
        if ($request->hasFile('assinatura') && $request->file('assinatura')->isValid()) {
            $arquivo = $request->file('assinatura');
                
            $arquivo->move(public_path('upload/assinatura'),$arquivo->getClientOriginalName());
            $upload = true;                
        }       

        $dtIni = explode("/",$request->input('dtInicioMandato'));
        $dtFim = explode("/",$request->input('dtFimMandato'));

        $comissaoDel = Comissao::where('codpes',$request->input('nuspComissao'))
                            ->where('papel',$request->input('papelComissao'))
                            ->onlyTrashed()
                            ->get();

        $comissaoId = null;

        if ($comissaoDel->isEmpty()) {
            $comissaoDel = Comissao::where('codpes',$request->input('nuspComissao'))
                                ->where('papel',$request->input('papelComissao'))
                                ->where('dtFimMandato','<=', date_create('now')->format('Y/m/d'))
                                ->get();
            
            if ($comissaoDel->isEmpty()) {
                $objComissao = new Comissao;
            } else {
                $objComissao = $comissaoDel->first();
                $comissaoId = $objComissao->id;
            }
        } else {
            $objComissao = $comissaoDel->first()->restore();
            $comissaoId = $objComissao->id;
        }
        
        if (empty($comissaoId)) {
            $coordenador = Comissao::where('dtInicioMandato','<=',date_create('now')->format('Y/m/d'))
                                ->where('dtFimMandato', '>=', date_create('now')->format('Y/m/d'))
                                ->where('papel','COORDENADOR');
        } else {
            $coordenador = Comissao::where('dtInicioMandato','<=',date_create('now')->format('Y/m/d'))
                                    ->where('dtFimMandato', '>=', date_create('now')->format('Y/m/d'))
                                    ->where('papel','COORDENADOR')
                                    ->where('id','<>',$comissaoId);
        }
        if ($coordenador->count() > 0 && $request->input('papelComissao') == 'COORDENADOR') {
            return Redirect::back()
                        ->withErrors(['papelComissao'=>'Já existe um coordenador cadastrado, não é permitido haver dois.'])
                        ->withInput();
        }

        $objComissao->codpes            = $request->input('nuspComissao');
        $objComissao->nome              = $request->input('nomeComissao');
        $objComissao->email             = $request->input('emailComissao');
        $objComissao->papel             = $request->input('papelComissao');
        $objComissao->dtInicioMandato   = $dtIni[2]."/".$dtIni[1]."/".$dtIni[0];
        $objComissao->dtFimMandato      = $dtFim[2]."/".$dtFim[1]."/".$dtFim[0];
        $objComissao->assinatura        = ($upload)?$arquivo->getClientOriginalName():null;
        $objComissao->save();

        return redirect()->route('comissao.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $objComissao = Comissao::find($id);

        $objComissao->dtInicioMandato = date_create($objComissao->dtInicioMandato);
        $objComissao->dtFimMandato = date_create($objComissao->dtFimMandato);
        
        return view('form-cadastro-comissao',['objComissao'=>$objComissao]);
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
        $rule = ["nomeComissao"     => ["required","min:3","max:100"]
                ,"emailComissao"    => ["required","email","max:100"]
                ,"papelComissao"    => ["required"]
                ,"dtInicioMandato"  => ["required","date_format:d/m/Y"]
                ,"dtFimMandato"     => ["required","date_format:d/m/Y"]
                ,"assinatura"       => ["image","file"]
                ];

        $messages = ["required"         => "O campo :attribute deve ser preenchido"
                    ,"min"              => "O campo :attribute deve ter no mínimo :min caracteres"
                    ,"max"              => "O campo :attribute deve ter no máximo :max caracteres"
                    ,"email"            => "O campo :attribute deve ser um e-mail válido"
                    ,"assinatura.image" => "O arquivo de assinatura deve ser do tipo JPG ou PNG"
                    ,"assinatura.file"  => "O upload do arquivo de assinatura falhou"
                    ];

        $request->validate($rule,$messages);
        
        $upload = false;
        if ($request->hasFile('assinatura') && $request->file('assinatura')->isValid()) {
            $arquivo = $request->file('assinatura');
            
            $arquivo->move(public_path('upload/assinatura'),$arquivo->getClientOriginalName());
            $upload = true;
        } 

        $dtIni = explode("/",$request->input('dtInicioMandato'));
        $dtFim = explode("/",$request->input('dtFimMandato'));

        if ($request->input('papelComissao') == 'COORDENADOR') {
            $coordenador = Comissao::where('dtInicioMandato','<=',date_create('now')->format('Y/m/d'))
                                ->where('dtFimMandato', '>=', date_create('now')->format('Y/m/d'))
                                ->where('papel','COORDENADOR')
                                ->where('id','<>',$id)
                                ->count();

            if ($coordenador > 0) {
                return Redirect::back()
                            ->withErrors(['papel'=>'Já existe um coordenador cadastrado'])
                            ->withInput();
            }
        }

        $objComissao = Comissao::find($id);
        $objComissao->nome              = $request->input('nomeComissao');
        $objComissao->email             = $request->input('emailComissao');
        $objComissao->papel             = $request->input('papelComissao');
        $objComissao->dtInicioMandato   = $dtIni[2]."/".$dtIni[1]."/".$dtIni[0];
        $objComissao->dtFimMandato      = $dtFim[2]."/".$dtFim[1]."/".$dtFim[0];

        if ($upload) {
            if (!empty($objComissao->assinatura) && 
                $arquivo->getClientOriginalName() != $objComissao->assinatura) 
            {
                File::delete('upload/assinatura/'.$objComissao->assinatura);
            }
            $objComissao->assinatura = $arquivo->getClientOriginalName();
        }
        
        if ($objComissao->update())
            print "<script>alert('Correção efetuada'); </script>";

        return redirect()->route('comissao.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $objComissao = Comissao::find($id);
        if (!empty($objComissao->assinatura)) {
            File::delete('upload/assinatura/'.$objComissao->assinatura);
        }
        $objComissao->delete();

        return redirect()->route('comissao.index');

    }

    /**
     * Busca dados de Membro da Comissão
     * @param id int Número USP
     * @return Array
     */
    public function ajaxBuscaDadosComissao(int $id) {
        $arrRetorno = array();
        $vinculos = Pessoa::vinculosSiglas($id);
        $testeV = is_array($vinculos)?array_search("SERVIDOR",$vinculos):false;

        if ($testeV === false) {
            $arrRetorno = ["id"         => ""
                          ,"nome"       => ""
                          ,"email"      => ""
                          ,"telefone"   => ""
                          ,"instituicao"=> ""
                          ,"papel"      => ""
                          ,"vinculos"   => $vinculos];

            return $arrRetorno;
        }

        $dadosPessoa = Pessoa::dump($id);
        $email       = Pessoa::email($id);
        $ramalUsp    = Pessoa::obterRamalUsp($id);
        $ramalUsp    = str_replace("x","",$ramalUsp);
        $ramalUsp    = str_replace("(0","(",$ramalUsp);
        $ramalUsp    = trim(substr($ramalUsp,0,14));
        $setores     = Pessoa::listarVinculosSetores($id,"9");
        $instituicao = null;

        if (count($setores)) {
            $instituicao = "Faculdade de Ciências Farmacêuticas - ".$setores[2];
        }
        
        $arrRetorno = ["id"         => $dadosPessoa["codpes"]
                      ,"nome"       => $dadosPessoa["nompes"]
                      ,"email"      => $email
                      ,"telefone"   => $ramalUsp
                      ,"instituicao"=> $instituicao
                      ,"papel"      => ""
                      ,"vinculos"   => $vinculos];

        return $arrRetorno;
    }

    /**
     * Método para Comissão validar se a defesa ocorreu ou não
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function validaDefesa(Request $request) {
        //dd($request->input('membro'));

        if ($request->filled('membro')) {
            if (!is_array($request->input('membro'))) {
                return Redirect::back()
                            ->withErrors(['msg' => 'Erro ao enviar o formulário'])
                            ->withInput();
            }
            if (count($request->input('membro')) < 3) {
                return Redirect::back()
                            ->withErrors(['msg' => 'É preciso selecionar ao menos 3 membros.'])
                            ->withInput();
            }
        } else {
            return Redirect::back()
                        ->withErrors(['msg' => 'É preciso selecionar ao menos 3 membros.'])
                        ->withInput();
        }

        $txtMensagem = "Prezado [NOME_BANCA],                                                     
        ";
        $txtMensagem.= "Você participou da banca de apresentação do Trabalho de Conclusão de Curso do(a) aluno(a)
        [NOME_ALUNO], título [TITULO], do curso de Farmácia da Faculdade de Ciências Farmacêuticas
        da USP.                                                                               
        ";
        $txtMensagem.= "Você está recebendo o certificado de participação desta banca                                      
        ";
        $txtMensagem.= "Estamos à disposição para qualquer dúvida.                   
        ";
        
        $paramPdf = array();

        $monografia = Monografia::with(['orientadores'])->find($request->input('idMonografia'));
        $defesa = Defesa::select('dataEscolhida')->where('monografia_id',$request->input('idMonografia'))->get();
        $aluno = Aluno::where('monografia_id',$request->input('idMonografia'));
        $comissao = Comissao::where('papel','COORDENADOR')
                            ->where('dtInicioMandato','<=', date_create('now')->format('Y-m-d'))
                            ->where('dtFimMandato', '>=', date_create('now')->format('Y-m-d'))
                            ->get();
        
        if (!$comissao->isEmpty() && is_null($comissao->first()->assinatura)) {
            return Redirect::back()
                        ->withErrors(['msg' => 'A Coordenadora da Comissão de TCC não tem uma assinatura cadastrada. Favor entrar em contato com o Serviço de Graduação.'])
                        ->withInput();
        }
        
        $dataDefesa = date_create($defesa->first()->dataEscolhida);

        foreach ($request->input('membro') as $key=>$membro) {

            $membroBanca = Banca::find($membro);
            $paramPdf = ['nome_membro'        => $membroBanca->nome
                        ,'papel_membro'       => $membroBanca->papel
                        ,'nome_aluno'         => $aluno->first()->nome
                        ,'titulo_trabalho'    => $monografia->titulo
                        ,'data_defesa'        => $dataDefesa->format('d/m/Y')
                        ,'hora_defesa'        => $dataDefesa->format('H:i')
                        ,'nomeCoordenador'    => $comissao->first()->nome
                        ,'pathImageAssinatura'=> public_path()."/upload/assinatura/".$comissao->first()->assinatura
                        ];
            
            $nomeArq = "declaracao_defesa_".$membro."-".date('Y').".pdf";
            
            $declaracao = Pdf::loadView('templates_pdf.declaracao-defesa-banca', $paramPdf);
            $declaracao->save(public_path().'/declaracao_banca/'.$nomeArq);

            $membroBanca->arquivo_declaracao = $nomeArq;
            $membroBanca->update();

            $monografia->status = "AGUARDANDO NOTA DO TCC";
            $monografia->update();

            $body = str_replace("[NOME_BANCA]",$membroBanca->nome,$txtMensagem);
            $body = str_replace("[NOME_ALUNO]",$aluno->first()->nome,$body);
            $body = str_replace("[TITULO]",$monografia->titulo,$body);
            
            EnviarEmailOrientador::dispatch(['email'        => $membroBanca->email
                                            ,'textoMsg'     => $body
                                            ,'assuntoMsg'   => "Certificado de participação em Banca TCC ".$monografia->titulo
                                            ,'nome'         => $membroBanca->nome 
                                            ,'attach'       => public_path()."/declaracao_banca/".$nomeArq
                                            ]);

        }

        $txtMensagem = "A defesa do TCC projeto **".$monografia->titulo."** occoreu em *".$paramPdf['data_defesa']." às ".$paramPdf['hora_defesa']."*              
        ";
        $txtMensagem.= "É preciso informar a média final do TCC no sistema.                                                                                              
        ";
        $txtMensagem.= "**Você tem 3 dias úteis para informar**                                                                                                   
        ";
        foreach($monografia->orientadores()->get() as $orientador) {
            EnviarEmailOrientador::dispatch(['email'        => $orientador->email
                                            ,'textoMsg'     => $txtMensagem
                                            ,'assuntoMsg'   => "Existem notas a serem informadas para o TCC do aluno ".$monografia->alunos->first()->nome
                                            ,'nome'         => $orientador->nome
                                            ]);
        }
        
        return Redirect::back();
        
    }
}
