$( document ).ready(function(){

    $("#parecer").hide(); 
    $("#publicacao").hide();
    
    var select = document.getElementById('acao');
	var value = select.options[select.selectedIndex].value;

    if (value != 0) {
        if (value == "DEVOLVIDO" || value == "REPROVADO") {
            $("#parecer").show();
            $("#publicacao").hide();
        } 
    }
	
    $("#acao").click(function() {
        if ($(this).val() == "DEVOLVIDO" || $(this).val() == "REPROVADO" ) {
            $("#parecer").show();
            $("#publicacao").hide();
        }
        if ($(this).val() == "APROVADO") {
            $("#publicacao").hide();
            $("#parecer").hide();
        }
    });
	
	
});