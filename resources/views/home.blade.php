@extends('layouts.app')

@section('content')

    <p>O Trabalho de Conclusão do Curso (TCC) consiste na formulação e apresentação de um trabalho de natureza cientifica ou 
        técnica da área farmacêutica, sob orientação de um professor da FCFUSP, ou profissional credendicado, elaborado 
        individualmente por aluno(a) do curso de graduação em Farmácia-Bioquímica, como condição, se aprovado(a), para a obtenção do 
        título de Farmacêutico-Bioquímico. O trabalho técnico é um estudo que visa propor, discutir, revisar e/ou apresentar 
        soluções para um problema de relevância nas ciências farmacêuticas.
    </p>
    <p><a href="https://www.fcf.usp.br/arquivos/graduacao/TCC/MANUAL%20DE%20TCC%20FCFUSP%202021.pdf" targe="_blank">MANUAL DE TRABALHO DE CONCLUSÃO DE CURSO FCFUSP</a></p>
    <p><a href="http://fcf.usp.br/arquivos/27102023-162859.pdf" target="_blank">Cronograma TCC 2024-2025</a></p>

    @auth
        
    @endauth

    @guest
        <p>Clique em "Entrar", localizado no canto direito do cabeçalho desta página, se você é aluno ou docente FCF-USP.</p>

        <p>Se vc é Orientador Externo (SEM VÍNCULO COM A FCF-USP)
            <ul>
                <li>Para se cadastrar, <a href="{{ route('orientador.novo-cadastro') }}"> clique aqui</a></li>
                <li>Para se logar no sistema <a href="{{ route('login.externo')}}">clique aqui</a></li>
            </ul>
        </p>
        
    @endguest

@endsection