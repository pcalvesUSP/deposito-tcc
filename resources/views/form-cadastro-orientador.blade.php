@extends('layouts.app')

@section('content')

<h1>Cadastro de Orientador</h1>
<form action="@if ($readonly) {{null}} @elseif (isset($objOrientador)) {{ route('orientador.update',['id'=>$objOrientador->id]) }} @else {{ route('orientador.salvardados') }} @endif" method="post" enctype="multipart/form-data">
    @csrf
    @if (isset($objOrientador)) @method("PUT") @endif
    <div class="campo"><label>Orientador Externo? </label><input type="checkbox" name="externo" id="externo" value="1" @if (old('externo')==1 || (isset($objOrientador) && $objOrientador->externo == 1)) checked @endif @if (isset($objOrientador)) disabled @endif><br/></div>
    <div id="divNUSP" style="display:inline">
        <label for="nuspOrientador">Número USP: </label><input type="text" class="inputBorder" style="left:205px;" name="nuspOrientador" id="nuspOrientador" value="{{ (isset($objOrientador)?$objOrientador->codpes:old('nuspOrientador')) }}" @if (isset($objOrientador) && $objOrientador->externo == 0) disabled @endif><br>
    </div>
    <div id="divExt" style="display:none">
        <label for="cpfOrientador">CPF (Somente n&uacute;meros): </label><input type="text" class="inputBorder" style="left:124px;" name="cpfOrientador" id="cpfOrientador" size="20" value="{{ (isset($objOrientador)?$objOrientador->CPF:old('cpfOrientador')) }}" @if (isset($objOrientador) || $readonly) disabled @endif ><br>
        <div class="erro" id="eCpfOrientador">{{  $errors->has('cpfOrientador') ? $errors->first('cpfOrientador'):null }}</div>
    </div>
    <label for="nomeOrientador">Nome: </label><input type="text" class="inputBorder" style="left:255px;" name="nomeOrientador" id="nomeOrientador" size="50" value="{{ (isset($objOrientador)?$objOrientador->nome:old('nomeOrientador')) }}" required @if ($readonly) disabled @endif><br>
    <div class="erro" id="enomeOrientador">{{  $errors->has('nomeOrientador') ? $errors->first('nomeOrientador'):null }}</div>
    <label for="emailOrientador">E-mail: </label><input type="text" class="inputBorder" style="left:252px;" name="emailOrientador" id="emailOrientador" size="50" value="{{ (isset($objOrientador)?$objOrientador->email:old('emailOrientador')) }}" required @if ($readonly) disabled @endif><br>
    <div class="erro" id="eemailOrientador">{{  $errors->has('emailOrientador') ? $errors->first('emailOrientador'):null }}</div>
    <label for="telefoneOrientador">Telefone: </label><input type="text" class="inputBorder" style="left:237px;" name="telefoneOrientador" id="telefoneOrientador" size="30" value="{{ (isset($objOrientador)?$objOrientador->telefone:old('telefoneOrientador')) }}" @if (!isset($objOrientador)) required @endif @if ($readonly) disabled @endif><br>
    <div class="erro" id="etelefoneOrientador">{{  $errors->has('telefoneOrientador') ? $errors->first('telefoneOrientador'):null }}</div>
    <label for="instituicaoOrientador">Instituição de Vínculo: </label><input type="text" class="inputBorder" style="left:145px;" name="instituicaoOrientador" id="instituicaoOrientador" size="50" value="{{ (isset($objOrientador)?$objOrientador->instituicao_vinculo:old('instituicaoOrientador')) }}" required @if ($readonly) disabled @endif><br>
    @if(isset($objOrientador) && !empty($objOrientador->comprovante_vinculo))
    <label>Comprovante de vínculo com a Instituição: </label> <a href="upload/orientador/{{ $objOrientador->comprovante_vinculo }}" target="_blank">Verifique o comprovante</a><br/>
    @else
    <label for="comprovante_vinculo">Comprovante de vínculo com a Instituição: </label><input type="file" name="comprovante_vinculo" id="comprovante_vinculo" @if (!isset($objOrientador)) required @endif @if ($readonly) disabled @endif><br>
    <div class="erro" id="ecomprovante_vinculo">{{  $errors->has('comprovante_vinculo') ? $errors->first('comprovante_vinculo'):null }}</div>
    @endif
    <label for="linkLattes">Link Lattes: </label><input type="text" class="inputBorder" style="left:218px;" name="linkLattes" id="linkLattes" size="50" value="{{ (isset($objOrientador)?$objOrientador->link_lattes:old('linkLattes')) }}" @if ($readonly) disabled @endif><br>
    <div id="campo">
        <label for="area_atuacao" style="vertical-align: top;">Área de Atuação: </label>
        <textarea class="inputBorder" style="left:173px;" rows="10" cols="50" name="area_atuacao" id="area_atuacao" @if ($readonly) disabled @endif>
           {{ (isset($objOrientador)?$objOrientador->area_atuacao:old('areaAtuacao')) }}
        </textarea>
    </div>
    @if (!$readonly)
    <input type="submit" id="buttonSubmit" style="float:left;" value="@if (isset($objOrientador)) Alterar @else Cadastrar @endif"/>
    @endif
</form>
<button id="buttonSubmit" style="float:left;width:auto;border:none;" onclick="javascript:window.location='@if ($readonly) {{ route('comissao.orientador') }} @else {{ route('orientador.index') }} @endif '">Cancelar</button> 
<script src="js/cadastroOrientador.js"></script>

@endsection