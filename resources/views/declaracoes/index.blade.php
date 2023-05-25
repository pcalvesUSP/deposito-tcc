@extends('layouts.app')

@section('content')

    <h1>Emissão de Declarações</h1>

    <p>
    <form action="{{ route('declaracao.aluno') }}" method="post" id="declaracao_alunos" target="_blank">
    @csrf
    Declaração de Alunos TCC -> Número USP Aluno: <input type="text" class="inputBorder" id="nuspAluno" name="nuspAluno" value="{{ old('nuspAluno')}}"/>
    <input type="submit" value="OK"/><br/>
    <div class="erro">{{  $errors->has('nuspAluno') ? $errors->first('nuspAluno'):null }}</div>
    </form>
    </p>

    <p>
    <form action="{{ route('relatorio.aluno-orientador') }}" method="post" id="relatorioAlunoOrientador">
    @csrf
    Relatório Alunos-Orientadores -> Ano: 
    <select name="ano_aluno_orientador" id="ano_aluno_orientador">
    @foreach ($anosCadastrados as $ano)
      <option value="{{ $ano->ano }}">{{ $ano->ano }}</option>  
    @endforeach
    </select><br/>
    <div class="erro">{{  $errors->has('ano_aluno_orientador') ? $errors->first('ano_aluno_orientador'):null }}</div>
    </form>
    </p>

    <p>
    <form action="{{ route('relatorio.publicaBDTA') }}" method="post" id="relatorioPublicacao">
    @csrf
    Relatório Publicação BDTA -> Ano: 
    <select name="ano_publicacao" id="ano_publicacao">
    @foreach ($anosCadastrados as $ano)
      <option value="{{ $ano->ano }}">{{ $ano->ano }}</option>  
    @endforeach
    </select><br/>
    <div class="erro">{{  $errors->has('ano_publicacao') ? $errors->first('ano_publicacao'):null }}</div>
    </form>
    </p>

    <p>
    <form action="{{ route('relatorio.emissaoCertificado') }}" method="post" id="relatorioEmissaoCert">
    @csrf
    Relatório para Emissão de Certificado -> Ano: 
    <select name="ano_emissao" id="ano_emissao">
    @foreach ($anosCadastrados as $ano)
      <option value="{{ $ano->ano }}">{{ $ano->ano }}</option>  
    @endforeach
    </select><br/>
    <div class="erro">{{  $errors->has('ano_emissao') ? $errors->first('ano_emissao'):null }}</div>
    </form>
    </p>

    <script>
      $( document ).ready(function(){

        $('#ano_aluno_orientador').click(function() {
          $('#relatorioAlunoOrientador').submit();
        });

        $('#ano_publicacao').click(function() {
          $('#relatorioPublicacao').submit();
        });

        $('#ano_emissao').click(function() {
          $('#relatorioEmissaoCert').submit();
        });

      });
    </script>

@endsection