@extends('layouts.app')

@section('content')

<h1>Cadastro de &Aacute;rea Tem&aacute;tica</h1>
<form action="@if (isset($objAreaTematica)) {{ route('area_tematica.update',['area_tematica' => $objAreaTematica->id]) }}  @else {{ route('area_tematica.store') }} @endif" method="post">
    @csrf
    @if (isset($objAreaTematica)) @method("PUT") @endif
    <div class="campo">
    <label for="areaTematica">&Aacute;rea Tem&aacute;tica: </label><input type="text" name="areaTematica" class="inputBorder" id="areaTematica" size="50" value= "@if (isset($objAreaTematica)) {{ $objAreaTematica->descricao }} @else {{ trim(old('areaTematica')) }} @endif" required><br>
    </div>
    <div class="erro">{{  $errors->has('areaTematica') ? $errors->first('areaTematica'):null }}</div><br/><br/>
    <input type="submit" id="buttonSubmit" value="@if (isset($objAreaTematica)) Alterar @else Cadastrar @endif"/>
</form>
@endsection