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

use Illuminate\Support\Facades\Mail;
use App\Mail\NotificacaoOrientador;

use Uspdev\Replicado\Pessoa;

use App\Rules\verificaBanca;

class ComissaoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(String $msg = null)
    {
        $listComissao = Comissao::all();
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
        $rule = ["nuspComissao" => ["required","numeric"]
                ,"nomeComissao" => ["required","min:3","max:100"]
                ,"emailComissao"=> ["required","email","max:100"]
                ,"papelComissao"=> ["required"]
                ,"assinatura"   => ["image","file"]
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
        
        $objComissao = new Comissao;
        $objComissao->codpes = $request->input('nuspComissao');
        $objComissao->nome = $request->input('nomeComissao');
        $objComissao->email = $request->input('emailComissao');
        $objComissao->papel = $request->input('papelComissao');
        $objComissao->assinatura = ($upload)?$arquivo->getClientOriginalName():null;
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
        $rule = ["nomeComissao" => ["required","min:3","max:100"]
                ,"emailComissao"=> ["required","email","max:100"]
                ,"papelComissao"=> ["required"]
                ,"assinatura"   => ["image","file"]
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

        $objComissao = Comissao::find($id);
        $objComissao->nome = $request->input('nomeComissao');
        $objComissao->email = $request->input('emailComissao');
        $objComissao->papel = $request->input('papelComissao');
        if ($upload) {
            if (!empty($objComissao->assinatura) && 
                $arquivo->getClientOriginalName() != $objComissao->assinatura) 
            {
                File::delete('upload/assinatura/'.$objComissao->assinatura);
            }
            $objComissao->assinatura = $arquivo->getClientOriginalName();
        }
        $objComissao->update();

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
                return Redirect::back()->withErrors(['msg' => 'Erro ao enviar o formulário']);
            }
            if (count($request->input('membro')) < 3) {
                return Redirect::back()->withErrors(['msg' => 'É preciso selecionar ao menos 3 membros.']);
            }
        } else {
            return Redirect::back()->withErrors(['msg' => 'É preciso selecionar ao menos 3 membros.']);
        }

        $txtMensagem = "Prezado [NOME_BANCA],                                                     
        ";
        $txtMensagem.= "Você está participou da banca de apresentação do Trabalho de Conclusão de Curso do(a) aluno(a)
        [NOME_ALUNO], título [TITULO], do curso de Farmácia da Faculdade de Ciências Farmacêuticas
        da USP.                                                                               
        ";
        $txtMensagem.= "Você está recebendo o certificado de participação desta banca                                      
        ";
        $txtMensagem.= "Estamos à disposição para qualquer dúvida.                   
        ";
        
        foreach ($request->input('membro') as $membro) {

            $membroBanca = Banca::find($membro);
            $monografia = Monografia::find($membroBanca->monografia_id);
            $defesa = Defesa::select('dataEscolhida')->where('monografia_id',$monografia->id)->get();
            $aluno = Aluno::where('monografia_id',$monografia->id);

            $dataDefesa = date_create($defesa->first()->dataEscolhida);

            $paramPdf = ['nome_membro'     => $membroBanca->nome
                        ,'papel_membro'    => $membroBanca->papel
                        ,'nome_aluno'      => $aluno->first()->nome
                        ,'titulo_trabalho' => $monografia->titulo
                        ,'data_defesa'     => $dataDefesa->format('d/m/Y')
                        ,'hora_defesa'     => $dataDefesa->format('H:i')];
            
            $nomeArq = "decaracao_defesa_".$membro."-".date('Y').".pdf";

            $declaracao = Pdf::loadView('templates_pdf.declaracao-defesa-banca', $paramPdf);
            $declaracao->save(public_path()."/declaracao_banca/".$nomeArq);

            $membroBanca->arquivo_declaracao = $nomeArq;
            $membroBanca->update();

            $monografia->status = "CONCLUIDO";
            $monografia->update();

            $body = str_replace("[NOME_BANCA]",$membroBanca->nome,$txtMensagem);
            $body = str_replace("[NOME_ALUNO]",$aluno->first()->nome,$body);
            $body = str_replace("[TITULO]",$monografia->titulo,$body);
            
            Mail::to("pcalves@usp.br", $membroBanca->nome)
                    ->send(new NotificacaoOrientador($body, "[".config('app.name')."] Certificado de participação em Banca TCC ".$monografia->titulo, $membroBanca->nome, public_path()."/declaracao_banca/".$nomeArq));
            /*Mail::to($membroBanca->email, $membroBanca->nome)
                  ->send(new NotificacaoOrientador($body, "[".config('app.name')."] Certificado de participação em Banca TCC ".$monografia->titulo, $membroBanca->nome, public_path()."/declaracao_banca/".$nomeArq));*/

        }

        return Redirect::back();
        
    }
}
