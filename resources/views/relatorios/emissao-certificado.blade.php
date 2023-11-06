@extends('layouts.app')

@section('content')
<p><a href="{{ route('declaracao') }}">Retornar</a></p>

<h1>Relatório para emissão de Certificados - Ano de {{ $ano }}/ {{ $semestre }}º Semestre </h1>
<p>Neste relatório constam somente monografias concluídas</p>

<table class="tableData">
    <thead>
        <tr>
            <th colspan="7" style="text-align:right;background:#c6c2eb;"><a href="{{ route('exportacao.rel-emissao-cert',['ano'=>$ano])}}">Gerar Excel</a></th>
        </tr>
        <tr>
            <th style="width:25%;" class="tableData">Título da Monografia</th>
            <th style="width:25%;" class="tableData">Orientador</th>
            <th style="width:25%;" class="tableData">Aluno</th>
            <th style="width:25%;" class="tableData">Data e Horário da Defesa</th>
        </tr>
    </thead>
    @foreach ($listMonografias as $monografia)
    @if (!empty($monografia->id))
    <tr>
        <td style="width:25%;" class="tableData">{{ $monografia->titulo }}</td>
        <td style="width:25%;" class="tableData">{{ $monografia->orientadores->first()->nome }}</td>
        <td style="width:25%;" class="tableData">{{ $monografia->alunos->first()->nome }}</td>
        <td style="width:25%;" class="tableData">{{ date_create($monografia->defesas->first()->dataEscolhida)->format('d/m/Y H:i') }}</td>
    </tr>
    @endif
    @endforeach
</table>
{{ $listMonografias->links() }}

@endsection