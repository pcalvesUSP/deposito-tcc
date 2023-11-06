<div style="border: solid 1px black; padding: 5px 5px 5px 5px;">
@if (strpos($userLogado,"Orientador") !== false && ($dadosMonografia->status == "AGUARDANDO NOTA DO TCC" || $dadosMonografia->status == "AGUARDANDO NOTA DO PROJETO"))
<label style="background-color: #9b51e0; color:white; font-weight: bold; width: 100%; text-align: center;">{{($dadosMonografia->status == "AGUARDANDO NOTA DO PROJETO")?'Informe a Nota do Projeto do Aluno':'Informe a Nota do TCC do Aluno'}}</label>
@else
<label style="font-weight: bold; width: 100%; text-align: center;">NOTAS</label>
@endif
<form id="form_nota_{{ $numUSPAluno }}" method="post" action="{{ route('orientador.notas') }}">
    @csrf
    <input type="hidden" name="nuspAluno" value="{{ $numUSPAluno }}">
    <input type="hidden" name="idTccNota" value="{{ $dadosMonografia->id }}">
    @if ($dadosMonografia->curriculo == 9013)
    <div id="campo">
        <label style="background-color: #9b51e0; color:white; font-weight: bold; text-align: right; margin-left: 105px;">Nota do Projeto</label>
        Nota: <input @if($dadosMonografia->status == "AGUARDANDO NOTA DO PROJETO" && strpos($userLogado,"Orientador") !== false) @readonly(false) @else class="inputReadonly" readonly @endif type="text" name="projeto_nota" size="10" value="{{ !empty($dadosNotasProjeto->first()->nota)?$dadosNotasProjeto->first()->nota:old('projeto_nota') }}" style="border: solid 1px black;">&nbsp; 
        Frequência: <input @if($dadosMonografia->status == "AGUARDANDO NOTA DO PROJETO" && strpos($userLogado,"Orientador") !== false) @readonly(false) @else class="inputReadonly" readonly @endif type="text" name="projeto_freq" size="10" value="{{ !empty($dadosNotasProjeto->first()->frequencia)?$dadosNotasProjeto->first()->frequencia:old('projeto_freq') }}" style="border: solid 1px black;">%
        <div class="erro" id="errodata1"> {{  $errors->has('projeto_nota') || $errors->has('projeto_freq') ? $errors->first('projeto_nota')." ".$errors->first('projeto_freq'):null }}</div>
    </div>
    @endif
    <div id="campo">
        <label style="background-color: #9b51e0; color:white; font-weight: bold; text-align: right;">Média Final da Defesa do TCC</label>
        Nota: <input @if($dadosMonografia->status == "AGUARDANDO NOTA DO TCC" && strpos($userLogado,"Orientador") !== false) @readonly(false) @else class="inputReadonly" readonly @endif type="text" name="tcc_nota" size="10" value="{{ !empty($dadosNotasTcc->first()->nota)?$dadosNotasTcc->first()->nota:old('tcc_nota') }}" style="border: solid 1px black;">&nbsp; 
        Frequência: <input @if($dadosMonografia->status == "AGUARDANDO NOTA DO TCC" && strpos($userLogado,"Orientador") !== false) @readonly(false) @else class="inputReadonly" readonly @endif  type="text" name="tcc_freq" size="10" value="{{ !empty($dadosNotasTcc->first()->frequencia)?$dadosNotasTcc->first()->frequencia:old('tcc_freq') }}" style="border: solid 1px black;">%
        <div class="erro" id="errodata2"> {{  $errors->has('tcc_nota') || $errors->has('tcc_freq') ? $errors->first('tcc_nota')." ".$errors->first('tcc_freq'):null }}</div>
    </div>
    @if(($dadosMonografia->status == "AGUARDANDO NOTA DO TCC") && strpos($userLogado,"Orientador") !== false )
        @if ($dadosMonografia->aluno_autoriza_publicar)
        <div id="publicacao" class="campo">
            <label for="publicar">RECOMENDA A INCLUSÃO DESTA TESE NO BANCO DA BIBLIOTECA DIGITAL DE TRABALHOS ACADÊMICOS (BDTA) DA USP</label>
                Sim -> <input type="radio" id="publicar" name="publicar" value="1" 
                    @if (old('publicar') == 1) checked @endif >&nbsp;&nbsp;&nbsp;&nbsp;
                Não -> <input type="radio" id="publicar" name="publicar" value="0" @if (old('publicar') == 1) checked @endif >
                <div class="erro">{{  $errors->has('publicar') ? $errors->first('publicar'):null }}</div>
        </div>
        @else
        <p>O aluno, na qualidade de titular dos direitos morais e patrimoniais de autor que recaem sobre o Trabalho de Conclusão de 
           Curso defendido, não autorizou a publicação do TCC em ambiente digital Institucional [Biblioteca Digital de trabalhos Acadêmicos, pela 
           Resolução CoCEx-COG nº 7497, de 09/04/2018].
         </p>
        @endif
    @endif
    @if(($dadosMonografia->status == "AGUARDANDO NOTA DO TCC" || $dadosMonografia->status == "AGUARDANDO NOTA DO PROJETO") && strpos($userLogado,"Orientador") !== false )
    <input type="submit" name="enviar" id="buttonNota" value="{{ !empty($dadosMonografia->alunos[0]->projeto_nota)?'Atualizar Nota':'Registrar a Nota' }}">
    @endif
</form>
<div id="process" title="Aguarde, pode levar algum tempo..."></div>
</div>

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
        
      $("#buttonNota").click( function() {
        $('body').css("background-color","rgba(0,0,0,0.1)");
        $(this).css("display","none");
        aguarde();

        $("form_nota_{{ $numUSPAluno }}").submit();
      });
        
       
    });
</script>