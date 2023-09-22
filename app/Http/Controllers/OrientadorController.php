<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use App\Models\Orientador;
use App\Models\Replicado;
use App\Models\Avaliacao;
use App\Models\Monografia;
use App\Models\MonoOrientadores;
use App\Models\Comissao;
use App\Models\User;

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
        if (!auth()->user()->can('userGraduacao') && !auth()->user()->can('admin')) {
            return "<script> alert('O1-Sem acesso à esta parte do Sistema. Entre em contato com a Graduação'); 
                             window.location.assign('".route('home')."');
                    </script>";
        }
        $orientadores = Orientador::where('nome','like','%')->orderBy('nome')->paginate($paginas);

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
        return view('form-cadastro-orientador');
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
        if ($request->input('externo')) {
            if (Orientador::where('CPF',$request->input('cpfOrientador'))->count() > 0 && !auth()->check()) {
                return "<script>alert('Já existe um registro cadastrado com esse CPF no sistema.'); window.location('".route('home')."')</script>";
            }

            $objRemovido = Orientador::onlyTrashed()->where('CPF',$request->input('cpfOrientador'))->get();

            $rules['cpfOrientador'] = ["required",new verificaCPF];
            $messages['cpfOrientador.required'] = "Favor informar o CPF do Orientador";
            $externo = 1;
            if (!auth()->check()) {
                $rules['instituicaoOrientador'] = ["required","min:3","max:150"];
                $rules['linkLattes']            = ["required","max:255"];
                $rules['areaAtuacao']           = ["required","min:3"];
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
        
        $messages['required']   = "Favor informar o :attribute do orientador.";
        $messages['min']        = "O :attribute deve conter no mínimo :min caracteres";
        $messages['max']        = "O :attribute deve conter no mínimo :max caracteres";

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
            $orientador->password = crypt(substr($request->input('cpfOrientador'),0,8),"$5&jj");
        } else {
            $orientador->aprovado = true;
        }
        
        if ($request->filled('linkLattes'))
            $orientador->link_lattes = $request->input('linkLattes');
        if ($request->filled('areaAtuacao'))
            $orientador->area_atuacao = $request->input('areaAtuacao');
        if ($request->filled('instituicaoOrientador'))
            $orientador->instituicao_vinculo = $request->input('instituicaoOrientador');
        
        if ($atualiza === false) { 
            $orientador->CPF = $request->input('cpfOrientador');
            $orientador->externo = $externo;
            $orientador->save();
        } else {
            $orientador->update();        
        }
        if ($externo == 1) {
            $user = User::create(['name'=>$orientador->nome
                                ,'email'=>$orientador->email
                                ,'password'=>$orientador->password]
                                );

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
            } else {
                $textoMensagem = "O seu cadastro foi realizado no Sistema de Depósito de TCC da Faculdade de Ciências Farmacêuticas da USP.           
                ";
                $textoMensagem.= "Caso tennha dúvidas, entre em contato com ctcc.fcf@usp.br                                               
                ";
                $textoMensagem.= "Seu login é este e-mail (".$orientador->email.")                                                                                            
                ";
                $textoMensagem.= "Sua senha é ".substr($orientador->CPF,0,8)."                               
                ";
            }
            Mail::to("pcalves@usp.br", $orientador->nome)
                    ->send(new NotificacaoOrientador($textoMensagem,"Cadastro para acesso ao Sistema de Depósito de TCC - FCF", $orientador->nome));
            /*Mail::to($orientador->email, $orientador->nome)
                    ->send(new NotificacaoOrientador($textoMensagem,"Cadastro para acesso ao Sistema de Depósito de TCC - FCF", $orientador->nome));*/

            print "<script>alert('Cadastro realizado com sucesso. Favor aguardar aprovação do cadastro. Foi enviado um e-mail de confirmação.');</script>";

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
        if (!auth()->user()->can('userGraduacao') && !auth()->user()->can('admin')) {
            return "<script> alert('EDIT-Sem acesso à esta parte do Sistema. Entre em contato com a Graduação'); 
                             window.location.assign('".route('sair')."');
                    </script>";
        }
        $objOrientador = Orientador::find($id);
        return view('form-cadastro-orientador', ["objOrientador"=>$objOrientador]);
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
        
        $messages['required']   = "Favor informar o :attribute do orientador.";
        $messages['min']        = "O :attribute deve conter no mínimo :min caracteres";
        $messages['max']        = "O :attribute deve conter no mínimo :max caracteres";

        $request->validate($rules,$messages);
        
        $objOrientador->nome = $request->input('nomeOrientador');
        $objOrientador->email = $request->input('emailOrientador');
        $objOrientador->telefone = $request->input('telefoneOrientador');
        $objOrientador->instituicao_vinculo = $request->input('instituicaoOrientador');
        $objOrientador->link_lattes = $request->input('linkLattes');
        $objOrientador->area_atuacao = $request->input('areaAtuacao');

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
            $rules["parecer"] = ["required","min:10","max:255"]; 
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
        if ($request->input('acao') == "DEVOLVIDO") {
            $monografia->status = "AGUARDANDO CORREÇÃO DO PROJETO";
            
            $textoMensagem = "O projeto de TCC título **".$dadosMonografia->first()->titulo."** tem uma correção a ser realizada.           
            ";
            $textoMensagem.= "Avaliador responsável: **".$dadosOrientador->nome. "**                                        
             ";
            $textoMensagem.= "**Correção a ser realizada:** *".$request->input('parecer')."*                                                              
            ";
            $textoMensagem.= "**Você tem 3 dias úteis para atender as recomendações apontadas**                                                               
            ";
            $textoMensagem.= "Clique no botão abaixo para acessar o sistema e efetuar a correção.                         
            ";
        } elseif ($request->input('acao') == "REPROVADO") {
            $monografia->status = ""; // neste projeto não está previsto reprovação
            
            $textoMensagem = "A projeto de TCC título **".$dadosMonografia->first()->titulo."** foi reprovada.             
            ";
            $textoMensagem.= "Avaliador responsável: **".$dadosOrientador->nome."**                      
            ";
            $textoMensagem.= "**MOTIVO:** *".$request->input('parecer')."*                       ";
        } elseif ($request->input('acao') == "APROVADO") {
            $monografia->status = "AGUARDANDO ARQUIVO TCC";

            $textoMensagem = "**PARABÉNS!!!**                                                            
            ";
            $textoMensagem.= "O projeto TCC título **".$dadosMonografia->first()->titulo."** foi aprovada.         
            ";
            $textoMensagem.= "Avaliador responsável: **".$dadosOrientador->nome."**                   
            ";
            $textoMensagem.= "Aguarde a abertura do sistema para anexar o arquivo de TCC                     
            ";            
        }
        $monografia->update();

        if (!empty($textoMensagem)) {
            foreach($dadosMonografia->first()->alunos()->get() as $key=>$aluno) {
                $email = Pessoa::emailusp($aluno->id);
                
                Mail::to("pcalves@usp.br", $aluno->nome)
                    ->send(new NotificacaoAluno($textoMensagem,$aluno->nome));
                /*Mail::to($email, $aluno->nome)
                    ->send(new NotificacaoAluno($textoMensagem,$aluno->nome));*/
            }
        }

        return redirect()->route('orientador.edicao', ['idMono'=> $dadosMonografia->first()->id]);
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
        
        Mail::to("pcalves@usp.br", $orientador->nome)
              ->send(new NotificacaoOrientador($textoMensagem,"Cadastro $assunto para acesso ao Sistema de Depósito de TCC - FCF", $orientador->nome));
        /*Mail::to($orientador->email, $orientador->nome)
                ->send(new NotificacaoOrientador($textoMensagem,"Cadastro para acesso ao Sistema de Depósito de TCC - FCF", $orientador->nome));*/

        print '<script>alert("Orientador '.$orientador->nome.' '.$assunto.' com sucesso."); </script>';
        return redirect()->route('orientador.index');

    }

}
