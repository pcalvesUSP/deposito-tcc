<div style="border: solid 1px black; padding: 10px 10px 10px 10px;">
<label style="background-color: #9b51e0; color:white; font-weight: bold; width: 100%; text-align: center;">FAÇA O UPLOAD DO TCC E INDIQUE A BANCA</label>

<form action="{{ route('aluno.salvarBanca') }}" method="post" id="formBanca" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="orientadorId" value="{{ $orientadorId }}" >
    <input type="hidden" name="monografiaId" value="{{ $monografiaId }}" >
    <div class="campo">
       <label for="path_arq_tcc">Arquivo do TCC: </label><input type="file" name="path_arq_tcc" id="path_arq_tcc">
       <div class="erro" id="erroarquivo"> {{  $errors->has('path_arq_tcc') ? $errors->first('path_arq_tcc'):null }}</div>
    </div>
    <br/>
    <div class="campo">
        <label for="data">Datas possíveis para banca (DD/MM/YYYY): </label>
        1ª Opção de Data: <input type="text" value="{{ trim(old('data1')) }}" name="data1" id="data1" maxlenght="10" size="15" style="border:solid 1px black;" placeholder="DD/MM/AAAA"><input type="text" value="{{ trim(old('horario1')) }}" name="horario1" id="horario1" maxlenght="5" size="10" style="border:solid 1px black;" placeholder="HH:MM"><br/>
        2ª Opção de Data: <input type="text" value="{{ trim(old('data2')) }}" name="data2" id="data2" maxlenght="10" size="15" style="border:solid 1px black;" placeholder="DD/MM/AAAA"><input type="text" value="{{ trim(old('horario2')) }}" name="horario2" id="horario2" maxlenght="5" size="10" style="border:solid 1px black;" placeholder="HH:MM"><br/>
        3ª Opção de Data: <input type="text" value="{{ trim(old('data3')) }}" name="data3" id="data3" maxlenght="10" size="15" style="border:solid 1px black;" placeholder="DD/MM/AAAA"><input type="text" value="{{ trim(old('horario3')) }}" name="horario3" id="horario3" maxlenght="5" size="10" style="border:solid 1px black;" placeholder="HH:MM"><br/>
        <div class="erro" id="errodata1"> {{  $errors->has('data1') || $errors->has('horario1') ? $errors->first('data1'):null }}</div>
        <div class="erro" id="errodata2"> {{  $errors->has('data2') || $errors->has('horario2') ? $errors->first('data2'):null }}</div>
        <div class="erro" id="errodata3"> {{  $errors->has('data3') || $errors->has('horario3') ? $errors->first('data3'):null }}</div>
    </div>
    <div class="campo">
        <label for="membroBanca">Informe os membros da banca, sendo o orientador o Presidente. Informe mais dois membros e 1 suplente</label>
        <p><b>1º Membro:</b></p>
        <div style="padding-top:5px; padding-bottom:5px;">
        <p>
            <span style="margin-left:109px;">Nome: <input type="text" value="{{ $dadosOrientadores->first()->orientadores()->first()->nome }}" name="membroBanca1" id="membroBanca1" size="30" style="border:solid 1px black;" readonly></span><br/>
            <span style="margin-left:112px;">Email: <input type="text" value="{{ $dadosOrientadores->first()->orientadores()->first()->email }}" name="emailBanca1" id="emailBanca1" maxlenght="100" size="30" style="background-color: #e0d1ed; border:solid 1px black;" readonly></span><br/>
            <span style="margin-left:91px;">Telefone: <input type="text" value="{{ (empty($dadosOrientadores->first()->orientadores()->first()->telefone))?old('telefone1'):$dadosOrientadores->first()->orientadores()->first()->telefone }}" name="telefone1" id="telefone1" maxlenght="12" size="15" style="background-color: #e0d1ed; border:solid 1px black;"></span><br/>
            <span style="margin-left:97px;">N.º USP: <input type="text" value="{{ $dadosOrientadores->first()->orientadores()->first()->codpes }}" name="nusp1" id="nusp1" size="15" style="background-color: #e0d1ed; border:solid 1px black;"  readonly></span></br>
            <span>Instituição de Vínculo: <input type="text" value="{{ $dadosOrientadores->first()->orientadores()->first()->instituicao_vinculo }}" name="instituicao1" id="instituicao1" size="50" maxlength="150" style="background-color: #e0d1ed; border:solid 1px black;"  readonly></span></br>
        <div class="erro" id="errotelefone1">{{  $errors->has('telefone1') ? $errors->first('telefone1'):null }}</div>
        </p>
        </div>
        <p style="background-color: #e0d1ed; margin-bottom: 0px;"><b>2º Membro:</b></p>
        <div style="background-color: #e0d1ed; padding-top:5px; padding-bottom:5px;">
            <p>
                <span style="margin-left:109px;">Nome: <input type="text" value="{{ trim(old('membroBanca2')) }}" name="membroBanca2" id="membroBanca2" maxlenght="100" size="30" style="background-color: #e0d1ed; border:solid 1px black;"></span><br/>
                <span style="margin-left:112px;">Email: <input type="text" value="{{ trim(old('emailBanca2')) }}" name="emailBanca2" id="emailBanca2" maxlenght="100" size="30" style="background-color: #e0d1ed; border:solid 1px black;"></span><br/>
                <span style="margin-left:91px;">Telefone: <input type="text" value="{{ old('telefone2') }}" name="telefone2" id="telefone2" maxlenght="12" size="15" style="background-color: #e0d1ed; border:solid 1px black;"></span><br/>
                <span style="margin-left:97px;">N.º USP: <input type="text" value="{{ trim(old('nusp2')) }}" name="nusp2" id="nusp2" size="15" style="background-color: #e0d1ed; border:solid 1px black;"></span><br/>
                <span>Instituição de Vínculo: <input type="text" value="{{ trim(old('instituicao2')) }}" name="instituicao2" id="instituicao2" size="50" maxlength="150" style="background-color: #e0d1ed; border:solid 1px black;"></span><br/>
                <div class="erro" id="erroBanca2">{{  $errors->has('membroBanca2') ? $errors->first('membroBanca2'):null }}</div>
                <div class="erro" id="erroEmailBanca2">{{  $errors->has('emailBanca2') ? $errors->first('emailBanca2'):null }}</div>
                <div class="erro" id="errotelefone2">{{  $errors->has('telefone2') ? $errors->first('telefone2'):null }}</div>
                <div class="erro" id="errinstituicao2">{{  $errors->has('instituicao2') ? $errors->first('instituicao2'):null }}</div>
            </p>
        </div>
        <p><b>3º Membro:</b></p>
        <div style="padding-top:5px; padding-bottom:5px;">
            <p>
                <span style="margin-left:109px;">Nome: <input type="text" value="{{ trim(old('membroBanca3')) }}" name="membroBanca3" id="membroBanca3" maxlenght="100" size="30" style="border:solid 1px black;"></span><br/>
                <span style="margin-left:112px;">Email: <input type="text" value="{{ trim(old('emailBanca3')) }}" name="emailBanca3" id="emailBanca3" maxlenght="100" size="30" style="border:solid 1px black;"></span><br/>
                <span style="margin-left:91px;">Telefone: <input type="text" value="{{ trim(old('telefone3')) }}" name="telefone3" id="telefone3" maxlenght="12" size="15" style="border:solid 1px black;"></span><br/>
                <span style="margin-left:97px;">N.º USP: <input type="text" value="{{ trim(old('nusp3')) }}" name="nusp3" id="nusp3" size="15" style="border:solid 1px black;"></span><br/>
                <span>Instituição de Vínculo: <input type="text" value="{{ trim(old('instituicao3')) }}" name="instituicao3" id="instituicao3" size="50" maxlength="150" style="background-color: #e0d1ed; border:solid 1px black;"></span><br/>
                <div class="erro" id="erroBanca">{{  $errors->has('membroBanca3') ? $errors->first('membroBanca3'):null }}</div>
                <div class="erro" id="erroEmailBanca3">{{  $errors->has('emailBanca3') ? $errors->first('emailBanca3'):null }}</div>
                <div class="erro" id="errotelefone3">{{  $errors->has('telefone3') ? $errors->first('telefone3'):null }}</div>
                <div class="erro" id="errinstituicao3">{{  $errors->has('instituicao3') ? $errors->first('instituicao3'):null }}</div>
            </p>
        </div>
        <p style="background-color: #e0d1ed; margin-bottom: 0px;"><b>Suplente:</b></p>
        <div style="background-color: #e0d1ed; padding-top:5px; padding-bottom:5px;">
            <p>
                <span style="margin-left:109px;">Nome: <input type="text" value="{{ trim(old('suplente')) }}" name="suplente" id="suplente" size="30" style="background-color: #e0d1ed; border:solid 1px black;"></span><br/>
                <span style="margin-left:112px;">Email: <input type="text" value="{{ trim(old('emailSuplente')) }}" name="emailSuplente" id="emailSuplente" maxlenght="100" size="30" style="background-color: #e0d1ed; border:solid 1px black;"></span><br/>
                <span style="margin-left:91px;">Telefone: <input type="text" value="{{ trim(old('telefoneSuplente')) }}" name="telefoneSuplente" id="telefoneSuplente" maxlenght="12" size="15" style="background-color: #e0d1ed; border:solid 1px black;"></span><br/>
                <span style="margin-left:97px;">N.º USP: <input type="text" value="{{ trim(old('nuspSuplente')) }}" name="nuspSuplente" id="nuspSuplente" size="15" style="background-color: #e0d1ed; border:solid 1px black;"></span><br/>
                <span>Instituição de Vínculo: <input type="text" value="{{ trim(old('instituicaoSuplente')) }}" name="instituicaoSuplente" id="instituicaoSuplente" size="50" maxlength="150" style="background-color: #e0d1ed; border:solid 1px black;"></span><br/>
                <div class="erro" id="erroSuplente">{{  $errors->has('suplente') ? $errors->first('suplente'):null }}</div>
                <div class="erro" id="erroEmailSuplente">{{  $errors->has('emailSuplente') ? $errors->first('emailSuplente'):null }}</div>
                <div class="erro" id="errotelSuplente">{{  $errors->has('telefoneSuplente') ? $errors->first('telefoneSuplente'):null }}</div>
                <div class="erro" id="errinstituicaoSuplente">{{  $errors->has('instituicaoSuplente') ? $errors->first('instituicaoSuplente'):null }}</div>
            </p>
        </div>
        <p><input type="checkbox" value="1" name="cienteOrientador" @if (old('cienteOrientador')==1) checked @endif><b> Orientador está ciente.</b></p>
        <div class="erro" id="errCienteOrientador">{{  $errors->has('cienteOrientador') ? $errors->first('cienteOrientador'):null }}</div>
    </div>
    <input type="submit" id="buttonSubmit" value="Enviar">
</form>
</div>
<hr/>

<script src="js/banca.js"></script>