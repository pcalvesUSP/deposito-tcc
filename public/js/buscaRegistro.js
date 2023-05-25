$( document ).ready(function(){
	
	$(function(campo) {
	    
	    if (campo.val().length > 0) {
			$.ajax({
			  url: "/orientador/ajaxBuscaDadosOrientador/"+$(this).val(),
			  method: "GET",
			  success: function( data ) {
				if (data == "Could not connect to host") {
					alert("Sistema em Manutenção, tente novamente mais tarde");
				} else {
					var obj = jQuery.parseJSON( data );

					if (obj.nome.length > 0) {
						$("#nomeOrientador").val(obj.nome);
						$("#emailOrientador").val(obj.email);
						$("#externo").attr("disabled",true);
						$("#salvar").trigger("focus");
					} else {
						alert("Erro ao buscar dados de Orientador, verifique o número digitado.");
						$("#nomeOrientador").val("");
						$("#emailOrientador").val("");
						$("#externo").attr("disabled",false);
						$(this).trigger("focus");
					}
				}
			   },
			   error: function ( data ) {
					//alert( "Erro: "+data.status+"-"+data.error);
					alert("Erro ao buscar dados de Orientador, verifique o número digitado.");
					$("#nomeOrientador").val("");
					$("#emailOrientador").val("");
					$("#externo").attr("disabled",false);
					$(this).trigger("focus");
				}
			});
		} else {
		    //alert("O N.o USP do Orientador deve ser informado");
		    $("#nomeOrientador").val("");
		    $("#emailOrientador").val("");
		    $("#externo").attr("disabled",false);
		}
	});
	
	$("#emailOrientador").blur(function() {
	  if ($(this).val().length > 0) {
	  	if (!valEmail($(this).val())) {
	  		alert("E-mail "+$(this).val()+" inválido.").
	  		$(this).val("");
	  	}
	  } 
	});
	
	function valEmail(email) {
	    var sEmail	= email;
		// filtros
		var emailFilter=/^.+@.+\..{2,}$/;
		var illegalChars= /[\(\)\<\>\,\;\:\\\/\"\[\]]/
		  
	    if (!(emailFilter.test(sEmail))||sEmail.match(illegalChars)) {
	        return false;
	    }
	    
	    return true;
	}
	
	
});