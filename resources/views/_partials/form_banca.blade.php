@php
if (!empty($dadosDefesa) && !$dadosDefesa->isEmpty()) {
    $dadosDefesa->first()->dataDefesa1 = date_create($dadosDefesa->first()->dataDefesa1);
    $dadosDefesa->first()->dataDefesa2 = date_create($dadosDefesa->first()->dataDefesa2);
    $dadosDefesa->first()->dataDefesa3 = date_create($dadosDefesa->first()->dataDefesa3);
    if (!empty($dadosDefesa->first()->dataEscolhida)) {
        $dadosDefesa->first()->dataEscolhida = date_create($dadosDefesa->first()->dataEscolhida);
    }
}
@endphp
<div style="border: solid 1px black; padding: 10px 10px 10px 10px;">
<label style="background-color: #9b51e0; color:white; font-weight: bold; width: 100%; text-align: center;">@if($aprovaBanca) VERIFIQUE OS MEMBROS DA BANCA @else FAÇA O UPLOAD DO TCC E INDIQUE A BANCA @endif </label>

<form action="@if($uploadTcc && (empty($dadosDefesa) || $dadosDefesa->isEmpty())) {{ route('aluno.salvarBanca') }} @elseif ($aprovaBanca) {{ route('orientador.aprova.banca'); }} @else {{ route('aluno.corrigirBanca') }} @endif" method="post" id="formBanca" enctype="multipart/form-data">
    @csrf
    @if ($uploadTcc && (!empty($dadosDefesa) && !$dadosDefesa->isEmpty() && !is_null($dadosDefesa->first()->aprovacao_orientador) && $dadosDefesa->first()->aprovacao_orientador == 0) )
    @method('PUT')
    @endif
    <input type="hidden" name="orientadorId" value="{{ $orientadorId }}" >
    <input type="hidden" name="monografiaId" value="{{ $monografiaId }}" >
    @if (!$aprovaBanca)
    <div class="campo">
        @if (!is_null($dadosMonografia->path_arq_tcc))
            <p><b>Para visualizar o TCC, <a href="upload/{{ $dadosMonografia->path_arq_tcc }}">baixe o trabalho</a></b></p>
        @endif
        <label for="path_arq_tcc">{{ (is_null($dadosMonografia->path_arq_tcc))? 'Upload de arquivo TCC final:' : 'Alterar arquivo TCC'}} </label><input type="file" name="path_arq_tcc" id="path_arq_tcc">
        <div class="erro" id="erroarquivo"> {{  $errors->has('path_arq_tcc') ? $errors->first('path_arq_tcc'):null }}</div><br/>
        @if (!is_null($dadosMonografia->aluno_autoriza_publicar)) <p style="color:red;">Caso queira modificar a opção anteriormente escolhida, selecione abaixo</p> @endif
        <div style="border: solid 1px black; margin: 10px 0px 10px 0px;">
            <p>Autorizo, na qualidade de titular dos direitos morais e patrimoniais de autor que recaem sobre o Trabalho de Conclusão de 
                Curso defendido, com fundamento nas disposições da lei Federal 9610, de 19/2/1998, a Faculdade de Ciências Farmacêuticas da 
                Universidade de São Paulo a publicar em ambiente digital Institucional [Biblioteca Digital de trabalhos Acadêmicos, pela 
                Resolução CoCEx-COG nº 7497, de 09/04/2018], sem ressarcimento dos direitos autorais, o texto integral do trabalho defendido, 
                em formato PDF, a título de divulgação da produção acadêmica de graduação gerado pela Universidade.</p>
            <p><input type="radio" value="1" name="aluno_autoriza_publicar" @if ($dadosMonografia->aluno_autoriza_publicar == 1 ||  old('aluno_autoriza_publicar') == "1") checked @endif> S &nbsp;&nbsp;&nbsp; 
                <input type="radio" value="0" name="aluno_autoriza_publicar" @if ((!is_null($dadosMonografia->aluno_autoriza_publicar) && $dadosMonografia->aluno_autoriza_publicar == 0) ||  old('aluno_autoriza_publicar') == "0") checked @endif> N
            </p>
            <p><u><b>OBS:</b> Essa autorização só será valida se o TCC for indicado pela Comissão Julgadora no dia da defesa.</u></p>
            <div class="erro" id="ealuno_autoriza_publicar"> {{  $errors->has('aluno_autoriza_publicar') ? $errors->first('aluno_autoriza_publicar'):null }}</div>
        </div>
    
    @if ($dadosMonografia->aluno_autoriza_publicar)
    <p><b>Autorizei na qualidade de titular dos direitos morais e patrimoniais de autor que recaem sobre o Trabalho de Conclusão de 
       Curso defendido, com fundamento nas disposições da lei Federal 9610, de 19/2/1998, a Faculdade de Ciências Farmacêuticas da 
       Universidade de São Paulo a publicar em ambiente digital Institucional [Biblioteca Digital de trabalhos Acadêmicos, pela 
       Resolução CoCEx-COG nº 7497, de 09/04/2018], sem ressarcimento dos direitos autorais, o texto integral do trabalho defendido, 
       em formato PDF, a título de divulgação da produção acadêmica de graduação gerado pela Universidade.<br/>
       <u>Tenho ciência que essa autorização só será valida se o TCC for indicado pela Comissão Julgadora no dia da defesa.</u>
       </b>
    </p>
    @elseif (!is_null($dadosMonografia->aluno_autoriza_publicar))
    <p><b>Na qualidade de titular dos direitos morais e patrimoniais de autor que recaem sobre o Trabalho de Conclusão de 
       Curso defendido, não autorizei a publicação do TCC em ambiente digital Institucional [Biblioteca Digital de trabalhos Acadêmicos, pela 
       Resolução CoCEx-COG nº 7497, de 09/04/2018].<br/>
       <u>Tenho ciência que essa decisão não poderá ser revista após receber a nota da Defesa do TCC.</u></b>
    </p>
    @endif
    </div>
    <br/>
    <div class="campo">
        <label for="data">Datas possíveis para banca (DD/MM/YYYY): </label>
        1ª Opção de Data: <input type="text" value="{{ (!empty($dadosDefesa) && !$dadosDefesa->isEmpty()) ? $dadosDefesa->first()->dataDefesa1->format('d/m/Y') : trim(old('data1')) }}" @if($aprovaBanca) readonly @endif name="data1" id="data1" maxlenght="10" size="15" style="border:solid 1px black;" placeholder="DD/MM/AAAA"><input type="text" value="{{ (!empty($dadosDefesa) && !$dadosDefesa->isEmpty()) ? $dadosDefesa->first()->dataDefesa1->format('H:i') : trim(old('horario1')) }}" @if($aprovaBanca) readonly @endif name="horario1" id="horario1" maxlenght="5" size="10" style="border:solid 1px black;" placeholder="HH:MM"><br/>
        2ª Opção de Data: <input type="text" value="{{ (!empty($dadosDefesa) && !$dadosDefesa->isEmpty()) ? $dadosDefesa->first()->dataDefesa2->format('d/m/Y') : trim(old('data2')) }}" @if($aprovaBanca) readonly @endif name="data2" id="data2" maxlenght="10" size="15" style="border:solid 1px black;" placeholder="DD/MM/AAAA"><input type="text" value="{{ (!empty($dadosDefesa) && !$dadosDefesa->isEmpty()) ? $dadosDefesa->first()->dataDefesa2->format('H:i') : trim(old('horario2')) }}" @if($aprovaBanca) readonly @endif name="horario2" id="horario2" maxlenght="5" size="10" style="border:solid 1px black;" placeholder="HH:MM"><br/>
        3ª Opção de Data: <input type="text" value="{{ (!empty($dadosDefesa) && !$dadosDefesa->isEmpty()) ? $dadosDefesa->first()->dataDefesa3->format('d/m/Y') : trim(old('data3')) }}" @if($aprovaBanca) readonly @endif name="data3" id="data3" maxlenght="10" size="15" style="border:solid 1px black;" placeholder="DD/MM/AAAA"><input type="text" value="{{ (!empty($dadosDefesa) && !$dadosDefesa->isEmpty()) ? $dadosDefesa->first()->dataDefesa3->format('H:i') : trim(old('horario3')) }}" @if($aprovaBanca) readonly @endif name="horario3" id="horario3" maxlenght="5" size="10" style="border:solid 1px black;" placeholder="HH:MM"><br/>
        <div class="erro" id="errodata1"> {{  $errors->has('data1') || $errors->has('horario1') ? $errors->first('data1'):null }}</div>
        <div class="erro" id="errodata2"> {{  $errors->has('data2') || $errors->has('horario2') ? $errors->first('data2'):null }}</div>
        <div class="erro" id="errodata3"> {{  $errors->has('data3') || $errors->has('horario3') ? $errors->first('data3'):null }}</div>
    </div>
    @endif
    <div class="campo">
        <label for="membroBanca">@if($aprovaBanca) Verifique se está de acordo com os membros da banca @else Informe os membros da banca, sendo o orientador o Presidente. Informe mais dois membros e 1 suplente @endif </label>
        <p><b>1º Membro:</b></p>
        <div style="padding-top:5px; padding-bottom:5px;">
        <p>
            <span style="margin-left:109px;">Nome: <input type="text" value="{{ $dadosOrientadores->first()->orientadores()->first()->nome }}" name="membroBanca1" id="membroBanca1" size="30" style="border:solid 1px black;" readonly></span><br/>
            <span style="margin-left:112px;">Email: <input type="text" value="{{ $dadosOrientadores->first()->orientadores()->first()->email }}" name="emailBanca1" id="emailBanca1" maxlenght="100" size="30" style="background-color: #e0d1ed; border:solid 1px black;" readonly></span><br/>
            <span style="margin-left:91px;">Telefone: <input type="text" value="{{ (empty($dadosOrientadores->first()->orientadores()->first()->telefone))?old('telefone1'):$dadosOrientadores->first()->orientadores()->first()->telefone }}" name="telefone1" id="telefone1" maxlenght="12" size="15" style="background-color: #e0d1ed; border:solid 1px black;" @if ($aprovaBanca) readonly @endif></span><br/>
            <span style="margin-left:97px;">N.º USP: <input type="text" value="{{ $dadosOrientadores->first()->orientadores()->first()->codpes }}" name="nusp1" id="nusp1" size="15" style="background-color: #e0d1ed; border:solid 1px black;"  readonly></span></br>
            <span>Instituição de Vínculo: <input type="text" value="{{ $dadosOrientadores->first()->orientadores()->first()->instituicao_vinculo }}" name="instituicao1" id="instituicao1" size="50" maxlength="150" style="background-color: #e0d1ed; border:solid 1px black;"  readonly></span></br>
        <div class="erro" id="errotelefone1">{{  $errors->has('telefone1') ? $errors->first('telefone1'):null }}</div>
        </p>
        </div>
        <p style="background-color: #e0d1ed; margin-bottom: 0px;"><b>2º Membro:</b></p>
        <div style="background-color: #e0d1ed; padding-top:5px; padding-bottom:5px;">
            <p>
                <span style="margin-left:109px;">Nome: <input type="text" value="{{ (!empty($dadosBanca) && !$dadosBanca->isEmpty()) ? $dadosBanca[1]->nome : trim(old('membroBanca2')) }}" @if ($aprovaBanca) readonly @endif name="membroBanca2" id="membroBanca2" maxlenght="100" size="30" style="background-color: #e0d1ed; border:solid 1px black;"></span><br/>
                <span style="margin-left:112px;">Email: <input type="text" value="{{ (!empty($dadosBanca) && !$dadosBanca->isEmpty()) ? $dadosBanca[1]->email : trim(old('emailBanca2')) }}" @if ($aprovaBanca) readonly @endif name="emailBanca2" id="emailBanca2" maxlenght="100" size="30" style="background-color: #e0d1ed; border:solid 1px black;"></span><br/>
                <span style="margin-left:91px;">Telefone: <input type="text" value="{{ (!empty($dadosBanca) && !$dadosBanca->isEmpty()) ? $dadosBanca[1]->telefone : old('telefone2') }}" @if ($aprovaBanca) readonly @endif name="telefone2" id="telefone2" maxlenght="12" size="15" style="background-color: #e0d1ed; border:solid 1px black;"></span><br/>
                <span style="margin-left:97px;">N.º USP: <input type="text" value="{{ (!empty($dadosBanca) && !$dadosBanca->isEmpty()) ? $dadosBanca[1]->codpes : trim(old('nusp2')) }}" @if ($aprovaBanca) readonly @endif name="nusp2" id="nusp2" size="15" style="background-color: #e0d1ed; border:solid 1px black;"></span> <span style="font-size:12px">(Caso o membro indicado não tenha N.º USP, poderá ser deixado em branco)</span><br/>
                <span>Instituição de Vínculo: <input type="text" value="{{ (!empty($dadosBanca) && !$dadosBanca->isEmpty()) ? $dadosBanca[1]->instituicao_vinculo : trim(old('instituicao2')) }}" name="instituicao2" id="instituicao2" size="50" maxlength="150" style="background-color: #e0d1ed; border:solid 1px black;"></span><br/>
                <div class="erro" id="erroBanca2">{{  $errors->has('membroBanca2') ? $errors->first('membroBanca2'):null }}</div>
                <div class="erro" id="erroEmailBanca2">{{  $errors->has('emailBanca2') ? $errors->first('emailBanca2'):null }}</div>
                <div class="erro" id="errotelefone2">{{  $errors->has('telefone2') ? $errors->first('telefone2'):null }}</div>
                <div class="erro" id="errinstituicao2">{{  $errors->has('instituicao2') ? $errors->first('instituicao2'):null }}</div>
            </p>
        </div>
        <p><b>3º Membro:</b></p>
        <div style="padding-top:5px; padding-bottom:5px;">
            <p>
                <span style="margin-left:109px;">Nome: <input type="text" value="{{ (!empty($dadosBanca) && !$dadosBanca->isEmpty()) ? $dadosBanca[2]->nome : trim(old('membroBanca3')) }}" @if ($aprovaBanca) readonly @endif name="membroBanca3" id="membroBanca3" maxlenght="100" size="30" style="border:solid 1px black;"></span><br/>
                <span style="margin-left:112px;">Email: <input type="text" value="{{ (!empty($dadosBanca) && !$dadosBanca->isEmpty()) ? $dadosBanca[2]->email : trim(old('emailBanca3')) }}" @if ($aprovaBanca) readonly @endif name="emailBanca3" id="emailBanca3" maxlenght="100" size="30" style="border:solid 1px black;"></span><br/>
                <span style="margin-left:91px;">Telefone: <input type="text" value="{{ (!empty($dadosBanca) && !$dadosBanca->isEmpty()) ? $dadosBanca[2]->telefone : trim(old('telefone3')) }}" @if ($aprovaBanca) readonly @endif name="telefone3" id="telefone3" maxlenght="12" size="15" style="border:solid 1px black;"></span><br/>
                <span style="margin-left:97px;">N.º USP: <input type="text" value="{{ (!empty($dadosBanca) && !$dadosBanca->isEmpty()) ? $dadosBanca[2]->codpes : trim(old('nusp3')) }}" @if ($aprovaBanca) readonly @endif name="nusp3" id="nusp3" size="15" style="border:solid 1px black;"></span> <span style="font-size:12px">(Caso o membro indicado não tenha N.º USP, poderá ser deixado em branco)</span><br/>
                <span>Instituição de Vínculo: <input type="text" value="{{ (!empty($dadosBanca) && !$dadosBanca->isEmpty()) ? $dadosBanca[2]->instituicao_vinculo : trim(old('instituicao3')) }}" @if ($aprovaBanca) readonly @endif name="instituicao3" id="instituicao3" size="50" maxlength="150" style="background-color: #e0d1ed; border:solid 1px black;"></span><br/>
                <div class="erro" id="erroBanca">{{  $errors->has('membroBanca3') ? $errors->first('membroBanca3'):null }}</div>
                <div class="erro" id="erroEmailBanca3">{{  $errors->has('emailBanca3') ? $errors->first('emailBanca3'):null }}</div>
                <div class="erro" id="errotelefone3">{{  $errors->has('telefone3') ? $errors->first('telefone3'):null }}</div>
                <div class="erro" id="errinstituicao3">{{  $errors->has('instituicao3') ? $errors->first('instituicao3'):null }}</div>
            </p>
        </div>
        <p style="background-color: #e0d1ed; margin-bottom: 0px;"><b>Suplente:</b></p>
        <div style="background-color: #e0d1ed; padding-top:5px; padding-bottom:5px;">
            <p>
                <span style="margin-left:109px;">Nome: <input type="text" value="{{ (!empty($dadosBanca) && !$dadosBanca->isEmpty()) ? $dadosBanca[3]->nome : trim(old('suplente')) }}" @if ($aprovaBanca) readonly @endif name="suplente" id="suplente" size="30" style="background-color: #e0d1ed; border:solid 1px black;"></span><br/>
                <span style="margin-left:112px;">Email: <input type="text" value="{{ (!empty($dadosBanca) && !$dadosBanca->isEmpty()) ? $dadosBanca[3]->email : trim(old('emailSuplente')) }}" @if ($aprovaBanca) readonly @endif name="emailSuplente" id="emailSuplente" maxlenght="100" size="30" style="background-color: #e0d1ed; border:solid 1px black;"></span><br/>
                <span style="margin-left:91px;">Telefone: <input type="text" value="{{ (!empty($dadosBanca) && !$dadosBanca->isEmpty()) ? $dadosBanca[3]->telefone : trim(old('telefoneSuplente')) }}" @if ($aprovaBanca) readonly @endif name="telefoneSuplente" id="telefoneSuplente" maxlenght="12" size="15" style="background-color: #e0d1ed; border:solid 1px black;"></span><br/>
                <span style="margin-left:97px;">N.º USP: <input type="text" value="{{ (!empty($dadosBanca) && !$dadosBanca->isEmpty()) ? $dadosBanca[3]->codpes : trim(old('nuspSuplente')) }}" @if ($aprovaBanca) readonly @endif name="nuspSuplente" id="nuspSuplente" size="15" style="background-color: #e0d1ed; border:solid 1px black;"></span> <span style="font-size:12px">(Caso o membro indicado não tenha N.º USP, poderá ser deixado em branco)</span><br/>
                <span>Instituição de Vínculo: <input type="text" value="{{ (!empty($dadosBanca) && !$dadosBanca->isEmpty()) ? $dadosBanca[3]->instituicao_vinculo : trim(old('instituicaoSuplente')) }}" @if ($aprovaBanca) readonly @endif name="instituicaoSuplente" id="instituicaoSuplente" size="50" maxlength="150" style="background-color: #e0d1ed; border:solid 1px black;"></span><br/>
                <div class="erro" id="erroSuplente">{{  $errors->has('suplente') ? $errors->first('suplente'):null }}</div>
                <div class="erro" id="erroEmailSuplente">{{  $errors->has('emailSuplente') ? $errors->first('emailSuplente'):null }}</div>
                <div class="erro" id="errotelSuplente">{{  $errors->has('telefoneSuplente') ? $errors->first('telefoneSuplente'):null }}</div>
                <div class="erro" id="errinstituicaoSuplente">{{  $errors->has('instituicaoSuplente') ? $errors->first('instituicaoSuplente'):null }}</div>
            </p>
        </div>
    </div>
    @if($aprovaBanca)
    <div class="campo">
        <label style="color: red;">Estou de acordo com a banca informada</label>
        <p><b>Caso não esteja de acordo, um e-mail será enviado para o aluno ajustar a banca</b></p>
        <input type="radio" name="aprovacao_orientador_banca" id="aprovacao_orientador_banca_s" value="1"> SIM <input type="radio" name="aprovacao_orientador_banca" id="aprovacao_orientador_banca_n" value="0"> NÃO
        <div id="orientacao" style="display: none;">
        <br/> <label>Orientações para Correção:</label> 
        <textarea name="correcao_banca" cols="50" rows="3"></textarea>
        </div>
    </div>
    @endif
    <input type="submit" id="buttonSubmit" value="Enviar" onclick="$(this).css({'display','none'});">
</form>
</div>
<hr/>

<script src="js/banca.js"></script>