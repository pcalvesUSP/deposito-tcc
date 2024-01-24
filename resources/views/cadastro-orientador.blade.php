@extends('layouts.app')

@section('content')

<div class="erro" id="mensagem"> {{ $mensagem }}</div>
@if (strpos($_GET['route'],"comissao") === false) <p style="align:right"><a href="orientador/novoCadastro">Cadastrar Novo</a></p> @endif
<h1>Orientadores @if (strpos($_GET['route'],"comissao") !== false) para Aprovação @endif</h1>

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
        <td style="width:16%;" class="tableData"> {{ $orientador->nome }} @if (!empty($orientador->nusp_aprovador)) <br/><span style="color:red;">Cadastro já {{ ($orientador->aprovado)?'Aprovado':'Reprovado' }}. N.USP Aprovador: {{ $orientador->nusp_aprovador }} </span> @endif </td>
        <td style="width:16%;" class="tableData"> {{ $orientador->email }} </td>
        <td style="width:16%;" class="tableData"> {{ $orientador->externo==0? "N" : "S" }} </td>
        <td style="width:16%;" class="tableData"> {{ $orientador->aprovado==0? "N" : "S" }} </td>
        <td style="width:3.7%;" class="tableData"><a href="{{ route('orientador.edit', ['id'=>$orientador->id]) }}">@if (strpos($_GET['route'],"comissao") === false) Editar @else Visualizar @endif </a></td>
        @if (!$orientador->aprovado && $orientador->externo && empty($orientador->nusp_aprovador))
        <td style="width:3.7%;" class="tableData"><a href="{{ route('graduacao.aprova.cadastro', ['id'=>$orientador->id,'aprovacao'=>1]) }}">Aprovar</a>&nbsp;
                                                  <a href="{{ route('graduacao.aprova.cadastro', ['id'=>$orientador->id,'aprovacao'=>0]) }}">Reprovar</a>
        </td>
        @else
        <td style="width:3.7%;" class="tableData">---</td>            
        @endif
        <td style="width:3.7%;" class="tableData">
        @if (strpos($_GET['route'],"comissao") === false)
        <form id="deleteOrientador_{{ $orientador->id }}" action={{ route('orientador.destroy', ['id'=>$orientador->id])}} method="post"> 
        @csrf
        @method('DELETE') 
        <input type="submit" value="Excluir" style="background-color:transparent;" onmouseover="return $(this).css({'color':'blue','text-decoration':'underline'})" onmouseout="return $(this).css({'color':'black','text-decoration':'none'})">
        </form>
        @else
        &nbsp;
        @endif
        </td>
    </tr>
    @endif
    @endforeach
</table>
@if (!empty($orientador->nome)) {{ $listOrientadores->links() }} @endif

<script>
    setTimeout(function() {
                $('#mensagem').fadeOut('fast');
              }, 3000);

    $( document ).ready(function(){

       $("#filtro").keypress(function( event ) {
            if (event.which == 13) {
                if ($(this).val().length >= 3) {
                    document.getElementById("filtrarOrientador").submit();
                } else {
                    alert("O termo a ser localizado deve conter 3 ou mais caracteres.");
                    return false;
                }
            }
        });
        
    });

</script>

@endsection