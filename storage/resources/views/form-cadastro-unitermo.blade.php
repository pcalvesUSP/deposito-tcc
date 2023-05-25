@extends('layouts.app')

@section('content')

<h1>Cadastro de Unitermos</h1>
<form action="@if(isset($objUnitermo)) {{ route('unitermos.update',['unitermo'=>$objUnitermo->id]) }} @else {{ route('unitermos.store') }} @endif" method="post">
    @csrf
    @if (isset($objUnitermo)) @method("PUT") @endif
    <div class="campo">
    <label for="unitermo">Unitermo: </label><input type="text" class="inputBorder" name="unitermo" id="unitermo" value="{{ (isset($objUnitermo)?$objUnitermo->unitermo:trim(old('unitermo'))) }}" required><br>
    </div><br/>
    <input type="submit" id="buttonSubmit" value="@if (isset($objUnitermo)) Alterar @else Cadastrar @endif"/>
    @if (isset($objUnitermo)) <button id="buttonSubmit" style="width:auto;border:none;" onclick="window.location='{{ route('unitermos.index') }}'; return false;">Cancelar</button> @endif

</form>
@endsection