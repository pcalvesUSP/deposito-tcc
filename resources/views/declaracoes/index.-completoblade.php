@extends('layouts.app')

@section('content')

    <h1>Emissão de Relatórios ou Declarações</h1>

    <p>
    <form action="{{ route('declaracao.aluno') }}" method="post" id="declaracao_alunos" target="_blank">
    @csrf
    Declaração de Alunos TCC -> Número USP Aluno: <input type="text" class="inputBorder" id="nuspAluno" name="nuspAluno" value="{{ old('nuspAluno')}}"/>
    <input type="submit" value="OK"/><br/>
    <div class="erro">{{  $errors->has('nuspAluno') ? $errors->first('nuspAluno'):null }}</div>
    </form>
    </p>

    <div style="background-color: rgb(245, 215, 240);">
      <form action="{{ route('relatorio.aluno-orientador') }}" method="post" id="relatorioAlunoOrientador">
      @csrf
      Relatório de  Alunos-Orientadores (Trabalhos Concluídos) -> Ano: 
      <select name="ano_aluno_orientador" id="ano_aluno_orientador">
        <option value="">Selecione</option>
      @foreach ($anosCadastrados as $ano)
        <option value="{{ $ano->ano }}">{{ $ano->ano }}</option>  
      @endforeach
      </select>&nbsp;
      Semestre:
      <select name="semestre_aluno_orientador" id="semestre_aluno_orientador">
        <option value="">Selecione</option>
        <option value="1">1</option>
        <option value="2">2</option>
        </select><br/>
      <div class="erro">{{  ($errors->has('ano_aluno_orientador') || $errors->has('semestre_aluno_orientador')) ? $errors->first('ano_aluno_orientador')." ".$errors->first('semestre_aluno_orientador'):null }}</div>
      </form>
    </div>

    <p>
    <form action="{{ route('relatorio.publicaBDTA') }}" method="post" id="relatorioPublicacao">
    @csrf
    Relatório Publicação BDTA -> Ano: 
    <select name="ano_publicacao" id="ano_publicacao">
      <option value="">Selecione</option>
    @foreach ($anosCadastrados as $ano)
      <option value="{{ $ano->ano }}">{{ $ano->ano }}</option>  
    @endforeach
    </select>&nbsp;
    Semestre:
    <select name="semestre_publicacao" id="semestre_publicacao">
      <option value="">Selecione</option>
      <option value="1">1</option>
      <option value="2">2</option>
      </select><br/>
    <div class="erro">{{  ($errors->has('ano_publicacao')) || $errors->has('semestre_publicacao') ? $errors->first('ano_publicacao')." ".$errors->first('semestre_publicacao'):null }}</div>
    </form>
    </p>

    <div style="background-color: rgb(245, 215, 240);">
    <form action="{{ route('relatorio.emissaoCertificado') }}" method="post" id="relatorioEmissaoCert">
    @csrf
    Relatório para Emissão de Certificado -> Ano: 
    <select name="ano_emissao" id="ano_emissao">
      <option value="">Selecione</option>
    @foreach ($anosCadastrados as $ano)
      <option value="{{ $ano->ano }}">{{ $ano->ano }}</option>  
    @endforeach
    </select>&nbsp;
    Semestre:
    <select name="semestre_emissao" id="semestre_emissao">
      <option value="">Selecione</option>
      <option value="1">1</option>
      <option value="2">2</option>
      </select><br/>
    <div class="erro">{{  ($errors->has('ano_emissao') || $errors->has('semestre_emissao')) ? $errors->first('ano_emissao')." ".$errors->has('semestre_emissao'):null }}</div>
    </form>
  </div>

    <p>
      <form action="{{ route('relatorio.bancasSugeridas') }}" method="post" id="relatorioSugestaoBanca">
      @csrf
      Sugestão de Banca -> Ano: 
      <select name="ano_banca" id="ano">
        <option value="">Selecione</option>
      @foreach ($anosCadastrados as $ano)
        <option value="{{ $ano->ano }}">{{ $ano->ano }}</option>  
      @endforeach
      </select>&nbsp;
      Semestre:
      <select name="semestre_banca" id="Semestre">
        <option value="">Selecione</option>
        <option value="1">1</option>
        <option value="2">2</option>
        </select>
        <input type="submit" value="OK"><br/>
      <div class="erro">{{  $errors->has('ano_banca') || $errors->has('semestre_banca')  ? $errors->first('ano_banca')." ".$errors->has('semestre_banca'):null }}</div>
      </form>
      </p>

      <div style="background-color: rgb(245, 215, 240);">
        <form action="{{ route('relatorio.notas-projeto-tcc') }}" method="post" id="relatorioNotas">
        @csrf
        Relatório de Notas Projeto e TCC -> Ano: 
        <select name="ano_tcc" id="ano_tcc">
          <option value="">Selecione</option>
        @foreach ($anosCadastrados as $ano)
          <option value="{{ $ano->ano }}">{{ $ano->ano }}</option>  
        @endforeach
        </select>&nbsp;
        Semestre:
        <select name="semestre_tcc" id="semestre_tcc">
          <option value="">Selecione</option>
          <option value="1">1</option>
          <option value="2">2</option>
          </select><br/>
        <div class="erro">{{  ($errors->has('ano_tcc') || $errors->has('semestre_tcc')) ? $errors->first('ano_tcc')." ".$errors->has('semestre_tcc'):null }}</div>
        </form>
      </div>

    <script>
      $( document ).ready(function(){

        $('#semestre_aluno_orientador').on("change", function() {
          if ($(this).val().length > 0) {
              $('#relatorioAlunoOrientador').submit();
          }        
        });

        $('#semestre_publicacao').on("change", function() {
          if ($(this).val().length > 0) {
              $('#relatorioPublicacao').submit();
          } 
        });

        $('#semestre_emissao').on("change", function() {
          if ($(this).val().length > 0) {
              $('#relatorioEmissaoCert').submit();
          }
        });

        $('#semestre_tcc').on("change", function() {
          if ($(this).val().length > 0) {
              $('#relatorioNotas').submit();
          }
        });

      });
    </script>

@endsection