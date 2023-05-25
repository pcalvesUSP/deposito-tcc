@extends('layouts.app')

@section('content')

<div class="erro" id="mensagem"> {{ $mensagem }}</div>
<p style="align:right"><a href="{{ route('comissao.create') }}">Cadastrar Novo</a></p>
<h1>Comissão</h1>
@if ($listComissao->isEmpty()) 
<p> Não há membros da Comissão cadastrados </p>
@else
<table class="tableData">
    <thead>
        <tr>
            <th style="width:16%;" class="tableData">N&uacute;mero USP</th>
            <th style="width:16%;" class="tableData">Nome</th>
            <th style="width:16%;" class="tableData">Email</th>
            <th style="width:16%;" class="tableData">Papel</th>
            <th style="width:16%;" class="tableData">Assinatura</th>
            <th style="width:16%;" class="tableData" colspan="2">A&ccedil;&otilde;es</th>
        </tr>
    </thead>
    @foreach ($listComissao as $comissao)
    <tr>
        <td style="width:16%;" class="tableData"> {{ $comissao->codpes }} </td>
        <td style="width:16%;" class="tableData"> {{ $comissao->nome }} </td>
        <td style="width:16%;" class="tableData"> {{ $comissao->email }} </td>
        <td style="width:16%;" class="tableData"> {{ $comissao->papel }} </td>
        <td style="width:16%;" class="tableData"> @if (!empty($comissao->assinatura)) <a href="upload/assinatura/{{ $comissao->assinatura }}">Baixar assinatura</a> @else --- @endif</td>
        <td style="width:8%;" class="tableData"><a href="{{ route('comissao.edit', ['comissao'=>$comissao->id]) }}">Editar</a></td>
        <td style="width:8%;" class="tableData">
        <form id="deleteOrientador_{{ $comissao->id }}" action={{ route('comissao.destroy', ['comissao'=>$comissao->id])}} method="post"> 
        @csrf
        @method('DELETE') 
        <input type="submit" value="Excluir" style="background-color:transparent;" onmouseover="return $(this).css({'color':'blue','text-decoration':'underline'})" onmouseout="return $(this).css({'color':'black','text-decoration':'none'})">
        </form></td>
    </tr>
    @endforeach
</table>

@endif

<script src="js/buscasRegistro.js"></script>
<script>
    setTimeout(function() {
                $('#mensagem').fadeOut('fast');
              }, 3000);
</script>

@endsection