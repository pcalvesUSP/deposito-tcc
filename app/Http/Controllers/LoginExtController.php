<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use \App\Models\User;
use \App\Models\Orientador;

class LoginExtController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('externo.login');
    }

    /**
     * Autentica o usuário
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function autenticar(Request $request) {

        $rules = ['usuario' => ["required","email"]
                 ,'senha'   => "required"
                 ];

        $mensagem = ["required" => "O :attribute deve ser informado."];
        $mensagem = ["email" => "O :attribute deve ser um e-mail válido."];
        
        $request->validate($rules, $mensagem);

        $user = Orientador::where('email',$request->input('usuario'))->where('aprovado',1)->get();
        if (!$user->isEmpty())
            $user = User::where('email',$request->input('usuario'))->get();

        if ($user->isEmpty()) {
            return "<script>alert('Erro ao realizar o login'); window.location='".route('home')."'</script>";
        } else {

            if (password_verify($request->input('senha'), $user->first()->password)) {
                $user = $user->first();
                Auth::login($user);
                $dadosLogin = $user->verificaIdentidade();
                session(['orientadorExterno' => 1]);
            } else {
                return "<script>alert('Senha não confere'); window.location='".route('home')."'</script>";
            }
        }
        
        return redirect(route('home'));
    }

    /**
     * Ajuste de Cadastro de usuário -> implementação
     */
    public function ajusteCadastroOrientadorExterno() {
        $listaOrientadores = Orientador::where('externo',1)->where('aprovado',1)->get();
        foreach($listaOrientadores as $orientador) {
            $user = User::where('email',$orientador->email)->get();
            if ($user->isEmpty()) {
                $user = new User();
                $user->name = $orientador->nome;
                $user->email = $orientador->email;
                $user->password = $orientador->password;
                $user->created_at = date('Y-m-d H:i:s');
                $user->updated_at = date('Y-m-d H:i:s');
                $user->save();
                print_r($user);
            }
        }
    }
}
