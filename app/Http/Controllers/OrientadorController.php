<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;

use App\Models\Orientador;
use App\Models\Replicado;
use App\Models\Avaliacao;
use App\Models\Monografia;
use App\Models\MonoOrientadores;
use App\Models\Comissao;
use App\Models\User;
use App\Models\Nota;
use App\Models\Defesa;
use App\Models\Banca;

use Barryvdh\DomPDF\Facade\Pdf;

use App\Rules\verificaCPF;

use Illuminate\Support\Facades\Mail;
use App\Mail\NotificacaoAluno;
use App\Mail\NotificacaoOrientador;

use Uspdev\Replicado\Pessoa;

class OrientadorController extends Controller
{
    private $autenticacao;

    function __construct() {
        if (auth()->check()) {
            $this->autenticacao = auth()->user()->verificaIdentidade();
            //return redirect('home');
        }
    }
    
    /**
     * Display a listing of the resource.
     * Cadastro de Orientador
     *
     * @return \Illuminate\Http\Response
     */
    public function index($msg = null, $paginas = 30)
    {
        if (!auth()->check()) {
            return redirect(route('home'));
        }
        if (!auth()->user()->can('userGraduacao') && !auth()->user()->can('admin') && !auth()->user()->can('userComissao')) {
            return "<script> alert('O1-Sem acesso à esta parte do Sistema. Entre em contato com a Graduação'); 
                             window.location.assign('".route('home')."');
                    </script>";
        }
        if (auth()->user()->can('userComissao')) {
            $orientadores = Orientador::where('aprovado',0)
                                      ->whereNull('nusp_aprovador')
                                      ->orderBy('nome')
                                      ->paginate($paginas);

        } else {
            $orientadores = Orientador::where('nome','like','%')
                                      ->orderBy('aprovado')
                                      ->orderBy('nome')->paginate($paginas);
        }

        if ($orientadores->isEmpty()) {
            $orientadores[] = new Orientador;
        }
        
        $parametros = ["listOrientadores" => $orientadores, "mensagem" => $msg, "paginas" => $paginas];

        return view('cadastro-orientador',$parametros);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!auth()->check()) {
            return view('form-cadastro-orientador-externo');
        } elseif (!auth()->user()->can('userGraduacao') && !auth()->user()->can('admin') && !auth()->user()->can('userAvaliador')) {
            return "<script> alert('CREATE-Sem acesso à esta parte do Sistema. Entre em contato com a Graduação'); 
                             window.location.assign('".route('home')."');
                    </script>";
        }
        return view('form-cadastro-orientador',['readonly' => 0]);
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
        $rules['comprovante_vinculo']= ["file","mimes:application/pdf,jpg,png"];

        if ($request->input('externo')) {
            if ($request->filled('cpfOrientador') && Orientador::where('CPF',$request->input('cpfOrientador'))->count() > 0 && !auth()->check()) {
                if (!auth()->check()) {
                    return "<script>alert('Já existe um registro cadastrado com esse CPF no sistema.'); window.location.assign('".route('orientador.novo-cadastro')."')</script>";
                } else {
                    return "<script>alert('Já existe um registro cadastrado com esse CPF no sistema.'); window.location.assign('".route('home')."')</script>";
                }
            }
            $objRemovido = Orientador::onlyTrashed()->where('CPF',$request->input('cpfOrientador'))->get();

            $rules['cpfOrientador'] = ["required",new verificaCPF];
            $messages['cpfOrientador.required'] = "Favor informar o CPF do Orientador";
            $externo = 1;
            if (!auth()->check()) {
                $rules['instituicaoOrientador'] = ["required","min:3","max:150"];
                $rules['linkLattes']            = ["required","max:255"];
                $rules['area_atuacao']          = ["required","min:3"];
                $rules['comprovante_vinculo']   = ["required","file","mimes:application/pdf,jpg,png"];
            }
        } else {
            $objRemovido = Orientador::onlyTrashed()->where('codpes',$request->input('nuspOrientador'))->get();

            $rules['nuspOrientador'] = "required";
            $messages['nuspOrientador.required'] = "Favor informar o número USP do Orientador";
            $externo = 0;
            
        }

        $rules['nomeOrientador']    = ["required","min:3","max:80"];
        $rules['emailOrientador']   = ["required","min:3", "email"];
        $rules['telefoneOrientador']= ["required","min:10"];
        
        $messages['required']                = "Favor informar o :attribute do orientador.";
        $messages['min']                     = "O :attribute deve conter no mínimo :min caracteres";
        $messages['max']                     = "O :attribute deve conter no mínimo :max caracteres";
        $messages['comprovante_vinculo.file'] = "O arquivo da monografia informado não é válido";
        $messages['comprovante_vinculo.mimes']= "O arquivo deve ser do tipo PDF ou imagem JPG ou PNG";

        $request->validate($rules,$messages);

        //Verficar se o registro existe e deve ser restaurado.
        $atualiza = false;
        if (!$objRemovido->isEmpty()) {
            $objRemovido->first()->restore();
            $orientador = $objRemovido->first();
            $atualiza = true;
        } else {
            if (Orientador::where('email',$request->input('emailOrientador'))->count() > 0) {
                $CollOrientador = Orientador::where('email',$request->input('emailOrientador'))->get();
                $orientador = $CollOrientador->first();
                $atualiza = true;
            } else {
                $orientador = new Orientador;
            }
        }

        $orientador->codpes     = $request->input('nuspOrientador');
        $orientador->nome       = $request->input('nomeOrientador');
        $orientador->email      = $request->input('emailOrientador');
        $orientador->telefone   = $request->input('telefoneOrientador');
        if ($externo == 1) {
            $orientador->aprovado = (!auth()->check())?false:true;
            $orientador->password = password_hash(substr($request->input('cpfOrientador'),0,8), PASSWORD_DEFAULT); //crypt(substr($request->input('cpfOrientador'),0,8));
        } else {
            $orientador->aprovado = true;
        }
        
        if ($request->filled('linkLattes'))
            $orientador->link_lattes = $request->input('linkLattes');
        if ($request->filled('area_atuacao'))
            $orientador->area_atuacao = $request->input('area_atuacao');
        if ($request->filled('instituicaoOrientador'))
            $orientador->instituicao_vinculo = $request->input('instituicaoOrientador');
        
        if ($request->file('comprovante_vinculo')->isValid()) {
            $arquivo = $request->file('comprovante_vinculo');
            $nomesOrient = explode(" ",$request->input('nomeOrientador'));
            $nomeArq = "comprovante_".$nomesOrient[0].$nomesOrient[1].$nomesOrient[2].".".$arquivo->extension();

            if (!$arquivo->move(public_path('upload/orientador'),$nomeArq)) {
                if (!auth()->check()) {
                    return "<script> alert('O26-Erro ao copiar o arquivo do comprovante.'); 
                                            window.location.assign('".route('orientador.novo-cadastro')."');
                            </script>";
                } else {
                    return "<script> alert('O26-Erro ao copiar o arquivo do comprovante.'); 
                                            window.location.assign('".route('home')."');
                            </script>";
                }
            } else {
                $orientador->comprovante_vinculo = $nomeArq;
            }            
        }
        
        if ($atualiza === false) { 
            $orientador->CPF = $request->input('cpfOrientador');
            $orientador->externo = $externo;
            $orientador->save();
        } else {
            $orientador->update();        
        }
        if ($externo == 1) {
            $user = User::updateOrCreate(['name'=>$orientador->nome
                                        ,'email'=>$orientador->email
                                        ,'password'=>$orientador->password
                                        ]);

            if (!$user->hasRole('orientador')) 
                $user->assignRole('orientador');
                            
            if (!$user->can('userOrientador'))
                $user->givePermissionTo('userOrientador');

            //enviar email para o orientador noficando sobre a senha ser os 8 primeiros numeros do CPF
            if (!auth()->check()) {
                $textoMensagem = "O seu cadastro foi realizado no Sistema de Depósito de TCC da Faculdade de Ciências Farmacêuticas da USP.           
                ";
                $textoMensagem.= "Aguarde a aprovação de seu cadastro pela Comissão de TCC.                                               
                ";
                $textoMensagem.= "Caso tennha dúvidas, entre em contato com ctcc.fcf@usp.br                                                
                ";

                $msgComissao = "Orientador ".$orientador->nome." se cadastrou no sistema e aguarda a sua aprovação         
                ";
                /*Mail::to("pcalves@usp.br", "Comissão TCC")
                    ->send(new NotificacaoOrientador($msgComissao,"Cadastro de novo orientador", "Comissão TCC"));*/
                Mail::to("ctcc.fcf@usp.br", "Comissão TCC")
                        ->send(new NotificacaoOrientador($msgComissao,"Cadastro de novo orientador", "Comissão TCC"));

            } else {
                $textoMensagem = "O seu cadastro foi realizado no Sistema de Depósito de TCC da Faculdade de Ciências Farmacêuticas da USP.           
                ";
                $textoMensagem.= "Caso tennha dúvidas, entre em contato com ctcc.fcf@usp.br                                               
                ";
                $textoMensagem.= "Seu login é este e-mail (".$orientador->email.")                                                                                            
                ";
                $textoMensagem.= "Sua senha é ".substr($request->input('cpfOrientador'),0,8)."                               
                ";
            }
            /*Mail::to("pcalves@usp.br", $orientador->nome)
                    ->send(new NotificacaoOrientador($textoMensagem,"Cadastro para acesso ao Sistema de Depósito de TCC - FCF", $orientador->nome));*/
            Mail::to($orientador->email, $orientador->nome)
                ->send(new NotificacaoOrientador($textoMensagem,"Cadastro para acesso ao Sistema de Depósito de TCC - FCF", $orientador->nome));

            if (!auth()->check())
                return "<script>alert('Cadastro realizado com sucesso. Favor aguardar aprovação do cadastro. Foi enviado um e-mail de confirmação.'); window.location.assign('".route('home')."');</script>";

        }
        return redirect(route('orientador.index',['msg'=>"O Orientador  ".$orientador->nome." foi cadastrado com sucesso."]));  
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('userGraduacao') && 
            !auth()->user()->can('userOrientador') &&
            !auth()->user()->can('userAvaliador') && 
            !auth()->user()->can('userComissao') && 
            !auth()->user()->can('admin')) {
            return "<script> alert('O1-Sem acesso à esta parte do Sistema. Entre em contato com a Graduação'); 
                             window.location.assign('".route('home')."');
                    </script>";
        }
        $monografia = new MonografiaController();
        return $monografia->show($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('userGraduacao') && !auth()->user()->can('admin') && !auth()->user()->can('userComissao')) {
            return "<script> alert('EDIT-Sem acesso à esta parte do Sistema. Entre em contato com a Graduação'); 
                             window.location.assign('".route('sair')."');
                    </script>";
        }
        $readonly = 0;
        if (auth()->user()->can('userComissao')) {
            $readonly = 1;
        }
        $objOrientador = Orientador::find($id);
        return view('form-cadastro-orientador', ["objOrientador"=>$objOrientador, "readonly" => $readonly]);
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
        if (!auth()->user()->can('userGraduacao') && !auth()->user()->can('admin')) {
            return "<script> alert('UPDATE-Sem acesso à esta parte do Sistema. Entre em contato com a Graduação'); 
                             window.location.assign('".route('sair')."');
                    </script>";
        }
        $objOrientador = Orientador::find($id);
        $emailAntigo = $objOrientador->email;
        
        $rules = [];
        $rules['nomeOrientador']       = ["required","min:3","max:80"];
        $rules['emailOrientador']      = ["required","min:3", "email"];
        $rules['telefoneOrientador']   = ["required","min:10"];
        $rules['instituicaoOrientador']= ["required","min:3","max:150"];
        $rules['comprovante_vinculo']  = ["file","mimes:application/pdf,jpg,png"];
        
        $messages['required']   = "Favor informar o :attribute do orientador.";
        $messages['min']        = "O :attribute deve conter no mínimo :min caracteres";
        $messages['max']        = "O :attribute deve conter no mínimo :max caracteres";

        $request->validate($rules,$messages);
        
        $objOrientador->nome = $request->input('nomeOrientador');
        $objOrientador->email = $request->input('emailOrientador');
        $objOrientador->telefone = $request->input('telefoneOrientador');
        $objOrientador->instituicao_vinculo = $request->input('instituicaoOrientador');
        $objOrientador->link_lattes = $request->input('linkLattes');
        $objOrientador->area_atuacao = $request->input('area_atuacao');

        if ($request->file('comprovante_vinculo')->isValid()) {
            
            $arquivo = $request->file('comprovante_vinculo');
            $nomeArq = "comprovante_".$request->input('nomeOrientador').".".$arquivo->extension();
            
            if (!$arquivo->move(public_path('upload/orientador/'),$nomeArq)) {
                return "<script> alert('O26-Erro ao copiar o arquivo do comprovante.'); 
                                        window.location.assign('".route('home')."');
                    </script>";
            } else {
                if (!empty($objOrientador->comprovante_vinculo)) {
                    File::delete('upload/orientador/'.$objOrientador->comprovante_vinculo);
                }
                $objOrientador->comprovante_vinculo = $nomeArq;
            }    
        }

        $objOrientador->update();

        if($objOrientador->externo == true) {
            $user = User::where('email',$emailAntigo)->get()->first();
            $user->name = $objOrientador->nome;
            $user->email = $objOrientador->email;
            //$user->password = $objOrientador->password;

            $user->update();
        }

        return redirect(route('orientador.index',['msg'=>"Alteração realizada para orientador ".$objOrientador->nome]));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('userGraduacao') && !auth()->user()->can('admin')) {
            return "<script> alert('DESTROY-Sem acesso à esta parte do Sistema. Entre em contato com a Graduação'); 
                             window.location.assign('".route('sair')."');
                    </script>";
        }

        $objOrientador= Orientador::find($id);
        if ($objOrientador->externo == 1)
            $delUser = User::excluirRegistroByEmail($objOrientador->email);
        
        if (!empty($objOrientador->comprovante_vinculo)) {
            File::delete('upload/orientador/'.$objOrientador->comprovante_vinculo);
        }
                
        $objOrientador->delete();

        return redirect(route('orientador.index',['msg'=>"Exclusão do Orientador ".$objOrientador->nome." realizada"]));

    }

    /**
     * Relação N:N
     */
    public function monoOrientador(){
        $this->belongsToMany('App\Models\Monografia','mono_orientadores');
    }

    /**
     * Listagem de Monografias
     */
    public function listarMonografias() {

        if (!auth()->user()->can('userGraduacao') && 
            !auth()->user()->can('userOrientador') && 
            !auth()->user()->can('userAvaliador') &&
            !auth()->user()->can('admin')) {
            return "<script> alert('LIST-Sem acesso à esta parte do Sistema. Entre em contato com a Graduação'); 
                             window.location.assign('".route('sair')."');
                    </script>";
        }
        
        $Monografias = new MonografiaController();
        if (auth()->user()->can('userOrientador') && !auth()->user()->can('admin')) {
            $dadosOrientador = Orientador::where('email',auth()->user()->email)->get();
            return $Monografias->listMonografia($dadosOrientador->first()->id,date('Y'));
        } else
            return $Monografias->listMonografia(0,date('Y'));
    }

    /**
     * Ajax de busca de dados de Orientador
     * @param id Número USP da pessoa
     */
    public function ajaxBuscaDadosOrientador($id) {
        $arrRetorno = array();
        $vinculos = Pessoa::vinculosSiglas($id);
        $testeV = is_array($vinculos)?array_search("SERVIDOR",$vinculos):false;

        if ($testeV === false) {
            $arrRetorno = ["id"         => ""
                          ,"nome"       => ""
                          ,"email"      => ""
                          ,"telefone"   => ""
                          ,"instituicao"=> ""
                          ,"externo"    => ""
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
        
        $arrRetorno = ["id" => $dadosPessoa["codpes"]
                      ,"nome" => $dadosPessoa["nompes"]
                      ,"email" => $email
                      ,"telefone" => $ramalUsp
                      ,"instituicao" => $instituicao
                      ,"externo" => 0
                      ,"vinculos" => $vinculos];

        return json_encode($arrRetorno);

    }

    /**
     * Busca dados de Orientador por filtro
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getOrientadorByFiltro(Request $request) {
        $listOrientador = Orientador::where('nome','like','%'.$request->input('filtro').'%')
                                    ->orWhere('codpes',$request->input('filtro'))
                                    ->orWhere('CPF','like', '%'.$request->input('filtro').'%')
                                    ->orWhere('email','like','%'.$request->input('filtro').'%')
                                    ->paginate(30);

        if ($listOrientador->isEmpty()) {
            $listOrientador[] = new Orientador;
        }

        $parametros = ["listOrientadores" => $listOrientador
                      ,"mensagem" => null
                      ,"paginas" => 30
                      ,"filtro" => $request->input('filtro')
                      ];

        return view('cadastro-orientador',$parametros);
    }

    /**
     * Método para cadastrar avaliações
     */
    public function salvarAvaliacao(Request $request) {
        
        $rules = ['pareceristaid', ["exists:comissoes,id"]
                 ,'monografiaId', ["exists:monografias,id"]
                 ];
        if ($request->input('acao') == "DEVOLVIDO" || $request->input('acao') == "REPROVADO") {
            $rules["parecer"] = ["required","min:10","max:60000"]; 
        } /*elseif ($request->input('acao') == "APROVADO") {
            $rules["publicar"] = ["required"];
            $messages['publicar.required'] = "Precisa ser informado se o trabalho deverá ou não ser publicado";
        }*/

        $messages['required']            = "Favor informar o :attribute.";
        $messages['min']                 = "O :attribute deve conter no mínimo :min caracteres";
        $messages['max']                 = "O :attribute deve conter no máximo :max caracteres";
        $messages['exists.pareceristaid']= "O Orientador precisa estar cadastrado";
        $messages['exists.monografiaId'] = "A Monografia precisa estar cadastrada";

        $request->validate($rules,$messages);

        $avaliacao = new Avaliacao;
        $avaliacao->comissoes_id = $request->input('pareceristaid');
        $avaliacao->monografia_id= $request->input('monografiaId');
        $avaliacao->dataAvaliacao= date_create('now');
        $avaliacao->status       = $request->input('acao');
        $avaliacao->parecer      = $request->input('parecer');
        $avaliacao->save();

        $dadosMonografia = Monografia::where('id',$request->input('monografiaId'))->get();
        $dadosOrientador = Comissao::find($request->input('pareceristaid'));
        $monografia      = $dadosMonografia->first();

        if ($request->has('publicar')) {
            $monografia->publicar = $request->input('publicar');
        } 
        
        $avExcl = Avaliacao::where("status","AGUARDANDO")
                           ->where("monografia_id",$request->input('monografiaId'))
                           ->where("comissoes_id",$request->input('pareceristaid'))
                           ->get();
        
        if (!$avExcl->isEmpty()) {
            $del = Avaliacao::excluirRegistro($avExcl->first()->id);
        }

        //enviar e-mail
        $textoMensagem = null;
        $assunto = null;
        $assuntoMsg = null;
        if ($request->input('acao') == "DEVOLVIDO") {
            $monografia->status = "AGUARDANDO CORREÇÃO DO PROJETO";

            $assunto = "Correção do projeto solicitada pela Comissão";
            
            $textoMensagem = "O projeto de TCC título **".$dadosMonografia->first()->titulo."** tem uma correção a ser realizada.           
            ";
            $textoMensagem.= "**Correção a ser realizada:** *".$request->input('parecer')."*                                                              
            ";
            $textoMensagem.= "**Você tem 3 dias úteis para atender as recomendações apontadas**                                                               
            ";
            $textoMensagem.= "Clique no botão abaixo para acessar o sistema e efetuar a correção.                         
            ";

            $assuntoMsg = "Projeto de TCC com título ".$dadosMonografia->first()->titulo." foi enviado para o aluno para correção.";
            $txtMsgOrientador = "O projeto de TCC título **".$dadosMonografia->first()->titulo."** tem uma correção a ser realizada pelo aluno.           
            ";
            $txtMsgOrientador.= "**Correção a ser realizada:** *".$request->input('parecer')."*                                                              
            ";
            $txtMsgOrientador.= "**Não é necessária nenhuma ação sua, o aluno precisa corrigir o projeto, este e-mail é somente para ciência**                                                               
            ";

        } elseif ($request->input('acao') == "REPROVADO") {
            $monografia->status = ""; // neste projeto não está previsto reprovação
            
            $textoMensagem = "A projeto de TCC título **".$dadosMonografia->first()->titulo."** foi reprovada.             
            ";
            $textoMensagem.= "**MOTIVO:** *".$request->input('parecer')."*                       
            ";
        } elseif ($request->input('acao') == "APROVADO") {
            if ($monografia->curriculo == 9012) {
                $monografia->status = "AGUARDANDO ARQUIVO TCC";
            } elseif ($monografia->curriculo == 9013) {
                $monografia->status = "AGUARDANDO NOTA DO PROJETO";
            }

            $assunto = "Projeto TCC aprovado";

            $textoMensagem = "**PARABÉNS!!!**                                                            
            ";
            $textoMensagem.= "O projeto TCC título **".$dadosMonografia->first()->titulo."** foi aprovada.         
            ";
            $textoMensagem.= "Aguarde a abertura do sistema para anexar o arquivo de TCC e indicação da banca                     
            ";    
            
            $assuntoMsg = "Projeto de TCC com título ".$dadosMonografia->first()->titulo." foi aprovado pela Comissão.";
            $txtMsgOrientador = "O projeto de TCC título **".$dadosMonografia->first()->titulo."** foi aprovado pela comissão.           
            ";
            
            if ($dadosMonografia->first()->curriculo == 9013) {
                $txtMsgOrientador.= "**Entre no sistema para informar a nota e a frequencia do projeto**                                                               
                ";
            } else {
                $txtMsgOrientador.= "**Não é necessária nenhuma ação, este e-mail é somente para ciência**                                                               
                ";
            }
        }
        $monografia->update();

        if (!empty($textoMensagem)) {
            foreach($dadosMonografia->first()->alunos()->get() as $key=>$aluno) {
                $email = Pessoa::emailusp($aluno->id);
                
                /*Mail::to("pcalves@usp.br", $aluno->nome, $assunto)
                    ->send(new NotificacaoAluno($textoMensagem,$aluno->nome));*/
                Mail::to($email, $aluno->nome, $assunto)
                    ->send(new NotificacaoAluno($textoMensagem,$aluno->nome));
            }

            foreach($dadosMonografia->first()->orientadores()->get() as $orientador) {
                /*Mail::to("pcalves@usp.br", $orientador->nome)
                    ->send(new NotificacaoOrientador($txtMsgOrientador, $assuntoMsg, $orientador->nome));*/
                Mail::to($orientador->email, $orientador->nome)
                      ->send(new NotificacaoOrientador($txtMsgOrientador, $assuntoMsg, $orientadores->nome));
            }
        }
        
        return redirect()->route('graduacao.edicao', ['idMono'=> $dadosMonografia->first()->id]);
        
    }

    /**
     * Método para abrir formulário de Avaliação
     * @param int idMonografia Id da Monografia cadastrada
     * @param string acao Ação a ser realizada no TCC (editar, avaliar, etc.)
     */
    public function avaliacao($idMonografia, $acao) {

        if (!auth()->user()->can('userAvaliador') ) {
            return "<script> alert('O2-Sem acesso à esta parte do Sistema. Entre em contato com a Graduação'); 
                             window.location.assign('".route('home')."');
                    </script>";
        }
        
        $monografia = new MonografiaController();
        if (Avaliacao::where("monografia_id",$idMonografia)->where("status","DEVOLVIDO")->count() > 0) {
            return $monografia->index($idMonografia,date('Y'),"Aguarde a correção da última avaliação.");
        } elseif (Avaliacao::where("monografia_id",$idMonografia)->where("status","APROVADO")->count() > 0 ||
                  Avaliacao::where("monografia_id",$idMonografia)->where("status","REPROVADO")->count() > 0
                 ) 
        {
            return $monografia->index($idMonografia,date('Y'),"Monografia concluída.");
        } else {
            $orientador = new Orientador;
            if (!empty(auth()->user()->codpes)) 
                $dadosOrientador = Orientador::where('codpes',auth()->user()->codpes)->get();
            else
                $dadosOrientador = Orientador::where('email',auth()->user()->email)->get();

            $dadosOrientador = $orientador->listOrientador($dadosOrientador->first()->id, $idMonografia);
            
            if($dadosOrientador->first()->principal == 1)
                return $monografia->index($idMonografia,date('Y'),null,$acao);
            else {
                print "<script>alert('Somente o Orientador principal pode realizar a avaliação.');</script>";
                return $monografia->index($idMonografia,date('Y'),"Somente o Orientador principal pode realizar a avaliação.");
            }
        }
    }

    /**
     * Aprovação de cadastro de orientador externo
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function aprovarCadastro($id,bool $aprovacao) {

        $textoMensagem = null;
        $assunto = null;
        $orientador = Orientador::find($id);

        if (!isset($orientador->nome)) {
            return "<script> alert('011-Erro ao buscar o cadastro de Orientador'); 
                             window.location.assign('".route('home')."');
                    </script>";
        }
        $orientador->aprovado = $aprovacao;
        $orientador->nusp_aprovador = auth()->user()->codpes;
        $orientador->update();

        if ($aprovacao) {
            $textoMensagem = "O seu cadastro foi aprovado no Sistema de Depósito de TCC da Faculdade de Ciências Farmacêuticas da USP.           
            ";
            $textoMensagem.= "Caso tennha dúvidas, entre em contato com ctcc.fcf@usp.br                                               
            ";
            $textoMensagem.= "Seu login é este e-mail (".$orientador->email.")                                                                                            
            ";
            $textoMensagem.= "Sua senha é ".substr($orientador->CPF,0,8)."                               
            ";
            $assunto = "APROVADO";
        } else {
            $textoMensagem = "O seu cadastro foi REPROVADO no Sistema de Depósito de TCC da Faculdade de Ciências Farmacêuticas da USP.           
            ";
            $textoMensagem.= "Caso tennha dúvidas, entre em contato com ctcc.fcf@usp.br                                               
            ";
            $assunto = "REPROVADO";
        }

        $presidenteComissao = Comissao::where('dtInicioMandato','<=', date_create('now')->format('Y-m-d'))
                                        ->where('dtFimMandato', '>=', date_create('now')->format('Y-m-d'))
                                        ->where('papel','PRESIDENTE')
                                        ->get();
        
        
        /*Mail::to("pcalves@usp.br", $orientador->nome)
              ->send(new NotificacaoOrientador($textoMensagem,"Cadastro $assunto para acesso ao Sistema de Depósito de TCC - FCF", $orientador->nome));*/
        Mail::to($orientador->email, $orientador->nome)
            ->send(new NotificacaoOrientador($textoMensagem,"Cadastro $assunto para acesso ao Sistema de Depósito de TCC - FCF", $orientador->nome));
        /*Mail::to($presidenteComissao->first()->email, $presidenteComissao->first()->nome)
                ->send(new NotificacaoOrientador($textoMensagem,"Cadastro para acesso ao Sistema de Depósito de TCC - FCF", $presidenteComissao->first()->nome));*/

        print '<script>alert("Orientador '.$orientador->nome.' '.$assunto.' com sucesso."); </script>';
        return redirect()->route('orientador.index');

    }

    /**
     * Informa as notas de proejeto ou de TCC
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function informaNota(Request $request) {

        $rule     = [];
        $messages = [];

        $monografia = Monografia::with(['orientadores','alunos'])->find($request->input('idTccNota'));
        if ($monografia->status == "AGUARDANDO NOTA DO PROJETO") {
            $rule['projeto_nota'] = ['required'];
            $rule['projeto_freq'] = ['required','integer'];
        }
        if ($monografia->status == "AGUARDANDO NOTA DO TCC") {
            $rule['tcc_nota'] = ['required'];
            $rule['tcc_freq'] = ['required','integer'];
            $rule['publicar'] = ['required'];
        }

        $messages['required'] = "Favor informar o :attribute.";
        $messages['integer']  = "O campo :attribute deve ser um número inteiro";
        $messages['numeric']  = "O campo :attribute deve ser numérico";

        $notaProjeto = new Nota();
        $notaProjeto->monografia_id = $monografia->id;

        $request->validate($rule,$messages);

        if ($request->filled('projeto_nota') && $monografia->status == "AGUARDANDO NOTA DO PROJETO") {
            
            $notaProjeto->tipo_nota     = 'PROJETO';
            $notaProjeto->frequencia    = ($request->input('projeto_freq') >100) ? 100 : $request->input('projeto_freq');
            $notaProjeto->nota          = str_replace(",",".",$request->input('projeto_nota'));

            $notaProjeto->nota = ( $notaProjeto->nota > 10)? 10 : $notaProjeto->nota;

            if ($notaProjeto->save()) {
                $monografia->status = "AGUARDANDO ARQUIVO TCC";
                $monografia->update();

                $aluno = $monografia->alunos()->get();

                //Envio de e-mail para todos os Orientadores
                $textoOrientador = "Você registrou a nota para o projeto abaixo                                           
                ";
                $textoOrientador.= "Aluno: ".$aluno->first()->nome."                                                                           
                ";
                $textoOrientador.= "Titulo do Projeto: ".$monografia->titulo."                                                        
                ";
                $textoOrientador.= "Nota: ".$request->input('projeto_nota')." / Frequencia: ".$request->input('projeto_freq')."                                                
                ";
                $textoOrientador.= "**Nenhuma ação é necessária, este é somente um e-mail informativo**         
                ";
                foreach($monografia->orientadores()->get() as $orientador) {

                    /*Mail::to("pcalves@usp.br", $orientador->nome)
                        ->send(new NotificacaoOrientador($textoOrientador,"Registrada Nota do Projeto",$orientador->nome));*/
                    Mail::to($orientador->email, $orientador->nome)
                        ->send(new NotificacaoOrientador($textoOrientador,"Registrada Nota do Projeto",$orientador->nome));

                }

                //Envio de e-mail para todos os Alunos
                $textoAluno = "Prezado ".$aluno->first()->nome."                                                         
                ";
                $textoAluno.= "O orientador registrou a nota para seu projeto                                           
                ";
                $textoAluno.= "Titulo do Projeto: ".$monografia->titulo."                                                        
                ";
                $textoAluno.= "Nota: ".$request->input('projeto_nota')." / Frequencia: ".$request->input('projeto_freq')."                                                
                ";
                $textoAluno.= "**Nenhuma ação é necessária, este é somente um e-mail informativo**         
                ";
                
                foreach($aluno as $dAluno) {
                    $emailAluno = Pessoa::emailusp($dAluno->id);
                    /*Mail::to("pcalves@usp.br", $dAluno->nome)
                        ->send(new NotificacaoAluno($textoAluno,$dAluno->nome,"Registro de Nota do Projeto"));*/
                    Mail::to($emailAluno, $dAluno->nome)
                        ->send(new NotificacaoAluno($textoAluno,$dAluno->nome,"Registro de Nota do Projeto"));
                }
            }
        }

        if ($request->filled('tcc_nota') && $monografia->status == "AGUARDANDO NOTA DO TCC") {
            
            $notaProjeto->tipo_nota     = 'TCC';
            $notaProjeto->frequencia    = $request->input('tcc_freq');
            $notaProjeto->nota          = '0'.str_replace(",",".",$request->input('tcc_nota')).'0';

            if ($notaProjeto->save()) {
                $monografia->status = "CONCLUIDO";
                $monografia->publicar = $request->input('publicar');
                $monografia->update();

                $aluno = $monografia->alunos()->get();

                //Envio de e-mail para todos os Orientadores
                $textoOrientador = "Você registrou a nota para a aprensentação do TCC do projeto abaixo                                           
                ";
                $textoOrientador.= "Aluno: ".$aluno->first()->nome."                                                                           
                ";
                $textoOrientador.= "Titulo do Projeto: ".$monografia->titulo."                                                        
                ";
                $textoOrientador.= "Nota: ".$request->input('projeto_nota')." / Frequencia: ".$request->input('projeto_freq')."                                                
                ";
                $textoOrientador.= "**Nenhuma ação é necessária, este é somente um e-mail informativo**         
                ";
                foreach($monografia->orientadores()->get() as $orientador) {

                    /*Mail::to("pcalves@usp.br", $orientador->nome)
                        ->send(new NotificacaoOrientador($textoOrientador,"Registrada Nota do Projeto",$orientador->nome));*/
                    Mail::to($orientador->email, $orientador->nome)
                        ->send(new NotificacaoOrientador($textoOrientador,"Registrada Nota do Projeto",$orientador->nome));

                }

                //Envio de e-mail para todos os Alunos
                $textoAluno = "Prezado ".$aluno->first()->nome."                                                         
                ";
                $textoAluno.= "O orientador registrou a nota para sua apresentação de TCC.                                           
                ";
                $textoAluno.= "Titulo do Projeto: ".$monografia->titulo."                                                        
                ";
                $textoAluno.= "Nota: ".$request->input('projeto_nota')." / Frequencia: ".$request->input('projeto_freq')."                                                
                ";
                $textoAluno.= "**Nenhuma ação é necessária, este é somente um e-mail informativo**         
                ";
                foreach($aluno as $dAluno) {
                    $emailAluno = Pessoa::emailusp($dAluno->id);
                    /*Mail::to("pcalves@usp.br", $dAluno->nome)
                        ->send(new NotificacaoAluno($textoAluno,$dAluno->nome,"Registrada Nota para Apresentação de TCC"));*/
                    Mail::to($emailAluno, $dAluno->nome)
                        ->send(new NotificacaoAluno($textoAluno,$dAluno->nome,"Registrada Nota para Apresentação de TCC"));
                }

                if ($notaProjeto->nota >= 5) {
                    $resultado = "APROVADO";
                } else {
                    $resultado = "REPROVADO";
                }

                $defesa = Defesa::where('monografia_id',$monografia->id)->get();
                $dataDefesa = date_create($defesa->first()->dataEscolhida);

                $paramPdfRel =  ['nome_aluno'       => $aluno->first()->nome
                                ,'nusp_aluno'       => $aluno->first()->id
                                ,'nome_orientador'  => $monografia->orientadores->first()->nome
                                ,'nusp_orientador'  => $monografia->orientadores->first()->codpes
                                ,'titulo_monografia'=> $monografia->titulo
                                ,'data_defesa'      => $dataDefesa->format('d/m/Y')
                                ,'hora_defesa'      => $dataDefesa->format('H:i')
                                ,'local'            => 'SALA GOOGLE MEET'
                                ,'media'            => $notaProjeto->nota
                                ,'frequencia'       => $notaProjeto->frequencia."%"
                                ,'resultado'        => $resultado
                                ,'publica'          => $request->input('publicar')];

                $banca = Banca::where('monografia_id',$monografia->id)
                              ->whereNotNull('arquivo_declaracao')
                              ->orderBy('ordem')
                              ->get();
                
                foreach($banca as $key=>$objBanca) {
                    if ($key == 0) {
                        if (!empty($objBanca->codpes)) {
                            $paramPdfRel['banca1'] = $objBanca->codpes." ".$objBanca->nome;
                        } else {
                            $paramPdfRel['banca1'] = $objBanca->nome."(".$objBanca->instituicao_vinculo.")";
                        }
                    }
                    if ($key == 1) {
                        if (!empty($objBanca->codpes)) {
                            $paramPdfRel['banca2'] = $objBanca->codpes." ".$objBanca->nome;
                        } else {
                            $paramPdfRel['banca2'] = $objBanca->nome."(".$objBanca->instituicao_vinculo.")";
                        }
                    }
                    if ($key == 2) {
                        if (!empty($objBanca->codpes)) {
                            $paramPdfRel['banca3'] = $objBanca->codpes." ".$objBanca->nome;
                        } else {
                            $paramPdfRel['banca3'] = $objBanca->nome."(".$objBanca->instituicao_vinculo.")";
                        }
                    }
                }
                
                $nomeArq = "relatorio_defesa_tcc_".$aluno->first()->id.".pdf";

                $relatorio = Pdf::loadView('templates_pdf.relatorio-defesa-tcc', $paramPdfRel);
                $relatorio->setPaper('a4', 'portrait')->save(public_path()."/upload/".$nomeArq);

                $body = "O orientador registrou a nota para o TCC.                                           
                ";
                $textoAluno.= "Titulo do Projeto: ".$monografia->titulo."                                                        
                ";
                $textoAluno.= "Nota: ".$request->input('projeto_nota')." / Frequência: ".$request->input('projeto_freq')."                                                
                ";
                $textoAluno.= "**Anexo relatório de Defesa do TCC**         
                ";

                /*Mail::to("pcalves@usp.br", "Comissão TCC")
                    ->send(new NotificacaoOrientador($body, "Relatório de Defesa TCC titulo ".$monografia->titulo, "Comissão TCC", public_path()."/upload/".$nomeArq));*/
                Mail::to("ctcc.fcf@usp.br", "Comissão TCC")
                    ->send(new NotificacaoOrientador($body, "Relatório de Defesa TCC titulo ".$monografia->titulo, "Comissão TCC", public_path()."/upload/".$nomeArq));

                File::delete(public_path()."/upload/".$nomeArq);
            }
        }
        return redirect()->route('orientador.edicao', ['idMono'=> $monografia->id]);

    }

    /**
     * Aprovação de banca indicada pelo aluno
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response 
     */
    public function aprovaBanca(Request $request) {

        $rule['aprovacao_orientador_banca'] = "required";
        $message['required'] = "Favor informar o :attribute.";

        $request->validate($rule,$message);

        $defesa = Defesa::where('monografia_id',$request->input('monografiaId'))->get();
        $defesa->first()->aprovacao_orientador = $request->input('aprovacao_orientador_banca');
        $defesa->first()->update();

        $monografia = Monografia::with(['orientadores','alunos'])
                                ->find($request->input('monografiaId'));

        $txtMensagem = null;
        $assunto = null;
        $emailDestino = null;
        $nomeDestino = null;

        if ($request->input('aprovacao_orientador_banca')) {
            //E-mail para Comissão para validar defesa
            $assunto = "A banca foi validada pelo Orientador, favor verificar a data da Defesa";
            $txtMensagem = "A banca para o projeto abaixo foi validada pelo Orientador           
            ";
            $txtMensagem.= "**Titulo:** ".$monografia->titulo."                                  
            ";
            $txtMensagem.= "**Aluno:** ".$monografia->alunos->first()->nome."                    
            ";
            $txtMensagem.= "**Por favor, entre no sistema e informe a melhor data para a defesa**
            ";
            $emailDestino = "ctcc.fcf@usp.br";
            $nomeDestino = "Comissão de TCC";
        } else {
            //E-mail para aluno corrigir a banca
            $assunto = "Orientador não está de acordo com a Banca sugerida.";
            $txtMensagem = "A banca sugerida para o projeto título ".$monografia->titulo." não foi aprovada pelo Orientador.   
            ";
            $txtMensagem.= "**Orientador Responsável:** ".$monografia->orientadores->first()->nome."                           
            ";
            $txtMensagem.= "**Orientações:** ".$request->input('correcao_banca')."                                             
            "; // Orientações de correção da banca para o aluno
            $txtMensagem.= "**Favor entrar no sistema e corrigir as informações como orientado com a maior brevidade possível**
            ";
            $emailDestino = Pessoa::emailusp($monografia->alunos->first()->id);
            $nomeDestino = $monografia->alunos->first()->nome;
        }

        /*Mail::to("pcalves@usp.br", $nomeDestino)
            ->send(new NotificacaoAluno($txtMensagem,$nomeDestino,$assunto));*/
        Mail::to($emailDestino, $nomeDestino)
              ->send(new NotificacaoAluno($txtMensagem,$nomeDestino,$assunto));

        print "<script>alert('A banca foi ".(($request->input('aprovacao_orientador_banca'))?'APROVADA':'REPROVADA')." um e-mail foi enviado para $nomeDestino para as providências necessárias'); </script>";
        
        return redirect()->route('orientador.edicao', ['idMono'=> $request->input('monografiaId')]);
        
    }

}
