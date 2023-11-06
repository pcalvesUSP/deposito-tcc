@extends('layouts.app')

@section('content')

    <h1>Emissão de Relatórios ou Declarações</h1>

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
    </div>

    <p>
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
      </p>

      <div style="background-color: rgb(245, 215, 240);">
        <form action="{{ route('relatorio.tema-defendido') }}" method="post" id="relatorioTemaDefendido">
        @csrf
        Relatório Tema Defendido -> Ano: 
        <select name="ano_tema" id="ano_tema">
          <option value="">Selecione</option>
        @foreach ($anosCadastrados as $ano)
          <option value="{{ $ano->ano }}">{{ $ano->ano }}</option>  
        @endforeach
        </select>&nbsp;
        Semestre:
        <select name="semestre_tema" id="semestre_tema">
          <option value="">Selecione</option>
          <option value="1">1</option>
          <option value="2">2</option>
          </select>
          <input type="submit" value="OK"><br/>
        <div class="erro">{{  $errors->has('ano_tema') || $errors->has('semestre_tema')  ? $errors->first('ano_tema')." ".$errors->has('semestre_tema'):null }}</div>
        </form>
      </div>

        <p>
        <form action="{{ route('relatorio.final') }}" method="post" id="relatorioFinal" target="_blank">
          @csrf
          Gerar Relatório Final em PDF -> Número USP Aluno: <input type="text" class="inputBorder" id="nuspAluno" name="nuspAluno" value="{{ old('nuspAluno')}}"/>
          <input type="submit" value="OK"/><br/>
          <div class="erro">{{  $errors->has('nuspAluno') ? $errors->first('nuspAluno'):null }}</div>
        </form>
      </p>  

    <script>
      $( document ).ready(function(){

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

        $('#semestre_tema').on("change", function() {
          if ($(this).val().length > 0) {
              $('#relatorioTemaDefendido').submit();
          }
        });

      });
    </script>

@endsection