$( document ).ready(function(){
    
	$("#nuspComissao").blur(function() {	    
	    if ($(this).val().length > 0) {
            $.ajax({
			  url: "graduacao/comissao/ajaxBuscaDadosComissao/"+$(this).val(),
			  method: "GET",
			  success: function( data ) {
				if (data == "Could not connect to host") {
					alert("Sistema em Manutenção, tente novamente mais tarde");
				} else {
                    if (data.length == 0) {
						alert("Erro ao buscar dados de Membro da Comissão, verifique o número digitado.");
						$("#nomeComissao").val("");
						$("#emailComissao").val("");
						$("#papeComissao").find("option[text=Selecione]").attr("selected", true);
						$(this).trigger("focus");
                        return false;                        
                    }
                    var obj = data;

					if (obj.nome.length > 0) {
						$("#nomeComissao").val(obj.nome);
						$("#emailComissao").val(obj.email);
                        if (obj.papel.length > 0)
                            $("#papelComissao").find("option[value=" + obj.papel + "]").attr("selected", true);
						$("#papelComissao").trigger("focus");
					} else {
						alert("Erro ao buscar dados de Membro da Comissão, verifique o número digitado.");
						$("#nomeComissao").val("");
						$("#emailComissao").val("");
						$("#papeComissao").find("option[text=Selecione]").attr("selected", true);
						$(this).trigger("focus");
					}
				}
			   },
			   error: function ( data ) {
					//alert( "Erro: "+data.status+"-"+data.error);
                    alert("Erro ao buscar dados de Membro da Comissão, verifique o número digitado.");
                    $("#nomeComissao").val("");
                    $("#emailComissao").val("");
                    $("#papeComissao").find("option[text=Selecione]").attr("selected", true);
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