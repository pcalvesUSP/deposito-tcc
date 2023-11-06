<div style="border: solid 1px black; padding: 5px 5px 5px 5px;">
@if ($dadosAvaliacoes->first()->status == "AGUARDANDO" )
<h3 style="color:red;"> Aguardando avaliação da Comissão</h1>
@elseif ($dadosAvaliacoes->first()->status == "CORRIGIDO")
<h3 style="color:red;"> Aluno já realizou a correção. Aguardando avaliação da Comissão desde {{ $dadosAvaliacoes->first()->dataAvaliacao->format('d/m/Y H:i:s') }}</h1>
@elseif ($dadosAvaliacoes->first()->status == "DEVOLVIDO")
<h3 style="color:red;"> Aguardando a correção do aluno desde {{ $dadosAvaliacoes->first()->dataAvaliacao->format('d/m/Y H:i:s') }} </h1>
@else
<h3 style="color:red;"> O STATUS do Projeto de TCC é {{ $dadosAvaliacoes->first()->status }} </h1>
@endif
@if (strpos($userLogado,'Graduacao') !== false)
<p>Parecerista: {{ $dadosAvaliacoes->_parecerista }}</p>
@endif
<p>Data @if ($dadosAvaliacoes->first()->status == "APROVADO") da Aprovação @else do Parecer: @endif {{ $dadosAvaliacoes->first()->dataAvaliacao->format('d/m/Y H:i:s') }}</p>
@if (!empty($dadosAvaliacoes->first()->parecer))
<p>
 @if ($dadosAvaliacoes->first()->status == "REPROVADO") Motivo da Reprovação: @else Parecer: @endif{{ empty($dadosAvaliacoes->first()->parecer)?'---':$dadosAvaliacoes->first()->parecer }}</p>
@endif

@if ($userLogado == "Aluno" && $dadosAvaliacoes->first()->status == "DEVOLVIDO")
    <p style="color:red;">Verifique e corrija abaixo!</p>
@elseif ($userLogado == "Avaliador" && $dadosAvaliacoes->first()->status == "DEVOLVIDO")
    <p style="color:red;">Aguarde o aluno corrigir a monografia</p>
@endif
</div>
<hr/>