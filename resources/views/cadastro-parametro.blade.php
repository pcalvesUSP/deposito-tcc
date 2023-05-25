@extends('layouts.app')

@section('content')

<div class="erro" id="mensagem"> {{ $mensagem }}</div>
<h1>Área Administrativa</h1>
<p>Atenção: as datas são contadas à partir da meia noite do dia selecionado</p>
<form action="@if ($acao=="novo") {{ route('administracao.store') }} @else {{ route('administracao.update',['administracao' => $dadosParam->first()->id ]) }} @endif" method="post">
@csrf
@if ($acao == "atualizacao") @method('PUT') @endif
<div class="campo">
<label for="dataInicioAlunos">Data INICIAL para todos os alunos (DD/MM/AAAA)</label><input type="text" value="@if (!$dadosParam->isEmpty()) {{ $dadosParam->first()->dataAberturaDiscente->format('d/m/Y') }} @else {{ trim(old('dataInicioAlunos')) }} @endif" name="dataInicioAlunos" id="dataInicioAlunos"/><br/>
<div class="erro">{{  $errors->has('dataInicioAlunos') ? $errors->first('dataInicioAlunos'):null }}</div>
<label for="dataFinalAlunos">Data FINAL para todos os alunos (DD/MM/AAAA)</label><input type="text" value="@if (!$dadosParam->isEmpty()) {{ $dadosParam->first()->dataFechamentoDiscente->format('d/m/Y') }} @else {{ trim(old('dataFinalAlunos')) }} @endif" name="dataFinalAlunos" id="dataFinalAlunos"/><br/>
<div class="erro">{{  $errors->has('dataFinalAlunos') ? $errors->first('dataFinalAlunos'):null }}</div>
<label for="dataInicioDocentes">Data INICIAL para todos os docentes (DD/MM/AAAA)</label><input type="text" value="@if (!$dadosParam->isEmpty()) {{ $dadosParam->first()->dataAberturaDocente->format('d/m/Y') }} @else {{ trim(old('dataInicioDocentes')) }} @endif" name="dataInicioDocentes" id="dataInicioDocentes"/><br/>
<div class="erro">{{  $errors->has('dataInicioDocentes') ? $errors->first('dataInicioDocentes'):null }}</div>
<label for="dataFinalDocentes">Data FINAL para todos os docentes (DD/MM/AAAA)</label><input type="text" value="@if (!$dadosParam->isEmpty()) {{ $dadosParam->first()->dataFechamentoDocente->format('d/m/Y') }} @else {{ trim(old('dataFinalDocentes')) }} @endif" name="dataFinalDocentes" id="dataFinalDocentes"/><br/>
<div class="erro">{{  $errors->has('dataFinalDocentes') ? $errors->first('dataFinalDocentes'):null }}</div>
<label for="mostra">Número romano que representa a Mostra</label><input type="text" value="@if (!$dadosParam->isEmpty()) {{ $dadosParam->first()->mostra }} @else {{ trim(old('mostra')) }} @endif" name="mostra" id="mostra"/><br/>
<div class="erro">{{  $errors->has('mostra') ? $errors->first('mostra'):null }}</div>
<label for="mesMostra">Mês em que a mostra ocorrerá</label><input type="text" value="@if (!$dadosParam->isEmpty()) {{ $dadosParam->first()->mesMostra }} @else {{ trim(old('mesMostra')) }} @endif" name="mesMostra" id="mesMostra"/><br/>
<div class="erro">{{  $errors->has('mesMostra') ? $errors->first('mesMostra'):null }}</div>
</div><br/>
<input type="submit" value="Salvar" id="salvar_parametros">
</form>


<script>
    setTimeout(function() {
                $('#mensagem').fadeOut('fast');
              }, 3000);
</script>

@endsection