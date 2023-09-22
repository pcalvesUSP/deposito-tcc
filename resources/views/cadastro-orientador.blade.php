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
            <th colspan="9" style="text-align:right;background:#c6c2eb;">Filtrar: <input type="text" id="filtro" name="filtro" size="15" style="font-weight:bold;width:150px;border: solid 1px blue;"/></th>
        </tr>
        </form>
        <tr>
            <th style="width:11.1%;" class="tableData">N&uacute;mero USP</th>
            <th style="width:11.1%;" class="tableData">CPF</th>
            <th style="width:11.1%;" class="tableData">Nome</th>
            <th style="width:11.1%;" class="tableData">Email</th>
            <th style="width:11.1%;" class="tableData">Externo</th>
            <th style="width:11.1%;" class="tableData">Aprovado?</th>
            <th style="width:11.1%;" class="tableData" colspan="3">A&ccedil;&otilde;es</th>
        </tr>
    </thead>
    @if (!empty($filtro))
    <tr><td class="tableData" colspan="9">Busca por termo: {{ $filtro }}<br/><a href="{{ route('orientador.index') }}" >Resetar a busca</a></td></tr>
    @endif
    @foreach ($listOrientadores as $orientador)
    @if (!empty($orientador->nome))
    <tr>
        <td style="width:16%;" class="tableData"> {{ $orientador->codpes }} </td>
        <td style="width:16%;" class="tableData"> {{ $orientador->CPF }} </td>
        <td style="width:16%;" class="tableData"> {{ $orientador->nome }} </td>
        <td style="width:16%;" class="tableData"> {{ $orientador->email }} </td>
        <td style="width:16%;" class="tableData"> {{ $orientador->externo==0? "N" : "S" }} </td>
        <td style="width:16%;" class="tableData"> {{ $orientador->aprovado==0? "N" : "S" }} </td>
        <td style="width:3.7%;" class="tableData"><a href="{{ route('orientador.edit', ['id'=>$orientador->id]) }}">Editar</a></td>
        @if ($orientador->aprovado==0 && $orientador->externo==1)
        <td style="width:3.7%;" class="tableData"><a href="{{ route('graduacao.aprova.cadastro', ['id'=>$orientador->id,'aprovacao'=>1]) }}">Aprovar</a>&nbsp;
                                                  <a href="{{ route('graduacao.aprova.cadastro', ['id'=>$orientador->id,'aprovacao'=>0]) }}">Reprovar</a>
        </td>
        @else
        <td style="width:3.7%;" class="tableData">---</td>            
        @endif
        <td style="width:3.7%;" class="tableData">
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