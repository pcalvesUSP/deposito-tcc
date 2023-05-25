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
use App\Models\User;

use Illuminate\Support\Facades\Mail;
use App\Mail\NotificacaoAluno;
use App\Mail\NotificacaoOrientador;

use Uspdev\Replicado\Pessoa;

class OrientadorController extends Controller
{
    private $autenticacao;

    function __construct() {
        if (!auth()->check()) {
            return redirect('home');
        }

        $this->autenticacao = auth()->user()->verificaIdentidade();
    }
    
    /**
     * Display a listing of the resource.
     * Cadastro de Orientador
     *
     * @return \Illuminate\Http\Response
     */
    public function index($msg = null, $paginas = 30)
    {
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
        if (!auth()->user()->can('userGraduacao') && !auth()->user()->can('admin')) {
            return "<script> alert('O1-Sem acesso à esta parte do Sistema. Entre em contato com a Graduação'); 
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
        if (!auth()->user()->can('userGraduacao') && !auth()->user()->can('admin')) {
            return "<script> alert('O1-Sem acesso à esta parte do Sistema. Entre em contato com a Graduação'); 
                             window.location.assign('".route('home')."');
                    </script>";
        }
        $rules = [];
        if ($request->input('externo')) {
            $objRemovido = Orientador::onlyTrashed()->where('CPF',$request->input('cpfOrientador'))->get();

            $rules['cpfOrientador'] = "required";
            $messages['cpfOrientador.required'] = "Favor informar o CPF do Orientador";
            $externo = 1;
        } else {
            $objRemovido = Orientador::onlyTrashed()->where('codpes',$request->input('nuspOrientador'))->get();

            $rules['nuspOrientador'] = "required";
            $messages['nuspOrientador.required'] = "Favor informar o número USP do Orientador";
            $externo = 0;
        }

        $rules['nomeOrientador']    = ["required","min:3","max:80"];
        $rules['emailOrientador']   = ["required","min:3", "email"];
        
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
            $orientador = new Orientador;
        }

        $orientador->codpes = $request->input('nuspOrientador');
        $orientador->nome = $request->input('nomeOrientador');
        $orientador->email = $request->input('emailOrientador');
        if ($externo == 1)
            $orientador->password = crypt(substr($request->input('cpfOrientador'),0,8),"$5&jj");
        
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
            $textoMensagem = "O seu cadastro foi realizado no Sistema de Depósito de TCC da Escola de Enfermagem da USP.           
            ";
            $textoMensagem.= "Esse cadastro foi realizado pelo Serviço de Graduação. Caso tennha dúvidas, entre em contato com gradee@usp.br                                        
            ";
            $textoMensagem.= "Seu login é este e-mail (".$orientador->email.")                                                                                            
            ";
            $textoMensagem.= "Sua senha é ".substr($orientador->CPF,0,8)."                               
            ";
            
            /*Mail::to("pcalves@usp.br", $orientador->nome)
                    ->send(new NotificacaoOrientador($textoMensagem,"Cadastro para acesso ao Sistema de Depósito de TCC - EEUSP", $orientador->nome));*/
            Mail::to($orientador->email, $orientador->nome)
                    ->send(new NotificacaoOrientador($textoMensagem,"Cadastro para acesso ao Sistema de Depósito de TCC - EEUSP", $orientador->nome));

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
        if (!auth()->user()->can('userGraduacao') && !auth()->user()->can('userOrientador') && !auth()->user()->can('admin')) {
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
            return "<script> alert('O1-Sem acesso à esta parte do Sistema. Entre em contato com a Graduação'); 
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
            return "<script> alert('O1-Sem acesso à esta parte do Sistema. Entre em contato com a Graduação'); 
                             window.location.assign('".route('sair')."');
                    </script>";
        }
        $objOrientador = Orientador::find($id);
        $emailAntigo = $objOrientador->email;
        
        $rules = [];

        $rules['nomeOrientador']    = ["required","min:3","max:80"];
        $rules['emailOrientador']   = ["required","min:3", "email"];
        
        $messages['required']   = "Favor informar o :attribute do orientador.";
        $messages['min']        = "O :attribute deve conter no mínimo :min caracteres";
        $messages['max']        = "O :attribute deve conter no mínimo :max caracteres";

        $request->validate($rules,$messages);
        
        $objOrientador->nome = $request->input('nomeOrientador');
        $objOrientador->email = $request->input('emailOrientador');
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
            return "<script> alert('O1-Sem acesso à esta parte do Sistema. Entre em contato com a Graduação'); 
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

        if (!auth()->user()->can('userGraduacao') && !auth()->user()->can('userOrientador') && !auth()->user()->can('admin')) {
            return "<script> alert('O1-Sem acesso à esta parte do Sistema. Entre em contato com a Graduação'); 
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
        $testeV = array_search("SERVIDOR",$vinculos);

        if ($testeV === false) {
            $arrRetorno = ["id" => ""
                          ,"nome" => ""
                          ,"email" => ""
                          ,"externo" => ""
                          ,"vinculos" => $vinculos];

            return $arrRetorno;
        }

        $dadosPessoa = Pessoa::dump($id);
        $email = Pessoa::email($id);
        
        $arrRetorno = ["id" => $dadosPessoa["codpes"]
                      ,"nome" => $dadosPessoa["nompes"]
                      ,"email" => $email
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
        
        $rules = ['orientadorId', ["exists:orientadores,id"]
                 ,'monografiaId', ["exists:monografias,id"]
                 ];
        if ($request->input('acao') == "DEVOLVIDO" || $request->input('acao') == "REPROVADO") {
            $rules["parecer"] = ["required","min:10","max:255"]; 
        } elseif ($request->input('acao') == "APROVADO") {
            $rules["publicar"] = ["required"];
            $messages['publicar.required'] = "Precisa ser informado se o trabalho deverá ou não ser publicado";
        }

        $messages['required']           = "Favor informar o :attribute.";
        $messages['min']                = "O :attribute deve conter no mínimo :min caracteres";
        $messages['max']                = "O :attribute deve conter no máximo :max caracteres";
        $messages['exists.orientadorId']= "O Orientador precisa estar cadastrado";
        $messages['exists.monografiaId']= "A Monografia precisa estar cadastrada";

        $request->validate($rules,$messages);

        $avaliacao = new Avaliacao;
        $avaliacao->orientadores_id = $request->input('orientadorId');
        $avaliacao->monografia_id   = $request->input('monografiaId');
        $avaliacao->dataAvaliacao   = date_create('now');
        $avaliacao->status          = $request->input('acao');
        $avaliacao->parecer         = $request->input('parecer');
        $avaliacao->save();

        $dadosMonografia = Monografia::where('id',$request->input('monografiaId'))->get();
        $dadosOrientador = Orientador::find($request->input('orientadorId'));

        if (!empty($request->input('publicar'))) {
            $monografia = $dadosMonografia->first();
            $monografia->publicar = ($request->has('publicar'))?$request->input('publicar'):null;
            $monografia->update();
        }

        //enviar e-mail
        $textoMensagem = null;
        if ($request->input('acao') == "DEVOLVIDO") {
            $textoMensagem = "A monografia título **".$dadosMonografia->first()->titulo."** tem uma correção a ser realizada.           
            ";
            $textoMensagem.= "Orientador responsável: **".$dadosOrientador->nome. "**                                        
             ";
            $textoMensagem.= "**Correção a ser realizada:** *".$request->input('parecer')."*                                                              
            ";
            $textoMensagem.= "Clique no botão abaixo para acessar o sistema e efetuar a correção.                         ";
        } elseif ($request->input('acao') == "REPROVADO") {
            $objMonografia = Monografia::find($request->input('monografiaId'));
            $objMonografia->status = "CONCLUIDO";
            $objMonografia->update();
            
            $textoMensagem = "A monografia título **".$dadosMonografia->first()->titulo."** foi reprovada.             
            ";
            $textoMensagem.= "Orientador responsável: **".$dadosOrientador->nome."**                      
            ";
            $textoMensagem.= "**MOTIVO:** *".$request->input('parecer')."*                       ";
        } elseif ($request->input('acao') == "APROVADO") {
            $objMonografia = Monografia::find($request->input('monografiaId'));
            $objMonografia->status = "CONCLUIDO";
            $objMonografia->update();

            $textoMensagem = "**PARABÉNS!!!**                                                            
            ";
            $textoMensagem.= "A monografia título **".$dadosMonografia->first()->titulo."** foi aprovada.         
            ";
            $textoMensagem.= "Orientador responsável: **".$dadosOrientador->nome."**                   ";            
        }
        
        if (!empty($textoMensagem)) {
            foreach($dadosMonografia->first()->alunos()->get() as $key=>$aluno) {
                $email = Pessoa::emailusp($aluno->id);
                
                /*Mail::to("pcalves@usp.br", $aluno->nome)
                    ->send(new NotificacaoAluno($textoMensagem,$aluno->nome));*/
                Mail::to($email, $aluno->nome)
                    ->send(new NotificacaoAluno($textoMensagem,$aluno->nome));
            }
        }

        return redirect()->route('orientador.edicao', ['idMono'=> $dadosMonografia->first()->id]);
    }

    /**
     * Método para abrir formulário de Avaliação
     */
    public function avaliacao($idMonografia, $acao) {

        if (!auth()->user()->can('userOrientador') ) {
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

}
