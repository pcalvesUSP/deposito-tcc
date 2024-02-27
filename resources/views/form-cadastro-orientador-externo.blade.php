@extends('layouts.app')

@section('content')

@guest
        <p>Clique em "Entrar", localizado no canto direito do cabeçalho desta página, se você é aluno ou docente FCF-USP.</p>

        <p>Se vc é Orientador Externo (SEM VÍNCULO FCF-USP)
            <ul>
                <li>Para se cadastrar, <a href="{{ route('orientador.novo-cadastro') }}"> clique aqui</a></li>
                <li>Para se logar no sistema <a href="{{ route('login.externo')}}">clique aqui</a></li>
            </ul>
        </p>
        
@endguest

<h1>Auto Cadastro de Orientador Externo</h1>
<form action="{{ route('orientador.salvardados') }}" method="post" enctype="multipart/form-data" id="cadOrientExterno">
    @csrf
    <input type="hidden" name="externo" value = 1>
    <div id="divExt">
        <label for="cpfOrientador">CPF (Somente n&uacute;meros): </label><input type="text" class="inputBorder" style="left:124px;" name="cpfOrientador" id="cpfOrientador" size="20" value="{{ old('cpfOrientador') }}" required><br>
        <div class="erro" id="eCpfOrientador">{{  $errors->has('cpfOrientador') ? $errors->first('cpfOrientador'):null }}</div>
    </div>
    <label for="nuspOrientador">N.º USP (se houver): </label><input type="text" class="inputBorder" style="left:158px;" name="nuspOrientador" id="nuspOrientador" size="20" value="{{ old('nuspOrientador') }}"><br>
    <label for="nomeOrientador">Nome: </label><input type="text" class="inputBorder" style="left:255px;" name="nomeOrientador" id="nomeOrientador" size="50" value="{{ old('nomeOrientador') }}" required><br>
    <div class="erro" id="eNomeOrientador">{{  $errors->has('nomeOrientador') ? $errors->first('nomeOrientador'):null }}</div>
    <label for="emailOrientador">E-mail: </label><input type="text" class="inputBorder" style="left:252px;" name="emailOrientador" id="emailOrientador" size="50" value="{{ old('emailOrientador') }}" required><br>
    <div class="erro" id="eEmailOrientador">{{  $errors->has('emailOrientador') ? $errors->first('emailOrientador'):null }}</div>
    <label for="telefoneOrientador">Telefone: </label><input type="text" class="inputBorder" style="left:237px;" name="telefoneOrientador" id="telefoneOrientador" size="30" value="{{ (isset($objOrientador)?$objOrientador->telefone:old('telefoneOrientador')) }}" required maxlenght="15"><br>
    <div class="erro" id="eTelefoneOrientador">{{  $errors->has('telefoneOrientador') ? $errors->first('telefoneOrientador'):null }}</div>
    <label for="instituicaoOrientador">Instituição de Vínculo: </label><input type="text" class="inputBorder" style="left:145px;" name="instituicaoOrientador" id="instituicaoOrientador" size="50" value="{{ (isset($objOrientador)?$objOrientador->instituicao_vinculo:old('instituicaoOrientador')) }}" required><br>
    <div class="erro" id="eInstituicaoOrientador">{{  $errors->has('instituicaoOrientador') ? $errors->first('instituicaoOrientador'):null }}</div>
    <label for="comprovante_vinculo">Comprovante de vínculo com a Instituição: </label><input type="file" name="comprovante_vinculo" id="comprovante_vinculo" required><br>
    <div class="erro" id="ecomprovante_vinculo">{{  $errors->has('comprovante_vinculo') ? $errors->first('comprovante_vinculo'):null }}</div>
    <label for="linkLattes">Link Lattes: </label><input type="text" class="inputBorder" style="left:219px;" name="linkLattes" id="linkLattes" size="50" value="{{ old('linkLattes') }}" required><br>
    <div class="erro" id="eLinkLattes">{{  $errors->has('linkLattes') ? $errors->first('linkLattes'):null }}</div>
    <div id="campo">
     <label for="area_atuacao" style="vertical-align: top;">Área de Atuação: </label>
     <textarea class="inputBorder" style="left:174px;" rows="10" cols="50" name="area_atuacao" id="area_atuacao" required>
        {{ old('area_atuacao') }}
     </textarea><br/>
     <div class="erro" id="eAreaAtuacao">{{  $errors->has('areaAtuacao') ? $errors->first('areaAtuacao'):null }}</div>
    </div>    
    <input type="submit" id="buttonSubmit" value="Cadastrar"/>
    <div id="process" title="Aguarde, pode levar algum tempo..."></div>
</form>
<script src="js/cadastroOrientador.js"></script>
<script>
    
    $(document).ready(function(){

        $( "#process" ).dialog({
                autoOpen: false,
                width: 400,
                resizable: false,
                draggable: false,
                close: function(){
                    // executa uma ação ao fechar
                    //alert("você fechou a janela");
                    aguarde();
                }
        });
            
        function aguarde() {
            $("#process").dialog("open").html("<img src='{{ asset('/vendor/laravel-usp-theme/fcf/images/aguarde.gif') }}'>");
            
            /*$.ajax({
                    type: 'POST',
                    url:'url.php',
                    data:{ 'id': 999999 },
                    beforeSend: function() {
                        $( "#process" ).dialog( "open" ).html("<p>Aguarde a validação e envio de e-mails para todos os Membros da Banca</p>");
                        //$('body').css("background-color","rgba(0,0,0,0.1)")
                    }
                }).done(function(data) {
                    $("#process").html(data);
                });*/
        } 

        $('#buttonSubmit').click(function() {
            $('body').css("background-color","rgba(0,0,0,0.1)");
            $(this).css("display","none");
            $('#cadOrientExterno').submit();
            
            aguarde();
            
	    });
    });
  </script>

@endsection