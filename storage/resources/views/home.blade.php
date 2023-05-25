@extends('layouts.app')

@section('content')

    @auth
        <p>Instruções para usuários logados... </p>
        
    @endauth

    @guest
        <p>Clique em "Entrar" se você é aluno ou docente USP.</p>

        <p>Caso contrário, <a href="{{ route('login.externo')}}">clique aqui</a> para fazer o login.</p>
    @endguest

@endsection