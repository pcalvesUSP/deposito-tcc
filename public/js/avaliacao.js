$( document ).ready(function(){

    $("#parecer").hide(); 
    $("#publicacao").hide();
    
    var select = document.getElementById('acao');
	var value = select.options[select.selectedIndex].value;

    if (value == "DEVOLVIDO" || value == "REPROVADO") {
        $("#parecer").show();
        $("#publicacao").hide();
    } else {
        $("#publicacao").show();
        $("#parecer").hide();
    }
	
    $("#acao").click(function() {
        if ($(this).val() == "DEVOLVIDO" || $(this).val() == "REPROVADO" ) {
            $("#parecer").show();
            $("#publicacao").hide();
        }
        if ($(this).val() == "APROVADO") {
            $("#publicacao").show();
            $("#parecer").hide();
        }
    });
	
	
});