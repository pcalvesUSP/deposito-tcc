@extends('layouts.app')

@section('content')
<h1 style="text-align:center;">Lista de Projetos @if (strpos($userLogado,'Avaliador') !== false && strpos($_GET['route'],"graduacao") !== false) para Avaliação @elseif ($indicarParecerista) para Indicação de Parecerista @else para Orientação @endif </h1>

<table class="tableData" id="listMonografias" border="1">
    <thead>
    <form id="filtrarStatus" action="{{ route('busca.monografia') }}" method="post">
        @csrf
        <input type="hidden" name="id_orientador" value="{{ $id_orientador }}">
        <tr>
            <th colspan="7" style="text-align:center;background:#c6c2eb;">
                Andamento:
                <select name="filtroStatus" id="filtroStatus" @if ($indicarParecerista) disabled @endif>
                    <option value="" selected>Lista de Projetos em Andamento</option>
                    <option @if(!empty($status) && $status == "AGUARDANDO APROVACAO DO ORIENTADOR") selected @endif  value="AGUARDANDO APROVACAO DO ORIENTADOR">Aguardando aprovação do Orientador</option>
                    <option @if((!empty($status) && $status == "AGUARDANDO AVALIACAO") || $indicarParecerista) selected @endif  value="AGUARDANDO AVALIACAO">Aguardando Avaliação</option>
                    <option @if(!empty($status) && $status == "AGUARDANDO CORRECAO DO PROJETO") selected @endif value="AGUARDANDO CORRECAO DO PROJETO">Aguardando Correção do Projeto</option>
                    <option @if(!empty($status) && $status == "AGUARDANDO NOTA DO PROJETO") selected @endif value="AGUARDANDO NOTA DO PROJETO">Aguardando Nota do Projeto</option>
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
        <th colspan="7" style="text-align:right;background:#c6c2eb;">Filtrar: <input type="text" id="filtro" name="filtro" value="{{ empty($filtro)?old('filtro'):$filtro }}" size="15" style="font-weight:bold;width:150px;border: solid 1px blue;"/></th>
      </tr>
    </form>
    <tr>
        <th style="width:20%" class="tableData">Titulo</th>
        <th style="width:20%" class="tableData">Aluno</th>
        <th style="width:20%" class="tableData">Orientador</th>
        <th style="width:20%" class="tableData">Ano</th>
        <th style="width:20%" class="tableData" @if ($userLogado == "Orientador") colspan="4" @else colspan="2" @endif >Ações</th>
    </tr>
    </thead>
    @if (!empty($filtro))
        <tr><td class="tableData" colspan="5">Busca por termo: {{ $filtro }}<br/><a href="{{ route('orientador.lista_monografia') }}" >Resetar a busca</a></td></tr>
    @endif
    
    @php
        $idMono = 0;
    @endphp

    @if ($dadosMonografias->isEmpty())
        <tr><td colspan="5" style="text-align: center;">Nenhum registro encontrado</td></tr>
    @endif
    
    @foreach ($dadosMonografias as $objMonografia)
        @if ($idMono == $objMonografia->id )
            @continue
        @endif
    <tr>
        <td style="width:20%" class="tableData">{{ $objMonografia->titulo }}</td>
        <td style="width:20%" class="tableData">
        @foreach ($grupoAlunos[$objMonografia->id] as $k=>$aluno)
            @if ($k!=0) <br/> @endif
            {{ $aluno->id }} - {{ $aluno->nome }}
        @endforeach
        </td>
        <td style="width:20%" class="tableData">
        @foreach ($dadosOrientadores[$objMonografia->id] as $k=>$orientador)
            @if ($k!=0) <br/> @endif
            {{ $orientador->codpes }} {{ $orientador->nome }}
        @endforeach
        </td>
        <td style="width:20%" class="tableData">{{ $objMonografia->semestre }}-{{ $objMonografia->ano }}</td>
        
        @if (empty($sistema_aberto[$objMonografia->id]) && strpos($userLogado,'Orientador') !== false && (strpos($_GET['route'],"orientador") !== false || strpos($_GET['route'],"buscaMonografia") !== false))
            <td style="width:6.25%" class="tableData" colspan="4"><a href="{{ route('orientador.edicao',['idMono'=>$objMonografia->id]) }}">VISUALIZAR</a> </td>
        @elseif (empty($sistema_aberto[$objMonografia->id]) && strpos($userLogado,'Avaliador') !== false && strpos($_GET['route'],"graduacao") !== false)
            <td style="width:6.25%" class="tableData"colspan="4" ><a href="{{ route('graduacao.edicao',['idMono'=>$objMonografia->id]) }}">{{ ($objMonografia->status=="AGUARDANDO AVALIACAO")?'AVALIAR':'VIZUALIZAR' }}</a> </td>
        @elseif (empty($sistema_aberto[$objMonografia->id]) && strpos($userLogado,'Comissao') !== false && strpos($_GET['route'],"comissao") !== false)
            <td style="width:6.25%" class="tableData"colspan="4" ><a href="{{ route('graduacao.edicao',['idMono'=>$objMonografia->id]) }}">VIZUALIZAR</a> </td>
        @elseif (strpos($userLogado,"Graduacao") !== false || strpos($userLogado,"Admin") !== false)
            <td style="width:12.5%" class="tableData"><a href="{{ route('orientador.edicao',['idMono'=>$objMonografia->id ]) }}">VISUALIZAR/EDITAR</a></td> 
            <td style="width:12.5%" class="tableData">
            <form id="deleteMonografia_{{ $objMonografia->id }}" action={{ route('graduacao.excluirMonografia', ['id'=>$objMonografia->id])}} method="post"> 
            @csrf
            @method('DELETE') 
            <input type="submit" value="EXCLUIR" style="background-color:transparent;" onmouseover="return $(this).css({'color':'blue','text-decoration':'underline'})" onmouseout="return $(this).css({'color':'black','text-decoration':'none'})">
            </form>
            </td> 
        @else
        <td style="width:6.25%" class="tableData" colspan="4">{{ isset($sistema_aberto[$objMonografia->id])?$sistema_aberto[$objMonografia->id]:null }}</td>
        @endif
        </td>
    </tr>
    @php
        $idMono = $objMonografia->id;
    @endphp
@endforeach
</table>
    {{ $dadosMonografias->links() }}

<script>
    $( document ).ready(function(){

        $("#filtro").keypress(function( event ) {
            if (event.which == 13) {
                if ($(this).val().length >= 3) {
                    document.getElementById("filtrarMonografia").submit();
                } else {
                    alert("O termo a ser localizado deve conter 3 ou mais caracteres.");
				    return false;
                }
            }
        });

        $("#filtroStatus").change(function() {
            document.getElementById("filtrarStatus").submit();
        });

        
    });

</script>

@endsection