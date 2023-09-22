@extends('layouts.app')

@section('content')

<h1 style="text-align:center;">Lista de Monografias @if ($userLogado == "Orientador") para avaliação @endif </h1>

<table class="tableData" id="listMonografias" border="1">
    <thead>
    <form id="filtrarMonografia" action="{{ route('busca.monografia') }}" method="post">
      @csrf
      <input type="hidden" name="id_orientador" value="{{ $id_orientador }}"
      <tr>
        <th @if ($userLogado == "Orientador") colspan="7" @else colspan="5" @endif style="text-align:right;background:#c6c2eb;">Filtrar: <input type="text" id="filtro" name="filtro" value="{{ empty($filtro)?old('filtro'):$filtro }}" size="15" style="font-weight:bold;width:150px;border: solid 1px blue;"/></th>
      </tr>
    </form>
    <tr>
        <th style="width:25%" class="tableData">Titulo</th>
        <th style="width:25%" class="tableData">Aluno(s)</th>
        <th style="width:25%" class="tableData">Ano</th>
        <th style="width:25%" class="tableData" @if ($userLogado == "Orientador") colspan="4" @else colspan="2" @endif >Ações</th>
    </tr>
    </thead>
    @if (!empty($filtro))
        <tr><td class="tableData" colspan="6">Busca por termo: {{ $filtro }}<br/><a href="{{ route('orientador.lista_monografia') }}" >Resetar a busca</a></td></tr>
    @endif
    
    @php
        $idMono = 0;
    @endphp
    
    @foreach ($dadosMonografias as $objMonografia)
        @if ($idMono == $objMonografia->id )
            @continue
        @endif

    <tr>
        <td style="width:25%" class="tableData">{{ $objMonografia->titulo }}</td>
        <td style="width:25%" class="tableData">
        @foreach ($grupoAlunos[$objMonografia->id] as $k=>$aluno)
            @if ($k!=0) <br/> @endif
            {{ $aluno->id }} - {{ $aluno->nome }}
        @endforeach
        </td>
        <td style="width:25%" class="tableData">{{ $objMonografia->ano }}</td>
        
        @if (empty($sistema_aberto) && $userLogado == "Orientador")
            <td style="width:6.25%" class="tableData" @if ($objMonografia->status != "AGUARDANDO AVALIACAO") colspan="4" @endif ><a href="{{ route('orientador.edicao',['idMono'=>$objMonografia->id]) }}">VISUALIZAR</a> </td>
            @if ($objMonografia->status == "AGUARDANDO AVALIACAO")
                <td style="width:6.25%" class="tableData"><a href="{{ route('orientador.avaliacao',['idMonografia'=>$objMonografia->id,'acao'=>'DEVOLVIDO']) }}">DEVOLVER</a></td>
                <td style="width:6.25%" class="tableData"><a href="{{ route('orientador.avaliacao',['idMonografia'=>$objMonografia->id,'acao'=>'APROVADO']) }}">APROVAR</a></td> 
                <td style="width:6.25%" class="tableData"><a href="{{ route('orientador.avaliacao',['idMonografia'=>$objMonografia->id,'acao'=>'REPROVADO']) }}">REPROVAR</a></td>
            @endif
        @elseif ($userLogado == "Graduacao" || $userLogado == "Admin")
            <td style="width:12.5%" class="tableData"><a href="{{ route('orientador.edicao',['idMono'=>$objMonografia->id]) }}">VISUALIZAR/EDITAR</a></td> 
            <td style="width:12.5%" class="tableData">
            <form id="deleteMonografia_{{ $objMonografia->id }}" action={{ route('graduacao.excluirMonografia', ['id'=>$objMonografia->id])}} method="post"> 
            @csrf
            @method('DELETE') 
            <input type="submit" value="EXCLUIR" style="background-color:transparent;" onmouseover="return $(this).css({'color':'blue','text-decoration':'underline'})" onmouseout="return $(this).css({'color':'black','text-decoration':'none'})">
            </form>
            </td> 
        @else
            {{ $sistema_aberto }}
        @endif
        </td>
    </tr>
    @php
        $idMono = $objMonografia->id;
    @endphp
@endforeach
</table>
@if (empty($filtro))
    {{ $dadosMonografias->links() }}
@endif
<script>
    $( document ).ready(function(){

        $("#filtro").keyup(function() {
            if ($(this).val().length > 3) {
                document.getElementById("filtrarMonografia").submit();
            }
        });
    });

</script>

@endsection