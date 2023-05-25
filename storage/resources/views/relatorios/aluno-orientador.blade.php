@extends('layouts.app')

@section('content')
<p><a href="{{ route('declaracao') }}">Retornar</a></p>

<h1>Relatório Alunos-Orientadores - Ano de {{ $ano }} </h1>
<table class="tableData">
    <thead>
        <tr>
            <th colspan="7" style="text-align:right;background:#c6c2eb;"><a href="{{ route('exportacao.rel-aluno-orientador', ['ano' => $ano])}}">Gerar Excel</a></th>
        </tr>
        <tr>
            <th style="width:20%;" class="tableData">Título da Monografia</th>
            <th style="width:20%;" class="tableData">Orientador</th>
            <th style="width:20%;" class="tableData">Co-Orientador(es)</th>
            <th style="width:20%;" class="tableData">Nome do Aluno</th>
            <th style="width:20%;" class="tableData">Status</th>
        </tr>
    </thead>
    @foreach ($listMonografias as $monografia)
    @if (!empty($monografia->id))
    <tr>
        <td style="width:20%;" class="tableData"> {{ $monografia->titulo }} </td>
        <td style="width:20%;" class="tableData"> 
        @foreach ($monografia->orientadores as $key => $orientador)
             @if ($orientador->pivot->principal) 
                {{ $orientador->nome }}
                @break
            @endif
        @endforeach
        </td>
        <td style="width:20%;" class="tableData"> 
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
        <td style="width:20%;" class="tableData">
            @foreach($monografia->alunos as $key => $aluno) 
                @if ($key > 0)
                    <br/>
                @endif
                {{ $aluno->nome }}
            @endforeach
        </td>
        <td style="width:20%;" class="tableData">{{ $monografia->status }}
            @if ($monografia->status == "CONCLUIDO")
                 -> {{ $monografia->avaliacoes->first()->status }}
            @endif
        </td>
    </tr>
    @endif
    @endforeach
</table>
{{ $listMonografias->links() }}

@endsection