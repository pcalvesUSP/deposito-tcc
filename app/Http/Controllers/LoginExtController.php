<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use \App\Models\User;

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

        $pass = crypt($request->input('senha'),"$5&jj");
        
        $user = User::where('email',$request->input('usuario'))->where('password',$pass)->get();

        if ($user->isEmpty()) {
            print "<script>alert('Erro ao realizar o login'); </script>";
        } else {
            $user = $user->first();
            Auth::login($user);

            $dadosLogin = $user->verificaIdentidade();
        }
        return redirect(route('home'));
    }
}
