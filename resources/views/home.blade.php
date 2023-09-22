@extends('layouts.app')

@section('content')

    @auth
        <p>Instruções para usuários logados... </p>
        
    @endauth

    @guest
        <p>Clique em "Entrar" se você é aluno ou docente USP.</p>

        <p>Se vc é Orientador Externo (SEM VÍNCULO USP)
            <ul>
                <li>Para se cadastrar, <a href="{{ route('orientador.novo-cadastro') }}"> clique aqui</a></li>
                <li>Para se logar no sistema <a href="{{ route('login.externo')}}">clique aqui</a></li>
            </ul>
        </p>
        
    @endguest

@endsection