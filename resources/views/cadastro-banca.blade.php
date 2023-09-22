@extends('layouts.app')

@section('content')

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
            <th style="width:5%;" class="tableData">Número USP</th>
            <th style="width:30%;" class="tableData">Nome</th>
            <th style="width:40%;" class="tableData">Título Projeto</th>
            <th style="width:10%;" class="tableData">Email</th>
            <th style="width:10%;" class="tableData">A&ccedil;&otilde;es</th>
        </tr>
        
    </thead>
    @if (!empty($filtro))
        <tr><td class="tableData" colspan="7">Busca por termo: {{ $filtro }}<br/><a href="{{ route('banca.index') }}" >Resetar a busca</a></td></tr>
    @endif

    @foreach ($listBanca as $banca)
    @if (!empty($banca->id))
    <tr>
        <td style="width:5%;" class="tableData"> {{ !empty($banca->codpes)?$banca->codpes:'EXTERNO' }} </td>
        <td style="width:30%;" class="tableData"> {{ $banca->nome }} </td>
        <td style="width:40%;" class="tableData"> {{ $monografia[$banca->id]->titulo }} </td>
        <td style="width:10%;" class="tableData"> {{ $banca->email }} </td>
        <td style="width:10%;" class="tableData">
            @if (!empty($banca->arquivo_declaracao))
            <a href="declaracao_banca/{{ $banca->arquivo_declaracao }}" target="_blank">Declaração</a>
            @else
            Não participou da Banca    
            @endif
        </td>
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