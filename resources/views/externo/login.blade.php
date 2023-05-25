@extends('layouts.app')

@section('content')

<div class="conteudo-pagina">
        <h1 style="text-align:center;">Login de Orientador Externo</h1>

        <div class="login_externo">
            <div style="width:30%; margin-left: auto; margin-right: auto;">
                <form action={{ route('login.externo') }} method="post">
                    @csrf
                    <input name="usuario" type="text" placeholder="Usuário"><br/>
                    <input name="senha" type="password" placeholder="Senha"><br/><br/>
                    <button type="submit" style="width:auto;left:50%;display:inline;">Acessar</button>
                </form>
            </div>
        </div>
    </div>

@endsection