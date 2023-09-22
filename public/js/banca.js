$( document ).ready(function(){

    $("#data1").mask("99/99/9999");
    $("#data2").mask("99/99/9999");
    $("#data3").mask("99/99/9999");
    $("#horario1").mask("99:99");
    $("#horario2").mask("99:99");
    $("#horario3").mask("99:99");

    if ($("#telefone1").val() >= 14) {
        $("#telefone1").mask('(00)00000-00009');
    } else {
        $("#telefone1").mask("(00)0000-00009");
    }
    if ($("#telefone2").val() >= 14) {
        $("#telefone2").mask('(00)00000-00009');
    } else {
        $("#telefone2").mask("(00)0000-00009");
    }
    if ($("#telefone3").val() >= 14) {
        $("#telefone3").mask('(00)00000-00009');
    } else {
        $("#telefone3").mask("(00)0000-00009");
    }
    if ($("#telefoneSuplente").val() >= 14) {
        $("#telefoneSuplente").mask('(00)00000-00009');
    } else {
        $("#telefoneSuplente").mask("(00)0000-00009");
    }
    
    $('#telefone1').blur(function(event) {
        if($(this).val().length >= 14){ // Celular com 9 dígitos + 2 dígitos DDD e 4 da máscara
           $(this).mask('(00)00000-00009');
        } else {
           $(this).mask('(00)0000-0009');
        }
    });
    $('#telefone2').blur(function(event) {
        if($(this).val().length >= 14){ // Celular com 9 dígitos + 2 dígitos DDD e 4 da máscara
           $(this).mask('(00)00000-00009');
        } else {
           $(this).mask('(00)0000-0009');
        }
     });

     $('#telefone3').blur(function(event) {
        if($(this).val().length >= 14){ // Celular com 9 dígitos + 2 dígitos DDD e 4 da máscara
           $('#telefone3').mask('(00)00000-00009');
        } else {
           $('#telefone3').mask('(00)0000-0009');
        }
     });

     $('#telefoneSuplente').blur(function(event) {
        if($(this).val().length >= 14){ // Celular com 9 dígitos + 2 dígitos DDD e 4 da máscara
           $('#telefoneSuplente').mask('(00)00000-00009');
        } else {
           $('#telefoneSuplente').mask('(00)0000-0009');
        }
     });

    $("#emailBanca2").blur(function() {
        var vemail = $(this).val();
        if ($(this).val().length > 0) {
            if (!valEmail($(this).val())) {
                $(this).focus();
                $(this).val("");
                alert("E-mail "+vemail+" inválido.");
            }
        } 
      });

      $("#emailBanca3").blur(function() {
        if ($(this).val().length > 0) {
            if (!valEmail($(this).val())) {
                alert("E-mail "+$(this).val()+" inválido.").
                $(this).val("");
            }
        } 
      });

      $("#emailSuplente").blur(function() {
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

      $("#nusp2").blur(function() {
        if ($(this).val().length > 0) {
            $.ajax({
               url: "graduacao/comissao/ajaxBuscaDadosComissao/"+$(this).val(),
               method: "GET",
               success: function( data ) {
                  if (data == "Could not connect to host") {
                      alert("Sistema em Manutenção, tente novamente mais tarde");
                  } else {
                      if (data.nome.length > 0) {
                          $("#membroBanca2").val(data.nome);
                          $("#emailBanca2").val(data.email);
                          $("#telefone2").val(data.telefone);
                          $("#instituicao2").val(data.instituicao);
                      } 
                  }
                 },
                 error: function ( data ) {
                      //alert( "Erro: "+data.status+"-"+data.error);
                      alert("Erro ao buscar dados de Membro da Comissão, verifique o número digitado.");
                 }
            });
        }
      });

      $("#nusp3").blur(function() {
        if ($(this).val().length > 0) {
            $.ajax({
               url: "graduacao/comissao/ajaxBuscaDadosComissao/"+$(this).val(),
               method: "GET",
               success: function( data ) {
                  if (data == "Could not connect to host") {
                      alert("Sistema em Manutenção, tente novamente mais tarde");
                  } else {
                      if (data.nome.length > 0) {
                          $("#membroBanca3").val(data.nome);
                          $("#emailBanca3").val(data.email);
                          $("#telefone3").val(data.telefone);
                          $("#instituicao3").val(data.instituicao);
                      } 
                  }
                 },
                 error: function ( data ) {
                      //alert( "Erro: "+data.status+"-"+data.error);
                      alert("Erro ao buscar dados de Membro da Comissão, verifique o número digitado.");
                 }
            });
        }
      });

      $("#nuspSuplente").blur(function() {
        if ($(this).val().length > 0) {
            $.ajax({
               url: "graduacao/comissao/ajaxBuscaDadosComissao/"+$(this).val(),
               method: "GET",
               success: function( data ) {
                  if (data == "Could not connect to host") {
                      alert("Sistema em Manutenção, tente novamente mais tarde");
                  } else {
                      if (data.nome.length > 0) {
                          $("#suplente").val(data.nome);
                          $("#emailSuplente").val(data.email);
                          $("#telefoneSuplente").val(data.telefone);
                          $("#instituicaosuplente").val(data.instituicao);
                      } 
                  }
                 },
                 error: function ( data ) {
                      //alert( "Erro: "+data.status+"-"+data.error);
                      alert("Erro ao buscar dados de Membro da Comissão, verifique o número digitado.");
                 }
            });
        }
      });
	
});