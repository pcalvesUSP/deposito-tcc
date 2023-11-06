@extends('layouts.app')

@section('content')

<div class="erro" id="mensagem"> {{ $mensagem }}</div>

<div style="width:70%;margin:0 250px 0 250px">
<p style="align:right"><a href={{ route('unitermos.create') }}>Cadastrar Novo</a></p>
<h1>Unitermos</h1>
<table class="tableData" >
    <thead>
        <form id="filtrarUnitermos" action="{{ route('unitermos.busca') }}" method="post">
            @csrf
            <tr>
                <th colspan="7" style="text-align:right;background:#c6c2eb;">Filtrar: <input type="text" id="filtro" name="filtro" size="15" style="font-weight:bold;width:150px;border: solid 1px blue;"/></th>
            </tr>
            </form>
        <tr>
            <th style="width:50%;" class="tableData">Unitermo</th>
            <th style="width:50%;" class="tableData" colspan="2">A&ccedil;&otilde;es</th>
        </tr>
    </thead>
    @if (!empty($filtro))
    <tr><td class="tableData" colspan="7">Busca por termo: {{ $filtro }}<br/><a href="{{ route('unitermos.index') }}" >Resetar a busca</a></td></tr>
    @endif
    @if (!$listUnitermos->isEmpty()) 
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
    @endif
</table>
{{ $listUnitermos->links() }}
</div>

<script>
    setTimeout(function() {
                $('#mensagem').fadeOut('fast');
              }, 5000);

    $( document ).ready(function(){

        $("#filtro").keypress(function( event ) {
            if (event.which == 13) {
                if ($(this).val().length >= 3) {
                    document.getElementById("filtrarUnitermos").submit();
                } else {
                    alert("O termo a ser localizado deve conter 3 ou mais caracteres.");
                    return false;
                }
            }
        });
        
    });
</script>


@endsection