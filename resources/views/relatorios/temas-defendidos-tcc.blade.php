@extends('layouts.app')

@section('content')
<p><a href="{{ route('declaracao') }}">Retornar</a></p>

<h1>Temas Defendidos de Trabalho de Conclusão de Curso de Farmácia Bioquímica {{ $ano }}/ Semestre {{ $semestre }} </h1>
<table class="tableData">
    <thead>
        <tr>
            <th colspan="5" style="text-align:right;background:#c6c2eb;"><a href="{{ route('exportacao.rel-temas-tcc', ['ano' => $ano, 'semestre' => $semestre])}}">Gerar Excel</a></th>
        </tr>
        <tr>
            <th style="width:4%;" class="tableData">N</th>
            <th style="width:20%;" class="tableData">Nome do Aluno</th>
            <th style="width:20%;" class="tableData">Orientador</th>
            <th style="width:24%;" class="tableData">Departamento</th>
            <th style="width:32%;" class="tableData">Título</th>
        </tr>
        @foreach ($listMonografia as $key=>$monografia)
        <tr>
            <td style="width:4%;" class="tableData">{{ ++$key }}</td>
            <td style="width:20%;" class="tableData">{{ $monografia->alunos->first()->nome }}</td>
            <td style="width:20%;" class="tableData">{{ $monografia->orientadores->first()->nome }} </td>
            <td style="width:24%;" class="tableData">{{ $monografia->orientadores->first()->instituicao_vinculo }} </td>
            <td style="width:32%;" class="tableData">{{ $monografia->titulo }} </td>
        </tr>
        @endforeach
    </thead>
</table>

@endsection