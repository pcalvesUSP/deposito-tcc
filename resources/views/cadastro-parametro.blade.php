@extends('layouts.app')

@section('content')

<div class="erro" id="mensagem"> {{ $mensagem }}</div>
<h1>Área Administrativa</h1>
<p><b>Atenção: as datas são contadas à partir da meia noite do dia selecionado</b></p>
<form id="formParametro" action="@if ($acao=="novo") {{ route('administracao.store') }} @else {{ route('administracao.update',['administracao' => $dadosParam->first()->id ]) }} @endif" method="post">
@csrf
<div id="metodo">
@if ($acao == "atualizacao" || !empty($dadosParam->first()->id)) @method('PUT') @endif
</div>
<input type="hidden" name="id_parametro" id="id_parametro" value="{{ (!empty($dadosParam->first()->id))?$dadosParam->first()->id:null }}">
<p>
<select name="semestreAno" id="semestreAno">
  <option>Selecione</option>
  @if (!$dadosParam->isEmpty())
      <option selected value="{{$dadosParam->first()->ano."-".$dadosParam->first()->semestre}}">Ano: {{ $dadosParam->first()->ano }} / Semestre: {{ $dadosParam->first()->semestre }}</option>
  @endif
  @foreach ($dadosSemestre as $semestreAno)
  @if (!$dadosParam->isEmpty() && $dadosParam->first()->ano == $semestreAno->ano && $dadosParam->first()->semestre == $semestreAno->semestre)
  @continue
  @else
  <option value="{{ $semestreAno->ano."-".$semestreAno->semestre}}">Ano: {{ $semestreAno->ano }} / Semestre: {{ $semestreAno->semestre }} </option>
  @endif
  @endforeach
</select>
</p>
<div class="campo">
<label for="dataInicioAlunos">Data INICIAL para cadastro do projeto de todos os alunos (DD/MM/AAAA)</label><input type="text" value="@if (!$dadosParam->isEmpty()) {{ $dadosParam->first()->dataAberturaDiscente->format('d/m/Y') }} @else {{ trim(old('dataInicioAlunos')) }} @endif" name="dataInicioAlunos" id="dataInicioAlunos"/><br/>
<div class="erro">{{  $errors->has('dataInicioAlunos') ? $errors->first('dataInicioAlunos'):null }}</div>
<label for="dataFinalAlunos">Data FINAL para cadastro do projeto de todos os alunos (DD/MM/AAAA)</label><input type="text" value="@if (!$dadosParam->isEmpty()) {{ $dadosParam->first()->dataFechamentoDiscente->format('d/m/Y') }} @else {{ trim(old('dataFinalAlunos')) }} @endif" name="dataFinalAlunos" id="dataFinalAlunos"/><br/>
<div class="erro">{{  $errors->has('dataFinalAlunos') ? $errors->first('dataFinalAlunos'):null }}</div>

<label for="dataInicioDocentes">Data INICIAL para aprovação do projeto pelos orientadores (DD/MM/AAAA)</label><input type="text" value="@if (!$dadosParam->isEmpty()) {{ $dadosParam->first()->dataAberturaDocente->format('d/m/Y') }} @else {{ trim(old('dataInicioDocentes')) }} @endif" name="dataInicioDocentes" id="dataInicioDocentes"/><br/>
<div class="erro">{{  $errors->has('dataInicioDocentes') ? $errors->first('dataInicioDocentes'):null }}</div>
<label for="dataFinalDocentes">Data FINAL para aprovação do projeto pelos orientadores (DD/MM/AAAA)</label><input type="text" value="@if (!$dadosParam->isEmpty()) {{ $dadosParam->first()->dataFechamentoDocente->format('d/m/Y') }} @else {{ trim(old('dataFinalDocentes')) }} @endif" name="dataFinalDocentes" id="dataFinalDocentes"/><br/>
<div class="erro">{{  $errors->has('dataFinalDocentes') ? $errors->first('dataFinalDocentes'):null }}</div>

<label for="dataAberturaAvaliacao">Data INICIAL para avaliação dos projetos de TCC (DD/MM/AAAA)</label><input type="text" value="@if (!$dadosParam->isEmpty()) {{ $dadosParam->first()->dataAberturaAvaliacao->format('d/m/Y') }} @else {{ trim(old('dataAberturaAvaliacao')) }} @endif" name="dataAberturaAvaliacao" id="dataAberturaAvaliacao"/><br/>
<div class="erro">{{  $errors->has('dataAberturaAvaliacao') ? $errors->first('dataInicioAlunos'):null }}</div>
<label for="dataFechamentoAvaliacao">Data FINAL para avaliação dos projetos de TCC (DD/MM/AAAA)</label><input type="text" value="@if (!$dadosParam->isEmpty()) {{ $dadosParam->first()->dataFechamentoAvaliacao->format('d/m/Y') }} @else {{ trim(old('dataFechamentoAvaliacao')) }} @endif" name="dataFechamentoAvaliacao" id="dataFechamentoAvaliacao"/><br/>
<div class="erro">{{  $errors->has('dataFechamentoAvaliacao') ? $errors->first('dataFechamentoAvaliacao'):null }}</div>

<label for="dataAberturaUploadTCC">Data INICIAL para upload de trabalho de TCC (DD/MM/AAAA)</label><input type="text" value="@if (!$dadosParam->isEmpty()) {{ $dadosParam->first()->dataAberturaUploadTCC->format('d/m/Y') }} @else {{ trim(old('dataAberturaUploadTCC')) }} @endif" name="dataAberturaUploadTCC" id="dataAberturaUploadTCC"/><br/>
<div class="erro">{{  $errors->has('dataAberturaUploadTCC') ? $errors->first('dataAberturaUploadTCC'):null }}</div>
<label for="dataFechamentoAvaliacao">Data FINAL para upload de trabalho de TCC (DD/MM/AAAA)</label><input type="text" value="@if (!$dadosParam->isEmpty()) {{ $dadosParam->first()->dataFechamentoUploadTCC->format('d/m/Y') }} @else {{ trim(old('dataFechamentoUploadTCC')) }} @endif" name="dataFechamentoUploadTCC" id="dataFechamentoUploadTCC"/><br/>
<div class="erro">{{  $errors->has('dataFechamentoUploadTCC') ? $errors->first('dataFechamentoUploadTCC'):null }}</div>

<br/>
<input type="submit" value="Salvar" id="salvar_parametros">
</form>

<script src="js/cadastroParametro.js"></script>
<script>
    setTimeout(function() {
                $('#mensagem').fadeOut('fast');
              }, 3000);
</script>

@endsection