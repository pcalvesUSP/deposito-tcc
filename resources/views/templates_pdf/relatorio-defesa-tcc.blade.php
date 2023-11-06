<!DOCTYPE html>
<html>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        .page-break {
            page-break-after: always;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
        }
        table {
            margin: 0px 20px 0x 20px;
        }
        td {
            border: solid 1px black;
            text-align: justify;
        }
        p {
            text-align: justify; 
            margin: 0px 20px 0x 20px;
        }
    </style>
    <body>
        <div style="text-align: center; left:5%; right:5%; float: center;">
            <img width="650px" src="{{ asset('/vendor/laravel-usp-theme/fcf/images/cabec_relatorio_aluno.png') }}" alt="Logo da Faculdade de Ciências Farmacêuticas da USP" />
            <h1 style="border: solid 1px black; font-weight:bold;">RELATÓRIO DE DEFESA DE TRABALHO DE CONCLUSÃO DE CURSO</h1>
            
            <p>Relatório de defesa do Trabalho de Conclusão de Curso de Farmácia-Bioquímica da Faculdade de Ciências Farmacêuticas da Universidade de São Paulo.</p>
            <table cellspacing="0" cellspading="0">
                <tr>
                    <td style="width: 10%">Aluno:</td>
                    <td style="width: 60%">{{ $nome_aluno }}</td>
                    <td style="width: 30%" colspan="2">N.º USP: {{ $nusp_aluno }}</td>
                </tr>
                <tr>
                    <td style="width: 10%">Orientador:</td>
                    <td style="width: 60%">{{ $nome_orientador }}</td>
                    <td style="width: 30%" colspan="2">N.º USP: {{ $nusp_orientador }}</td>
                </tr>
                <tr>
                    <td style="width: 10%">Título do TCC:</td>
                    <td style="width: 90%" colspan="3">{{ $titulo_monografia }}</td>
                </tr>
                <tr>
                    <td style="width: 10%">Data da Defesa:</td>
                    <td style="width: 60%">{{ $data_defesa }}</td>
                    <td style="width: 15%">Hora Defesa:</td>
                    <td style="width: 15%">{{ $hora_defesa }}</td>
                </tr>
                <tr>
                    <td style="width: 10%">Local:</td>
                    <td style="width: 90%" colspan="3">{{ $local }}</td>
                </tr>
            </table>
            <p>A Comissão Julgadora, após apresentação do aluno e respectiva arguição, atribuiu as notas e proclamou o resultado:</p>
            <table cellspacing="0" cellspading="0">
                <tr>
                    <td><b>Média:</b> {{ $media }} </td>
                    <td><b>Frequencia:</b> {{ $frequencia }}</td>
                </tr>
                <tr>
                    <td colspan="2"><b>RESULTADO FINAL:</b> {{ $resultado }} </td>
                </tr>
            </table>
            <p>&nbsp;</p>
            <table cellspacing="0" cellspading="0">
                <tr>
                    <td><b>Membros da Comissão Julgadora que participaram da defesa</b></td>
                </tr>
                <tr>
                    <td> {{ $banca1 }} </td>
                </tr>
                <tr>
                    <td> {{ $banca2 }} </td>
                </tr>
                <tr>
                    <td> {{ $banca3 }} </td>
                </tr>
            </table>
            <p>&nbsp;</p>
            <p><b>RECOMENDA A INCLUSÃO DESTA TESE NO BANCO DA BIBLIOTECA DIGITAL DE TRABALHOS ACADÊMICOS (BDTA) DA USP → ({{ ($publica)?'X':'   ' }}) SIM  ({{ (!$publica)?'X':'   ' }}) NÃO</b></p>
            
        </div>
        <p></p>
        <p></p>
        
        </body>
</html>