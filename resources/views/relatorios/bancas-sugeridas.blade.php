@extends('layouts.app')

@section('content')
<p><a href="{{ route('declaracao') }}">Retornar</a></p>

<h1>Relatório de Bancas Sugeridas ({{ $semestre }}/{{ $ano }}) </h1>
<table class="tableData">
    <thead>
        <tr>
            <th colspan="11" style="text-align:right;background:#c6c2eb;"><a href="{{ route('exportacao.rel-banca-sugerida', ['ano' => $ano, 'semestre' => $semestre])}}">Gerar Excel</a></th>
        </tr>
        <tr>
            <th style="width:9.09%;" class="tableData">Aluno</th>
            <th style="width:9.09%;" class="tableData">N.º USP</th>            
            <th style="width:9.09%;" class="tableData">E-mail Aluno</th>
            <th style="width:3.09%;" class="tableData">Ciente Orientador</th>
            <th style="width:9.09%;" class="tableData">Orientador</th>
            <th style="width:9.09%;" class="tableData">E-mail Orientador</th>
            <th style="width:9.09%;" class="tableData">Depto Orientador</th>
            <th style="width:12.09%;" class="tableData">Banca (excluindo o Orientador)</th>
            <th style="width:9.09%;" class="tableData">Titulo Projeto</th>
            <th style="width:12.09%;" class="tableData">Datas e horários sugeridos</th>
        </tr>
    </thead>
    @foreach ($listMonografia as $monografia)
    @if (!empty($monografia->id))
    <tr>
        <td style="width:9.09%;" class="tableData">{{ $monografia->alunos->first()->nome }}</td>
        <td style="width:9.09%;" class="tableData">{{ $monografia->alunos->first()->id }}</td>            
        <td style="width:9.09%;" class="tableData">{{ $emailAluno }}</td>
        <td style="width:3.09%;" class="tableData">S</td>
        <td style="width:9.09%;" class="tableData">{{ $monografia->orientadores->first()->nome }}</td>
        <td style="width:9.09%;" class="tableData">{{ $monografia->orientadores->first()->email}}</td>
        <td style="width:9.09%;" class="tableData">{{ $monografia->orientadores->first()->instituicao_vinculo}}</td>
        <td style="width:12.09%;" class="tableData">
            <table class="table table-borderless">
                <tr>
                    <th>Papel</th>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Depto</th>
                </tr>
                @foreach ($monografia->bancas as $banca)
                    @if ($banca->papel == "PRESIDENTE")
                        @continue
                    @endif
                    <tr>
                        <td>{{ $banca->papel }}</td>
                        <td>{{ $banca->nome }}</td>
                        <td>{{ $banca->email }}</td>
                        <td>{{ $banca->instituicao_vinculo }}</td>
                    </tr>
                @endforeach
            </table>
        </td>
        <td style="width:9.09%; font-size=8px;" class="tableData">{{ $monografia->titulo }}</td>
        <td style="width:12.09%; font-size=8px;" class="tableData">
        Data 1: {{ date_create($monografia->defesas->first()->dataDefesa1)->format('d/m/Y H:i') }}<br/>
        Data 2: {{ date_create($monografia->defesas->first()->dataDefesa2)->format('d/m/Y H:i') }}<br/>
        Data 3: {{ date_create($monografia->defesas->first()->dataDefesa3)->format('d/m/Y H:i') }}
        </td>
    </tr>
    @else
        <tr>
            <td colspan="18">Ainda não existem sugestões cadastradas</td>
        </tr>
        @break
    @endif
    @endforeach
</table>


@endsection