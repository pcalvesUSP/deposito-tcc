@extends('layouts.app')

@section('content')

<h1>Cadastro de Orientador</h1>
<form action="@if (isset($objOrientador)) {{ route('orientador.update',['id'=>$objOrientador->id]) }} @else {{ route('orientador.salvardados') }} @endif" method="post">
    @csrf
    @if (isset($objOrientador)) @method("PUT") @endif
    <div class="campo"><label>Orientador Externo? </label><input type="checkbox" name="externo" id="externo" value="1" @if (old('externo')==1 || (isset($objOrientador) && $objOrientador->externo == 1)) checked @endif @if (isset($objOrientador)) disabled @endif><br/></div>
    <div id="divNUSP" style="display:inline">
        <label for="nuspOrientador">Número USP: </label><input type="text" class="inputBorder" style="left:86px;" name="nuspOrientador" id="nuspOrientador" value="{{ (isset($objOrientador)?$objOrientador->codpes:old('nuspOrientador')) }}" @if (isset($objOrientador)) disabled @endif><br>
    </div>
    <div id="divExt" style="display:none">
        <label for="cpfOrientador">CPF (Somente n&uacute;meros): </label><input type="text" class="inputBorder" style="left:5px;" name="cpfOrientador" id="cpfOrientador" size="20" value="{{ (isset($objOrientador)?$objOrientador->CPF:old('cpfOrientador')) }}" @if (isset($objOrientador)) disabled @endif><br>
    </div>
    <label for="nomeOrientador">Nome: </label><input type="text" class="inputBorder" style="left:136px;" name="nomeOrientador" id="nomeOrientador" size="50" value="{{ (isset($objOrientador)?$objOrientador->nome:old('nomeOrientador')) }}" required><br>
    <label for="emailOrientador">E-mail: </label><input type="text" class="inputBorder" style="left:133px;" name="emailOrientador" id="emailOrientador" size="50" value="{{ (isset($objOrientador)?$objOrientador->email:old('emailOrientador')) }}" required><br>
    <input type="submit" id="buttonSubmit" value="@if (isset($objOrientador)) Alterar @else Cadastrar @endif"/>
    @if (isset($objOrientador)) <button id="buttonSubmit" style="width:auto;border:none;" onclick="windows.location='{{ route('orientador.index') }}'">Cancelar</button> @endif
</form>
<script src="js/cadastroOrientador.js"></script>

@endsection