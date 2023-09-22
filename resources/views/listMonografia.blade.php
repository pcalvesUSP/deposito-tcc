@extends('layouts.app')

@section('content')

<h1 style="text-align:center;">Lista de Monografias @if ($userLogado == "Orientador") para avaliação @endif </h1>

<table class="tableData" id="listMonografias" border="1">
    <thead>
    <form id="filtrarStatus" action="{{ route('busca.monografia') }}" method="post">
        @csrf
        <input type="hidden" name="id_orientador" value="{{ $id_orientador }}">
        <tr>
            <th colspan="7" style="text-align:center;background:#c6c2eb;">
                Andamento:
                <select name="filtroStatus" id="filtroStatus">
                    <option value="" selected>Lista de TCC em Andamento</option>
                    <option @if(!empty($status) && $status == "AGUARDANDO APROVACAO DO ORIENTADOR") selected @endif  value="AGUARDANDO APROVACAO DO ORIENTADOR">Aguardando aprovação do Orientador</option>
                    <option @if(!empty($status) && $status == "AGUARDANDO CORRECAO DO PROJETO") selected @endif value="AGUARDANDO CORRECAO DO PROJETO">Aguardando Correção do Projeto</option>
                    <option @if(!empty($status) && $status == "AGUARDANDO ARQUIVO TCC") selected @endif value="AGUARDANDO ARQUIVO TCC">Aguardando Arquivo TCC</option>
                    <option @if(!empty($status) && $status == "AGUARDANDO VALIDACAO DE BANCA") selected @endif value="AGUARDANDO VALIDACAO DE BANCA">Aguardando validação da Banca</option>
                    <option @if(!empty($status) && $status == "AGUARDANDO DEFESA") selected @endif value="AGUARDANDO DEFESA">Aguardando Defesa</option>
                    <option @if(!empty($status) && $status == "CONCLUIDO") selected @endif value="CONCLUIDO">Concluídos</option>
                </select>
        
            </th>
        </tr>
    </form>
    <form id="filtrarMonografia" action="{{ route('busca.monografia') }}" method="post">
      @csrf
      <input type="hidden" name="id_orientador" value="{{ $id_orientador }}">
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

    @if ($dadosMonografias->isEmpty())
        <tr><td colspan="7" style="text-align: center;">Nenhum registro encontrado</td></tr>
    @endif
    
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
            <td style="width:6.25%" class="tableData" colspan="4"><a href="{{ route('orientador.edicao',['idMono'=>$objMonografia->id]) }}">VISUALIZAR</a> </td>
        @elseif (empty($sistema_aberto) && $userLogado == "Avaliador")
            <td style="width:6.25%" class="tableData"colspan="4" ><a href="{{ route('orientador.edicao',['idMono'=>$objMonografia->id]) }}">{{ ($objMonografia->status=="AGUARDANDO AVALIACAO")?'AVALIAR':'VIZUALIZAR' }}</a> </td>
        @elseif ($userLogado == "Graduacao" || $userLogado == "Admin")
            <td style="width:12.5%" class="tableData"><a href="{{ route('orientador.edicao',['idMono'=>$objMonografia->id ]) }}">VISUALIZAR/EDITAR</a></td> 
            <td style="width:12.5%" class="tableData">
            <form id="deleteMonografia_{{ $objMonografia->id }}" action={{ route('graduacao.excluirMonografia', ['id'=>$objMonografia->id])}} method="post"> 
            @csrf
            @method('DELETE') 
            <input type="submit" value="EXCLUIR" style="background-color:transparent;" onmouseover="return $(this).css({'color':'blue','text-decoration':'underline'})" onmouseout="return $(this).css({'color':'black','text-decoration':'none'})">
            </form>
            </td> 
        @else
        <td style="width:6.25%" class="tableData" colspan="4">{{ $sistema_aberto }}</td>
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

        $("#filtroStatus").change(function() {
            document.getElementById("filtrarStatus").submit();
        });

        
    });

</script>

@endsection