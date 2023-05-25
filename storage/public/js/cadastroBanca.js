$( document ).ready(function(){

    $("#filtro").keyup(function() {
        if ($(this).val().length > 3) {
            document.getElementById("filtrarBanca").submit();
        }
    });
    
	$("#numUSPBanca").blur(function() {	    
	    if ($(this).val().length > 3) {
            $.ajax({
			  url: "graduacao/comissao/ajaxBuscaDadosComissao/"+$(this).val(),
			  method: "GET",
			  success: function( data ) {
				if (data == "Could not connect to host") {
					alert("Sistema em Manutenção, tente novamente mais tarde");
				} else {
                    if (data.length == 0) {
						alert("Erro ao buscar dados de Membro da Banca, verifique o número digitado. Caso seja um membro externo, deixe este campos vazio.");
						$('#numUSPBanca').val("");
						$("#nomeBanca").val("");
						$("#emailBanca").val("");
						$('#numUSPBanca').trigger("focus");
                        return false;                        
                    }
                    var obj = data;

					if (obj.nome.length > 0) {
						$("#nomeBanca").val(obj.nome);
						$("#emailBanca").val(obj.email);
						$("#buttonSubmit").trigger("focus");
					} else {
						alert("Erro ao buscar dados de Membro da Banca, verifique o número digitado. Caso seja um membro externo, deixe este campos vazio.");
						$('#numUSPBanca').val("");
						$("#nomeBanca").val("");
						$("#emailBanca").val("");
						$('#numUSPBanca').trigger("focus");
					}
				}
			   },
			   error: function ( data ) {
					//alert( "Erro: "+data.status+"-"+data.error);
                    alert("Erro ao buscar dados de Membro da Banca, verifique o número digitado. Caso seja um membro externo, deixe este campos vazio.");
					$('#numUSPBanca').val("");
					$("#nomeBanca").val("");
                    $("#emailBanca").val("");
                    $('#numUSPBanca').trigger("focus");
            }
			});
		} else {
		    //alert("O N.o USP do Orientador deve ser informado");
		    $("#nomeBanca").val("");
            $("#emailBanca").val("");
		}
	});
	
	$("#emailBanca").blur(function() {
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