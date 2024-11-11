@php
$dadosDefesa->first()->dataDefesa1 = date_create($dadosDefesa->first()->dataDefesa1);
$dadosDefesa->first()->dataDefesa2 = date_create($dadosDefesa->first()->dataDefesa2);
$dadosDefesa->first()->dataDefesa3 = date_create($dadosDefesa->first()->dataDefesa3);
if (!empty($dadosDefesa->first()->dataEscolhida)) {
    $dadosDefesa->first()->dataEscolhida = date_create($dadosDefesa->first()->dataEscolhida);
}
@endphp

<div style="border: solid 1px black; padding: 5px 5px 5px 5px;">
@if(is_null($dadosDefesa->first()->aprovacao_orientador) && !$validacaoTcc)
<p style="color:red">AGUARDANDO VALIDAÇÃO DE BANCA PELO ORIENTADOR</p>
@elseif (!$dadosDefesa->first()->aprovacao_orientador)
<p style="color:red">AGUARDANDO CORREÇÃO DA BANCA PELO ALUNO</p>
@endif

<p><b>Composição da Banca</b></p>
@if ($dadosMonografia->status == "AGUARDANDO DEFESA" && date_create($dadosDefesa->first()->dataEscolhida) <= date_create('now') && $dadosMonografia->orientadores->first()->email == auth()->user()->email)
<form id="formValDefesa" action="{{ route('graduacao.validaDefesa') }}" method="post">
    @csrf
    <label style="background-color: #9b51e0; color:white; font-weight: bold; width: 100%; text-align: center;">Selecione abaixo as pessoas que participaram da Banca:</label>
    <input type="hidden" name="idMonografia" value="{{ isset($dadosMonografia->id)?$dadosMonografia->id:null }}">
@endif
@foreach($dadosBanca as $key=>$mBanca)
<p>
@if ($dadosMonografia->status == "AGUARDANDO DEFESA" && $dadosDefesa->first()->dataEscolhida <= date_create('now') && $dadosMonografia->orientadores->first()->email == auth()->user()->email) 
<input type="checkbox" id="{{ $mBanca->papel."_".$key }}" name="membro[]" value="{{ $mBanca->id }}">
@endif
{{ ($mBanca->papel=="MEMBRO")?$key++."º":"" }} {{$mBanca->papel}}: {{ $mBanca->nome }}
                        , E-mail: {{ $mBanca->email }}
                        , Telefone: {{ $mBanca->telefone }} 
                        , Instituição de Vínculo: {{ $mBanca->instituicao_vinculo }}   
</p>
@endforeach
<div style="color:red;">{{$errors->any() ? $errors->first('msg'):null}}</div>
@if ($dadosMonografia->status == "AGUARDANDO DEFESA" && $dadosDefesa->first()->dataEscolhida <= date_create('now') && $dadosMonografia->orientadores->first()->email == auth()->user()->email)
    <p><input type="submit" id="validarDefesa" value="Validar Defesa"></p>
</form>
@endif

@if (!empty($dadosDefesa->first()->dataEscolhida))
    <p><b>Data da Defesa:</b> {{ $dadosDefesa->first()->dataEscolhida->format('d/m/Y H:i') }} 
    @if ($dadosMonografia->status == "AGUARDANDO DEFESA" && (strpos($userLogado,"Graduacao") !== false || strpos($userLogado,"Admin") !== false) && $dadosDefesa->first()->dataEscolhida > date_create('now'))
    <input type="checkbox" value="1" id="modificaData" {{ ($errors->has('txtData') || $errors->has('txtHora'))?'checked':null }}> Modificar a data da defesa?
    <div id="formDataBancaM" style="{{ ($errors->has('txtData') || $errors->has('txtHora'))?null:'display: none;' }}">
        <form id="formModificaData" action="{{ route('graduacao.alteradata') }}" method="post">
            @csrf
            @method('PUT')
            <input type="hidden" name="idDefesa" value="{{$dadosDefesa->first()->id}}">
            <label>Informe uma nova data:</label>
            <input type="text" name="txtData" value="" size="15" maxlength="10" value="{{ old('txtData') }}" style="border: solid 1px black;" placeholder="DD/MM/AAAA">
            <input type="text" name="txtHora" value="" size="10" maxlength="5" value="{{ old('txtHora') }}" style="border: solid 1px black;" placeholder="HH:MM">
            <input type="submit" value="Alterar Data" id="alteraData" style="display: inline">
            <div class="erro"> {{  $errors->has('txtData') ? $errors->first('txtData'):null }} {{  $errors->has('txtHora') ? " - ".$errors->first('txtHora'):null }} </div>
            <div id="process" title="Aguarde..."></div>
        </form>
    </div>
    @endif
    </p>
@endif

@if($validacaoTcc && empty($dadosDefesa->first()->dataEscolhida) && (strpos($userLogado,"Graduacao") !== false || strpos($userLogado,"Admin") !== false) )
    <form id="formDtBanca" action="{{ route('graduacao.validaBanca') }}" method="post">
        @csrf
        <input type="hidden" name="idDefesa" value="{{ $dadosDefesa->first()->id }}">
        <input type="hidden" name="monografiaId" value="{{ $dadosDefesa->first()->monografia_id }}">
        <div class="campo" style="margin-top: 10px;">
            <label>Escolha a data ou informe uma para agendamento da Defesa:</label>
            <select name="dataEscolhida" id="dataEscolhida" style="float: left">
                <option value="{{ $dadosDefesa->first()->dataDefesa1->format('d/m/Y H:i') }}">{{ $dadosDefesa->first()->dataDefesa1->format('d/m/Y H:i') }}</option>
                <option value="{{ $dadosDefesa->first()->dataDefesa2->format('d/m/Y H:i') }}">{{ $dadosDefesa->first()->dataDefesa2->format('d/m/Y H:i') }}</option>
                <option value="{{ $dadosDefesa->first()->dataDefesa3->format('d/m/Y H:i') }}">{{ $dadosDefesa->first()->dataDefesa3->format('d/m/Y H:i') }}</option>
            </select>
            &nbsp;
            <div style="float:left; margin-right: 150px;" id="divNovaData">
                <input type="checkbox" id="cadData" name="cadData" value="1"
                  @if (old('cadData') == 1) checked @endif> Informar Data
                
                <div id="txtData" style="display:none;float:left; margin-right: 5px;">
                    <label for="txtData">Informe uma nova data:</label>
                    <input type="text" name="txtData" value="" size="15" maxlength="10" value="{{ old('txtData') }}" style="border: solid 1px black;" placeholder="DD/MM/AAAA">
                    <input type="text" name="txtHora" value="" size="10" maxlength="5" value="{{ old('txtHora') }}" style="border: solid 1px black;" placeholder="HH:MM">
                    <div class="erro"> {{  $errors->has('txtData') ? $errors->first('txtData'):null }} {{  $errors->has('txtHora') ? " - ".$errors->first('txtHora'):null }} </div>
                </div>
            </div>
        </div>
        <div class="campo">
        <input type="submit" name="enviar" id="validarBanca" value="Validar Banca"><br/><br/>
        <div id="process" title="Aguarde..."></div>
        </div>
    </form>
@endif
</div>

<script>
    $(document).ready(function(){

        $("input[name=txtData]").mask("99/99/9999");
        $("input[name=txtHora]").mask("99:99");

        if ($('#cadData').is(':checked')) {
            $("#txtData").show();
            $('#dataEscolhida').prop( "disabled", true );
        }

        $('#cadData').click(function() {
            if ($(this).is(':checked')) {
                $("#txtData").show();
                $('#dataEscolhida').prop( "disabled", true );
            } else {
                $("#txtData").hide();
                $("input[name=txtData]").val("");
                $('#dataEscolhida').prop( "disabled", false );
            }
        });

        $('#modificaData').click(function() {
            if ($(this).is(':checked')) {
                $('#formDataBancaM').show();
            } else {
                $('#formDataBancaM').hide();
            }
        });

        $('#validarBanca').click(function() {
            $('body').css("background-color","rgba(0,0,0,0.1)");
            $(this).css("display","none");
            aguarde();
        });

        $('#alteraData').click(function() {
            $('body').css("background-color","rgba(0,0,0,0.1)");
            $(this).css("display","none");
            aguarde();
        });

        $('#validarDefesa').click(function() {
            $('body').css("background-color","rgba(0,0,0,0.1)");
            $(this).css("display","none");
            aguarde();
        });
 
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
    });
</script>
<hr/>