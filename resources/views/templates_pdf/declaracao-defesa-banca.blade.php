<!DOCTYPE html>
<html>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        .page-break {
            page-break-after: always;
        }
    </style>
    <body>
        <div class="col-md-12" style="text-align: center;">
            <img src="{{ asset('/vendor/laravel-usp-theme/fcf/images/LOGOFcf_nome.png') }}" alt="Logo da Faculdade de Ciências Farmacêuticas da USP" />
        </div>
        <h1 style="text-align: center;">DECLARAÇÃO</h1>
        <p></p>
        <p></p>
        <p style="text-align: justify; margin: 0px 50px 0x 50px;">Declaro que o(a) Prof(a). Dr(a). {{ $nome_membro }} {{ $papel_membro=='PRESIDENTE'?'atuou como orientador(a) e':null }} participou da Comissão Julgadora do Trabalho de Conclusão do Curso de 
        Graduação em Farmácia-Bioquímica do(a) aluno(a) {{ $nome_aluno }}, intitulado “{{ $titulo_trabalho }}”, da Faculdade de Ciências Farmacêuticas da 
        Universidade de São Paulo, em sessão virtual no dia {{ $data_defesa }}, às {{ $hora_defesa }} horas.

        </p>
    </body>
</html>