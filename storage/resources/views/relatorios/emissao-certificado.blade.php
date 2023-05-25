@extends('layouts.app')

@section('content')
<p><a href="{{ route('declaracao') }}">Retornar</a></p>

<h1>Relatório para emissão de Certificados - Ano de {{ $ano }} </h1>
<p>Neste relatório constam somente monografias aprovadas</p>

<table class="tableData">
    <thead>
        <tr>
            <th colspan="7" style="text-align:right;background:#c6c2eb;"><a href="{{ route('exportacao.rel-emissao-cert',['ano'=>$ano])}}">Gerar Excel</a></th>
        </tr>
        <tr>
            <th style="width:16.66%;" class="tableData">Título da Monografia</th>
            <th style="width:16.66%;" class="tableData">Orientador</th>
            <th style="width:16.66%;" class="tableData">Co-Orientador(es)</th>
            <th style="width:16.66%;" class="tableData">Nome do Aluno</th>
            <th style="width:16.66%;" class="tableData">Número da Mostra</th>
            <th style="width:16.66%;" class="tableData">Mês de Apresentação</th>
        </tr>
    </thead>
    @foreach ($listMonografias as $monografia)
    @if (!empty($monografia->id))
    <tr>
        <td style="width:16.66%;" class="tableData"> {{ $monografia->titulo }} </td>
        <td style="width:16.66%;" class="tableData"> 
        @foreach ($monografia->orientadores as $key => $orientador)
             @if ($orientador->pivot->principal) 
                {{ $orientador->nome }}
                @break
            @endif
        @endforeach
        </td>
        <td style="width:16.66%;" class="tableData">
        @if ($monografia->orientadores()->where('mono_orientadores.principal',0)->count() > 0)
            @foreach ($monografia->orientadores as $key => $orientador)
                @if (!$orientador->pivot->principal) 
                    {{ $orientador->nome }}
                    <br/>
                @endif
            @endforeach
        @else
        ---
        @endif
        </td>
        <td style="width:16.66%;" class="tableData">
            @foreach($monografia->alunos as $key => $aluno) 
                @if ($key > 0)
                    <br/>
                @endif
                {{ $aluno->nome }}
            @endforeach
        </td>
        <td style="width:16.66%;" class="tableData"> {{ $mostra->numero }} </td>
        <td style="width:16.66%;" class="tableData"> {{ $mostra->mes }} </td>
    </tr>
    @endif
    @endforeach
</table>
{{ $listMonografias->links() }}

@endsection