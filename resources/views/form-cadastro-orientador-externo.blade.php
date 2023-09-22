@extends('layouts.app')

@section('content')

@guest
        <p>Clique em "Entrar" se você é aluno ou docente USP.</p>

        <p>Se vc é Orientador Externo (SEM VÍNCULO USP)
            <ul>
                <li>Para se cadastrar, <a href="{{ route('orientador.novo-cadastro') }}"> clique aqui</a></li>
                <li>Para se logar no sistema <a href="{{ route('login.externo')}}">clique aqui</a></li>
            </ul>
        </p>
        
@endguest

<h1>Auto Cadastro de Orientador Externo</h1>
<form action="{{ route('orientador.salvardados') }}" method="post">
    @csrf
    <input type="hidden" name="externo" value = 1>
    <div id="divExt">
        <label for="cpfOrientador">CPF (Somente n&uacute;meros): </label><input type="text" class="inputBorder" style="left:5px;" name="cpfOrientador" id="cpfOrientador" size="20" value="{{ old('cpfOrientador') }}" required><br>
        <div class="erro" id="eCpfOrientador">{{  $errors->has('cpfOrientador') ? $errors->first('cpfOrientador'):null }}</div>
    </div>
    <label for="nomeOrientador">Nome: </label><input type="text" class="inputBorder" style="left:136px;" name="nomeOrientador" id="nomeOrientador" size="50" value="{{ old('nomeOrientador') }}" required><br>
    <div class="erro" id="eNomeOrientador">{{  $errors->has('nomeOrientador') ? $errors->first('nomeOrientador'):null }}</div>
    <label for="emailOrientador">E-mail: </label><input type="text" class="inputBorder" style="left:133px;" name="emailOrientador" id="emailOrientador" size="50" value="{{ old('emailOrientador') }}" required><br>
    <div class="erro" id="eEmailOrientador">{{  $errors->has('emailOrientador') ? $errors->first('emailOrientador'):null }}</div>
    <label for="telefoneOrientador">Telefone: </label><input type="text" class="inputBorder" style="left:118px;" name="telefoneOrientador" id="telefoneOrientador" size="30" value="{{ (isset($objOrientador)?$objOrientador->telefone:old('telefoneOrientador')) }}" required maxlenght="15"><br>
    <div class="erro" id="eTelefoneOrientador">{{  $errors->has('telefoneOrientador') ? $errors->first('telefoneOrientador'):null }}</div>
    <label for="instituicaoOrientador">Instituição de Vínculo: </label><input type="text" class="inputBorder" style="left:25px;" name="instituicaoOrientador" id="instituicaoOrientador" size="50" value="{{ (isset($objOrientador)?$objOrientador->instituicao_vinculo:old('instituicaoOrientador')) }}" required><br>
    <div class="erro" id="eInstituicaoOrientador">{{  $errors->has('instituicaoOrientador') ? $errors->first('instituicaoOrientador'):null }}</div>
    <label for="linkLattes">Link Lattes: </label><input type="text" class="inputBorder" style="left:101px;" name="linkLattes" id="linkLattes" size="50" value="{{ old('linkLattes') }}" required><br>
    <div class="erro" id="eLinkLattes">{{  $errors->has('linkLattes') ? $errors->first('linkLattes'):null }}</div>
    <div id="campo">
     <label for="area_atuacao" style="vertical-align: top;">Área de Atuação: </label>
     <textarea class="inputBorder" style="left:55px;" rows="10" cols="50" name="area_atuacao" id="area_atuacao" required>
        {{ old('area_atuacao') }}
     </textarea><br/>
     <div class="erro" id="eAreaAtuacao">{{  $errors->has('areaAtuacao') ? $errors->first('areaAtuacao'):null }}</div>
    </div>
    <input type="submit" id="buttonSubmit" value="Cadastrar"/>
</form>
<script src="js/cadastroOrientador.js"></script>

@endsection