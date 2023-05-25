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

});