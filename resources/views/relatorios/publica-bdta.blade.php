@extends('layouts.app')

@section('content')
<p><a href="{{ route('declaracao') }}">Retornar</a></p>

<h1>Relatório de Publicação BDTA - Ano de {{ $ano }}/ {{ $semestre }}º Semestre </h1>
<p>Neste relatório constam somente monografias aprovadas</p>

<table class="tableData">
    <thead>
        <tr>
            <th colspan="7" style="text-align:right;background:#c6c2eb;"><a href="{{ route('exportacao.rel-publica-bdta', ['ano'=> $ano])}}">Gerar Excel</a></th>
        </tr>
        <tr>
            <th style="width:20%;" class="tableData">Título da Monografia</th>
            <th style="width:20%;" class="tableData">Orientador</th>
            <th style="width:20%;" class="tableData">Nome do Aluno</th>
            <th style="width:20%;" class="tableData">Publica BDTA?</th>
        </tr>
    </thead>
    @foreach ($listMonografias as $monografia)
    @if (!empty($monografia->id))
    <tr>
        <td style="width:20%;" class="tableData">{{ $monografia->titulo }} </td>
        <td style="width:20%;" class="tableData">{{ $monografia->orientadores->first()->nome }}</td>
        <td style="width:20%;" class="tableData">{{ $monografia->alunos->first()->nome }}</td>
        <td style="width:20%;" class="tableData">{{ ($monografia->publicar)?"S":"N" }}</td>
    </tr>
    @endif
    @endforeach
</table>
{{ $listMonografias->links() }}

@endsection