@extends('layouts.app')

@section('content')
<p><a href="{{ route('banca.index') }}">Listar Membros da Banca</a></p>
<h1>Cadastro de Banca</h1>
<form action="@if (isset($objBanca)) {{ route('banca.update',['banca'=>$objBanca->id]) }} @else {{ route('banca.store') }} @endif" method="post">
    @csrf
    @if (isset($objBanca)) @method("PUT") @endif
    <label for="numUSPBanca">Número USP: </label>&nbsp;<input type="text" class="inputBorder" style="left:5px;" name="numUSPBanca" id="numUSPBanca" value="{{ (isset($objBanca)?$objBanca->codpes:old('numUSPBanca')) }}" @if (isset($objBanca)) disabled @endif><br>
    <div class="erro">{{  $errors->has('numUSPBanca') ? $errors->first('numUSPBanca'):(isset($objBanca))?null:'Número USP pode ser deixado em branco' }}</div>
    <label for="nomeBanca">Nome: </label>&nbsp;<input type="text" class="inputBorder" style="left:55px;" name="nomeBanca" id="nomeBanca" size="50" value="{{ (isset($objBanca)?$objBanca->nome:old('nomeBanca')) }}" required><br>
    <div class="erro">{{  $errors->has('nomeBanca') ? $errors->first('nomeBanca'):null }}</div>
    <label for="emailBanca">E-mail: </label>&nbsp;<input type="text" class="inputBorder" style="left:51px;" name="emailBanca" id="emailBanca" size="50" value="{{ (isset($objBanca)?$objBanca->email:old('emailBanca')) }}" required><br>
    <div class="erro">{{  $errors->has('emailBanca') ? $errors->first('emailBanca'):null }}</div>
    <input type="submit" id="buttonSubmit" value="@if (isset($objBanca)) Alterar @else Cadastrar @endif"/>
</form>
<script src="js/cadastroBanca.js"></script>

@endsection