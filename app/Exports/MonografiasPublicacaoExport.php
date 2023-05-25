<?php

namespace App\Exports;

use App\Models\Monografia;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MonografiasPublicacaoExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //return Monografia::all();
        $ano = substr($_GET['route'],strlen($_GET['route'])-4,4);
        return Monografia::with(['alunos','orientadores','avaliacoes'])
                         ->where('ano', $ano)
                         ->where('status','CONCLUIDO')
                         ->whereRelation('avaliacoes', 'status', 'APROVADO')
                         ->get();
    }

    public function headings():array {
        return ['título da monografia'
               ,'orientador'
               ,'co-orientadores'
               ,'nome do(s) aluno(s)'
               ,'ano'
               ,'publica BDTA?'
               ];
    }

    public function map($linha):array {
        $campos = array();
        
        $campos[] = $linha->titulo;
        $coOrientadores = null;
        foreach ($linha->orientadores as $objOrientador) {
            if ($objOrientador->pivot->principal)
                $orientadorPrincipal = $objOrientador->nome;
            else
                $coOrientadores.= $objOrientador->nome.',';
        };
        if (empty($coOrientadores)) $coOrientadores = "---";
        $campos[] = $orientadorPrincipal;
        $campos[] = $coOrientadores;
        
        $alunos = null;
        foreach($linha->alunos as $objAluno) {
            $alunos.=$objAluno->nome.",";
        }
        $campos[] = $alunos;
        $campos[] = $linha->ano;
        if ($linha->publica)
            $campos[] = "S";
        else
            $campos[] = "N";
        
        return $campos;
    }
}
