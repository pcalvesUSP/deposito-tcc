@extends('layouts.app')

@section('content')

<div class="erro" id="mensagem"> {{ $mensagem }}</div>
<div style="width:70%;margin:0 250px 0 250px">
<p style="align:right"><a href={{ route('area_tematica.create') }}>Cadastrar Novo</a></p>
<h1>&Aacute;reas Tem&aacute;ticas</h1>
@if ($listAreaTematica->isEmpty()) 
<p> Não há &aacute;reas Tem&aacute;ticas cadastradas </p>
@else
<table class="tableData" >
    <tr>
        <th style="width:50%;" class="tableData">&Aacute;rea Tem&aacute;tica</th>
        <th style="width:50%;" class="tableData" colspan="2">A&ccedil;&otilde;es</th>
    </tr>
    @foreach ($listAreaTematica as $area_tematica)
    <tr>
        <td style="width:33%;" class="tableData">{{ $area_tematica->descricao }}</td>
        <td style="width:25%;" class="tableData"><a href="{{ route('area_tematica.edit', ['area_tematica'=>$area_tematica->id]) }}">Editar</a></td>
        <td style="width:25%;" class="tableData"> 
        <form id="deleteAreaTematica_{{ $area_tematica->id }}" action={{ route('area_tematica.destroy', ['area_tematica'=>$area_tematica->id])}} method="post"> 
        @csrf
        @method('DELETE') 
        <input type="submit" value="Excluir" style="background-color:transparent;" onmouseover="return $(this).css({'color':'blue','text-decoration':'underline'})" onmouseout="return $(this).css({'color':'black','text-decoration':'none'})">
        </form>
        </td>
    </tr>
    @endforeach
</table>
</div>
@endif
<script>
    setTimeout(function() {
                $('#mensagem').fadeOut('fast');
              }, 3000);
</script>


@endsection