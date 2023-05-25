@extends('layouts.app')

@section('content')

<div class="erro" id="mensagem"> {{ $mensagem }}</div>
<p style="align:right"><a href="orientador/novoCadastro">Cadastrar Novo</a></p>
<h1>Orientadores</h1>

<table class="tableData">
    <thead>
        <form id="filtrarOrientador" action="{{ route('busca.orientador') }}" method="post">
        @csrf
        <tr>
            <th colspan="7" style="text-align:right;background:#c6c2eb;">Filtrar: <input type="text" id="filtro" name="filtro" size="15" style="font-weight:bold;width:150px;border: solid 1px blue;"/></th>
        </tr>
        </form>
        <tr>
            <th style="width:16%;" class="tableData">N&uacute;mero USP</th>
            <th style="width:16%;" class="tableData">CPF</th>
            <th style="width:16%;" class="tableData">Nome</th>
            <th style="width:16%;" class="tableData">Email</th>
            <th style="width:16%;" class="tableData">Externo</th>
            <th style="width:16%;" class="tableData" colspan="2">A&ccedil;&otilde;es</th>
        </tr>
    </thead>
    @if (!empty($filtro))
    <tr><td class="tableData" colspan="7">Busca por termo: {{ $filtro }}<br/><a href="{{ route('orientador.index') }}" >Resetar a busca</a></td></tr>
    @endif
    @foreach ($listOrientadores as $orientador)
    @if (!empty($orientador->nome))
    <tr>
        <td style="width:16%;" class="tableData"> {{ $orientador->codpes }} </td>
        <td style="width:16%;" class="tableData"> {{ $orientador->CPF }} </td>
        <td style="width:16%;" class="tableData"> {{ $orientador->nome }} </td>
        <td style="width:16%;" class="tableData"> {{ $orientador->email }} </td>
        <td style="width:16%;" class="tableData"> {{ $orientador->externo==0? "N" : "S" }} </td>
        <td style="width:8%;" class="tableData"><a href="{{ route('orientador.edit', ['id'=>$orientador->id]) }}">Editar</a></td>
        <td style="width:8%;" class="tableData">
        <form id="deleteOrientador_{{ $orientador->id }}" action={{ route('orientador.destroy', ['id'=>$orientador->id])}} method="post"> 
        @csrf
        @method('DELETE') 
        <input type="submit" value="Excluir" style="background-color:transparent;" onmouseover="return $(this).css({'color':'blue','text-decoration':'underline'})" onmouseout="return $(this).css({'color':'black','text-decoration':'none'})">
        </form></td>
    </tr>
    @endif
    @endforeach
</table>
{{ $listOrientadores->links() }}

<script src="js/buscasRegistro.js"></script>
<script>
    setTimeout(function() {
                $('#mensagem').fadeOut('fast');
              }, 3000);

    $( document ).ready(function(){

        $("#filtro").keyup(function() {
            if ($(this).val().length > 3) {
                document.getElementById("filtrarOrientador").submit();
            }
        });
    });

</script>

@endsection