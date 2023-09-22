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
        <div class="erro" id="eCpfOrientador">{{  $errors->has('cpfOrientador') ? $errors->first('cpfOrientador'):null }}</div>
    </div>
    <label for="nomeOrientador">Nome: </label><input type="text" class="inputBorder" style="left:136px;" name="nomeOrientador" id="nomeOrientador" size="50" value="{{ (isset($objOrientador)?$objOrientador->nome:old('nomeOrientador')) }}" required><br>
    <label for="emailOrientador">E-mail: </label><input type="text" class="inputBorder" style="left:133px;" name="emailOrientador" id="emailOrientador" size="50" value="{{ (isset($objOrientador)?$objOrientador->email:old('emailOrientador')) }}" required><br>
    <label for="telefoneOrientador">Telefone: </label><input type="text" class="inputBorder" style="left:118px;" name="telefoneOrientador" id="telefoneOrientador" size="30" value="{{ (isset($objOrientador)?$objOrientador->telefone:old('telefoneOrientador')) }}" required><br>
    <label for="instituicaoOrientador">Instituição de Vínculo: </label><input type="text" class="inputBorder" style="left:25px;" name="instituicaoOrientador" id="instituicaoOrientador" size="50" value="{{ (isset($objOrientador)?$objOrientador->instituicao_vinculo:old('instituicaoOrientador')) }}" required><br>
    <label for="linkLattes">Link Lattes: </label><input type="text" class="inputBorder" style="left:101px;" name="linkLattes" id="linkLattes" size="50" value="{{ (isset($objOrientador)?$objOrientador->link_lattes:old('linkLattes')) }}"><br>
    <div id="campo">
        <label for="area_atuacao">Área de Atuação: </label>
        <textarea class="inputBorder" style="left:55px;" rows="10" cols="50" name="area_atuacao" id="area_atuacao">
           {{ (isset($objOrientador)?$objOrientador->area_atuacao:old('areaAtuacao')) }}
        </textarea>
    </div>
    <input type="submit" id="buttonSubmit" style="float:left;" value="@if (isset($objOrientador)) Alterar @else Cadastrar @endif"/>
</form>
@if (isset($objOrientador)) <button id="buttonSubmit" style="float:left;width:auto;border:none;" onclick="javascript:window.location='{{ route('orientador.index') }}'">Cancelar</button> @endif
<script src="js/cadastroOrientador.js"></script>

@endsection