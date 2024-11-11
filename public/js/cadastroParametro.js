$( document ).ready(function(){

    var dataAtual = new Date();
    var anoAtual = dataAtual.getFullYear();
    var mesAtual = dataAtual.getMonth();
    var semestre = 0;

    if (mesAtual <= 6) {
        semestre = 1;
    } else {
        semestre = 2;
    }

    $('#semestreAno').on ("change",function () {   
        var valor = $(this).val();
        $.ajax({
            url: "/graduacao/buscaDadosParametros/"+$(this).val().substring(0,4)+"/"+$(this).val().substring(5,6),
            method: "GET",
            success: function( data ) {
                if (data == "Could not connect to host") {
                    alert("Sistema em Manutenção, tente novamente mais tarde");
                } else {
                    if (data.dataAberturaDiscente.length > 0) {
                        $("#id_parametro").val(data.id);
                        $('#metodo').html('<input type="hidden" name="_method" value="PUT">');
                        $('#formParametro').attr('action', 'graduacao/administracao/'+$("#id_parametro").val());
                        $("#dataInicioAlunos").val(data.dataAberturaDiscente.substring(8,10)+"/"+data.dataAberturaDiscente.substring(5,7)+"/"+data.dataAberturaDiscente.substring(0,4));
                        $("#dataFinalAlunos").val(data.dataFechamentoDiscente.substring(8,10)+"/"+data.dataFechamentoDiscente.substring(5,7)+"/"+data.dataFechamentoDiscente.substring(0,4));
                        $("#dataInicioDocentes").val(data.dataAberturaDocente.substring(8,10)+"/"+data.dataAberturaDocente.substring(5,7)+"/"+data.dataAberturaDocente.substring(0,4));
                        $("#dataFinalDocentes").val(data.dataFechamentoDocente.substring(8,10)+"/"+data.dataFechamentoDocente.substring(5,7)+"/"+data.dataFechamentoDocente.substring(0,4));
                        $("#dataAberturaAvaliacao").val(data.dataAberturaAvaliacao.substring(8,10)+"/"+data.dataAberturaAvaliacao.substring(5,7)+"/"+data.dataAberturaAvaliacao.substring(0,4));
                        $("#dataFechamentoAvaliacao").val(data.dataFechamentoAvaliacao.substring(8,10)+"/"+data.dataFechamentoAvaliacao.substring(5,7)+"/"+data.dataFechamentoAvaliacao.substring(0,4));
                        $("#dataAberturaUploadTCC").val(data.dataAberturaUploadTCC.substring(8,10)+"/"+data.dataAberturaUploadTCC.substring(5,7)+"/"+data.dataAberturaUploadTCC.substring(0,4));
                        $("#dataFechamentoUploadTCC").val(data.dataFechamentoUploadTCC.substring(8,10)+"/"+data.dataFechamentoUploadTCC.substring(5,7)+"/"+data.dataFechamentoUploadTCC.substring(0,4));

                        /*if (valor.substring(0,4) != anoAtual && valor.substring(5,6) != semestre) {
                            $("#dataInicioAlunos").attr({readonly:true, class:'inputReadonly'});
                            $("#dataFinalAlunos").attr({readonly:true, class:'inputReadonly'});
                            $("#dataInicioDocentes").attr({readonly:true, class:'inputReadonly'});
                            $("#dataFinalDocentes").attr({readonly:true, class:'inputReadonly'});
                            $("#dataAberturaAvaliacao").attr({readonly:true, class:'inputReadonly'});
                            $("#dataFechamentoAvaliacao").attr({readonly:true, class:'inputReadonly'});
                            $("#dataAberturaUploadTCC").attr({readonly:true, class:'inputReadonly'});
                            $("#dataFechamentoUploadTCC").attr({readonly:true, class:'inputReadonly'});
                        } else {
                            $("#dataInicioAlunos").attr({readonly:false, class:''});
                            $("#dataFinalAlunos").attr({readonly:false, class:''});
                            $("#dataInicioDocentes").attr({readonly:false, class:''});
                            $("#dataFinalDocentes").attr({readonly:false, class:''});
                            $("#dataAberturaAvaliacao").attr({readonly:false, class:''});
                            $("#dataFechamentoAvaliacao").attr({readonly:false, class:''});
                            $("#dataAberturaUploadTCC").attr({readonly:false, class:''});
                            $("#dataFechamentoUploadTCC").attr({readonly:false, class:''});
                        }*/
                    } else {
                        $("#dataInicioAlunos").val("");
                        $("#dataFinalAlunos").val("");
                        $("#dataInicioDocentes").val("");
                        $("#dataFinalDocentes").val("");
                        $("#dataAberturaAvaliacao").val("");
                        $("#dataFechamentoAvaliacao").val("");
                        $("#dataAberturaUploadTCC").val("");
                        $("#dataFechamentoUploadTCC").val("");
                        $(this).trigger("focus");
                    }
                }
            },
            error: function ( data ) {
                    alert( "Erro: "+data.status+"-"+data.error);
                    $("#dataInicioAlunos").val("");
                    $("#dataFinalAlunos").val("");
                    $("#dataInicioDocentes").val("");
                    $("#dataFinalDocentes").val("");
                    $("#dataAberturaAvaliacao").val("");
                    $("#dataFechamentoAvaliacao").val("");
                    $("#dataAberturaUploadTCC").val("");
                    $("#dataFechamentoUploadTCC").val("");
                    $(this).trigger("focus");
            }
        });     
    });

}); 