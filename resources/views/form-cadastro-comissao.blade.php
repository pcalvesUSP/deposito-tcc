@extends('layouts.app')

@section('content')

<p><a href="{{ route('comissao.index') }}">Listar Membros da Comissão</a></p>

<h1>Cadastro de Membro da Comissão</h1>
<div id="msg" class="erro"></div>
<form action="@if (isset($objComissao)) {{ route('comissao.update',['comissao'=>$objComissao->id]) }} @else {{ route('comissao.store') }} @endif" method="post" enctype="multipart/form-data">
    @csrf
    @if (isset($objComissao)) @method("PUT") @endif
    <label for="nuspComissao">Número USP: </label><input type="text" class="inputBorder" style="left:52px;" name="nuspComissao" id="nuspComissao" size="20" value="{{ (isset($objComissao)?$objComissao->codpes:old('nuspComissao')) }}" @if (isset($objComissao)) disabled @endif><br>
    <label for="nomeComissao">Nome: </label><input type="text" class="inputBorder" style="left:102px;" name="nomeComissao" id="nomeComissao" size="50" value="{{ (isset($objComissao)?$objComissao->nome:old('nomeComissao')) }}" required><br>
    <label for="emailComissao">E-mail: </label><input type="text" class="inputBorder" style="left:99px;" name="emailComissao" id="emailComissao" size="50" value="{{ (isset($objComissao)?$objComissao->email:old('emailComissao')) }}" required><br>
    <label for="papelComissao">Papel na Comissão: </label>
    <select name="papelComissao" id="papelComissao" class="inputBorder" style="left:5px;">
    <option value="">Selecione</option>
    <option value="COORDENADOR" @if (old('papelComissao') == "COORDENADOR" || (isset($objComissao) && $objComissao->papel == "COORDENADOR")) selected @endif >Coordenador</option>
    <option value="VICE-COORDENADOR" @if (old('papelComissao') == "VICE-COORDENADOR" || (isset($objComissao) && $objComissao->papel == "VICE-COORDENADOR")) selected @endif >Vice-Coordenador</option>
    <option value="MEMBRO" @if (old('papelComissao') == "MEMBRO" || (isset($objComissao) && $objComissao->papel == "MEMBRO")) selected @endif >Membro</option>
    </select>
    <div class="erro">{{ $errors->has('papelComissao') ? $errors->first('papelComissao'):null }}</div><br/>
    @if (!empty($objComissao->assinatura))
    <p style="color:red">
    ASSINATURA JÁ CADASTRADA. <a href="upload/assinatura/{{ $objComissao->assinatura }}">Baixar assinatura</a><br/>
    Se quiser substituir a assinatura, basta fazer upload de novo arquivo:
    </p>
    @endif
    <label for="assinatura">Assinatura: </label><input type="file" class="inputBorder" style="left:68px;" name="assinatura" id="assinatura">
    <div class="erro">{{ $errors->has('assinatura') ? $errors->first('assinatura'):null }}</div><br/><br/>
    <input type="submit" id="buttonSubmit" value="@if (isset($objComissao)) Alterar @else Cadastrar @endif"/>
    @if (isset($objComissao)) <button id="buttonSubmit" style="width:auto;border:none;" onclick="windows.location='{{ route('orientador.index') }}'; return false;">Cancelar</button> @endif
</form>
<script src="js/cadastroComissao.js"></script>
<script>
    setTimeout(function() {
                $('#msg').fadeOut('fast');
              }, 3000);
</script>

@endsection