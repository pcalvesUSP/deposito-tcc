@extends('layouts.app')

@section('content')

<div class="erro" id="mensagem"> {{ $mensagem }}</div>
<div style="width:70%;margin:0 250px 0 250px">
<p style="align:right"><a href={{ route('unitermos.create') }}>Cadastrar Novo</a></p>
<h1>Unitermos</h1>
@if ($listUnitermos->isEmpty()) 
<p> Não há unitermos cadastrados </p>
@else
<table class="tableData" >
    <tr>
        <th style="width:50%;" class="tableData">Unitermo</th>
        <th style="width:50%;" class="tableData" colspan="2">A&ccedil;&otilde;es</th>
    </tr>
    @foreach ($listUnitermos as $unitermo)
    <tr>
        <td style="width:50%;" class="tableData">{{ $unitermo->unitermo }}</td>
        <td style="width:25%;" class="tableData"><a href="{{ route('unitermos.edit',['unitermo'=>$unitermo->id])}}">Editar</td> 
        <td style="width:25%;" class="tableData">
        <form id="deleteUnitermo_{{ $unitermo->id }}" action={{ route('unitermos.destroy', ['unitermo'=>$unitermo->id])}} method="post"> 
        @csrf
        @method('DELETE') 
        <input type="submit" value="Excluir" style="background-color:transparent;" onmouseover="return $(this).css({'color':'blue','text-decoration':'underline'})" onmouseout="return $(this).css({'color':'black','text-decoration':'none'})">
        </form></td>
    </tr>
    @endforeach
</table>
</div>
@endif
<script>
    setTimeout(function() {
                $('#mensagem').fadeOut('fast');
              }, 5000);
</script>


@endsection