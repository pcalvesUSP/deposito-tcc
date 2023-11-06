@extends('layouts.app')

@section('content')
<p><a href="{{ route('declaracao') }}">Retornar</a></p>

<h1>Relatório de Notas do Projeto e TCC Final {{ $ano }}/ Semestre {{ $semestre }} </h1>
<table class="tableData">
    <thead>
        <tr>
            <th colspan="4" style="text-align:right;background:#c6c2eb;"><a href="{{ route('exportacao.rel-notas-tcc', ['ano' => $ano, 'semestre' => $semestre])}}">Gerar Excel</a></th>
        </tr>
        <tr>
            <th style="width:25%;" class="tableData">N.º USP do Aluno</th>
            <th style="width:25%;" class="tableData">Nome do Aluno</th>
            <th style="width:25%;" class="tableData">Nota / Frequencia Projeto</th>
            <th style="width:25%;" class="tableData">Nota / Frequencia TCC</th>
        </tr>
        @foreach ($listMonografia as $monografia)
        <tr>
            <td style="width:25%;" class="tableData">{{ $monografia->alunos->first()->id }}</td>
            <td style="width:25%;" class="tableData">{{ $monografia->alunos->first()->nome }}</td>
            @foreach ($monografia->notas as $key => $nota)
                @if ($nota->tipo_nota == "TCC" && $key==0)
                <td style="width:25%;" class="tableData">&nbsp</td>
                <td style="width:25%;" class="tableData">{{ $nota->nota }} / {{ $nota->frequencia }}% </td>
                @else
                <td style="width:25%;" class="tableData">{{ $nota->nota }} / {{ $nota->frequencia }}% </td>
                @endif
            @endforeach
            @if ($monografia->notas->count() == 0)
            <td style="width:25%;" class="tableData">&nbsp</td>
            <td style="width:25%;" class="tableData">&nbsp</td>
            @endif
        </tr>
        @endforeach
    </thead>
</table>

@endsection