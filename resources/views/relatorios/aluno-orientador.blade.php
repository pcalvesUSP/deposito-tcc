@extends('layouts.app')

@section('content')
<p><a href="{{ route('declaracao') }}">Retornar</a></p>

<h1>Relatório Alunos-Orientadores - Ano {{ $ano }}/ Semestre {{ $semestre }} - Trabalhos Concluídos </h1>
<table class="tableData">
    <thead>
        <tr>
            <th colspan="4" style="text-align:right;background:#c6c2eb;"><a href="{{ route('exportacao.rel-aluno-orientador', ['ano' => $ano])}}">Gerar Excel</a></th>
        </tr>
        <tr>
            <th style="width:25%;" class="tableData">Título da Monografia</th>
            <th style="width:25%;" class="tableData">Orientador</th>
            <th style="width:25%;" class="tableData">Co-Orientador(es)</th>
            <th style="width:25%;" class="tableData">Nome do Aluno</th>
        </tr>
    </thead>
    @foreach ($listMonografias as $monografia)
    @if (!empty($monografia->id))
    <tr>
        <td style="width:25%;" class="tableData"> {{ $monografia->titulo }} </td>
        <td style="width:25%;" class="tableData"> 
        @foreach ($monografia->orientadores as $key => $orientador)
             @if ($orientador->pivot->principal) 
                {{ $orientador->nome }}
                @break
            @endif
        @endforeach
        </td>
        <td style="width:25%;" class="tableData"> 
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
        <td style="width:25%;" class="tableData">
            @foreach($monografia->alunos as $key => $aluno) 
                @if ($key > 0)
                    <br/>
                @endif
                {{ $aluno->nome }}
            @endforeach
        </td>
    </tr>
    @endif
    @endforeach
</table>
{{ $listMonografias->links() }}

@endsection