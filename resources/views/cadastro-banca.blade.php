@extends('layouts.app')

@section('content')

<p style="align:right"><a href="{{ route('banca.create') }}">Cadastrar Novo</a></p>
<div class="erro" id="mensagem"> {{ empty($mensagem)?null:$mensagem }}</div>
<h1>Cadastro de Banca</h1>

<table class="tableData">
    <thead>
        <form id="filtrarBanca" action="{{ route('banca.filtro') }}" method="post">
        @csrf
        <tr>
            <th colspan="7" style="text-align:right;background:#c6c2eb;">
            Filtrar: <input type="text" id="filtro" name="filtro" value="{{ empty($filtro)?old('filtro'):$filtro }}" size="15" style="font-weight:bold;width:150px;border: solid 1px blue;"/>
            </th>
        </tr>
        </form>
        <tr>
            <th style="width:10%;" class="tableData">Número USP</th>
            <th style="width:30%;" class="tableData">Nome</th>
            <th style="width:30%;" class="tableData">Email</th>
            <th style="width:10%;" class="tableData">Ano</th>
            <th style="width:20%;" class="tableData" colspan="2">A&ccedil;&otilde;es</th>
        </tr>
        
    </thead>
    @if (!empty($filtro))
        <tr><td class="tableData" colspan="7">Busca por termo: {{ $filtro }}<br/><a href="{{ route('banca.index') }}" >Resetar a busca</a></td></tr>
    @endif

    @foreach ($listBanca as $banca)
    @if (!empty($banca->id))
    <tr>
        <td style="width:10%;" class="tableData"> {{ !empty($banca->codpes)?$banca->codpes:'EXTERNO' }} </td>
        <td style="width:30%;" class="tableData"> {{ $banca->nome }} </td>
        <td style="width:30%;" class="tableData"> {{ $banca->email }} </td>
        <td style="width:10%;" class="tableData"> {{ $banca->ano }} </td>
        <td style="width:10%;" class="tableData"><a href="{{ route('declaracao.moderador',['comissao'=>$banca->id]) }}" target="_blank">Declaração<br/>moderador</a></td>
        <!--td style="width:6.66%;" class="tableData"><a href="{{ route('banca.edit', ['banca'=>$banca->id]) }}">Editar</a></td-->
        <td style="width:10%;" class="tableData">
        <form id="deletebanca_{{ $banca->id }}" action={{ route('banca.destroy', ['banca'=>$banca->id])}} method="post"> 
        @csrf
        @method('DELETE') 
        <input type="submit" value="Excluir" style="background-color:transparent;" onmouseover="return $(this).css({'color':'blue','text-decoration':'underline'})" onmouseout="return $(this).css({'color':'black','text-decoration':'none'})">
        </form></td>
    </tr>
    @endif
    @endforeach
    
</table>
{{ $listBanca->links(); }}

<script src="js/cadastroBanca.js"></script>
<script>
    setTimeout(function() {
                $('#mensagem').fadeOut('fast');
              }, 3000);
</script>

@endsection