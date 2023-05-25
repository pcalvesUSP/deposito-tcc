$(document).ready(function(){
    
    $("#trabDupla").hide();

    if ($("#dupla").is(':checked')) {
        $("#trabDupla").show();
    } 

    $("#dupla").click (function() {
        result = false;

        if ($(this).is(':checked')) {
            $("#trabDupla").show();
        } else {
            $("#trabDupla").hide();
            $("#edupla").html("");
        }
    });

    $("#pessoaDupla").change(function () {
      if ($("#dupla").is(':checked') && $(this).val().length <= 0) {
	  	  $("#edupla").html("Informe o membro do grupo de trabalho");
	  } else {
          $("#edupla").html("");
      }
    });

    $("#addOrientador").click(function () {
        
      i = parseInt($(this).attr("ind"));
      nome = "orientador_secundario_id_1";
      i = i+1;
      $(this).attr("ind", i.toString());

      strHtml = $("#orientadorSecundario").html();
      strHtml = strHtml.replace(nome,"orientador_secundario_id_"+i.toString())+"<br/>";

      strNovo = $("#novosOrientadores").html()+strHtml;
    
      $("#novosOrientadores").html(strNovo);
      return false;

    });

    //$('#unitermo1').select2();

    $('#cadastroUnitermo1').click(function() {
        if ($(this).is(':checked')) {
            $("#txtUnitermo1").show();
            $('#unitermo1').prop( "disabled", true );
        } else {
            $("#txtUnitermo1").hide();
            $("input[name=txtUnitermo1]").val("");
            $('#unitermo1').prop( "disabled", false );
        }
    });

    $('#cadastroUnitermo2').click(function() {
        if ($(this).is(':checked')) {
            $("#txtUnitermo2").show();
            $('#unitermo2').prop( "disabled", true );
        } else {
            $("#txtUnitermo2").hide();
            $("input[name=txtUnitermo2]").val("");
            $('#unitermo2').prop( "disabled", false );
        }
    });

    $('#cadastroUnitermo3').click(function() {
        if ($(this).is(':checked')) {
            $("#txtUnitermo3").show();
            $('#unitermo3').prop( "disabled", true );
        } else {
            $("#txtUnitermo3").hide();
            $("input[name=txtUnitermo3]").val("");
            $('#unitermo3').prop( "disabled", false );
        }
    });

    $('#cadastroUnitermo4').click(function() {
        if ($(this).is(':checked')) {
            $("#txtUnitermo4").show();
            $('#unitermo4').prop( "disabled", true );
        } else {
            $("#txtUnitermo4").hide();
            $("input[name=txtUnitermo4]").val("");
            $('#unitermo4').prop( "disabled", false );
        }
    });

    $('#cadastroUnitermo5').click(function() {
        if ($(this).is(':checked')) {
            $("#txtUnitermo5").show();
            $('#unitermo4').prop( "disabled", true );
        } else {
            $("#txtUnitermo5").hide();
            $("input[name=txtUnitermo5]").val("");
            $('#unitermo5').prop( "disabled", false );
        }
    });

    var $disabledResults = $(".js-example-disabled-results");
    $disabledResults.select2();

    setTimeout(function() {
              $('#mensagem').fadeOut('fast');
            }, 3000);

});