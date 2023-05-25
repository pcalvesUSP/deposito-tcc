@if ($dadosAvaliacoes->first()->status != "DEVOLVIDO")
<h3 style="color:red;"> O STATUS da Monografia é {{ $dadosAvaliacoes->first()->status }} </h1>
@else
<h3 style="color:red;"> Avaliação Pendente de Correção </h1>
@endif
<p>Orientador: {{ $dadosAvaliacoes->_orientador }}</p>
<p>Data do Parecer: {{ $dadosAvaliacoes->first()->dataAvaliacao->format('d/m/Y H:i:s') }}</p>
<p>@if ($dadosAvaliacoes->first()->status == "REPROVADO") Motivo da Reprovação: @else Parecer: @endif{{ empty($dadosAvaliacoes->first()->parecer)?'---':$dadosAvaliacoes->first()->parecer }}</p>

@if ($userLogado == "Aluno" && $dadosAvaliacoes->first()->status == "DEVOLVIDO")
    <p style="color:red;">Verifique e corrija abaixo!</p>
@elseif ($userLogado == "Orientador" && $dadosAvaliacoes->first()->status == "DEVOLVIDO")
    <p style="color:red;">Aguarde o aluno corrigir a monografia</p>
@endif

<hr/>