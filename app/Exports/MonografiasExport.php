<?php

namespace App\Exports;

use App\Models\Monografia;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MonografiasExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $ano = substr($_GET['route'],strlen($_GET['route'])-4,4);
        return Monografia::with(['alunos','orientadores','avaliacoes'])
                         ->where('ano',$ano)
                         ->get();
    }

    public function headings():array {
        return ['título da monografia'
               ,'orientador'
               ,'co-orientadores'
               ,'nome do(s) aluno(s)'
               ,'ano'
               ,'status'
               ];
    }

    public function map($linha):array {
        $campos = array();

        if (!$linha->orientadores->isEmpty()) {
        
            $campos[] = $linha->titulo;
            $coOrientadores = null;
            
            foreach ($linha->orientadores as $objOrientador) {
                if ($objOrientador->pivot->principal)
                    $campos[] = $objOrientador->nome;
                else
                    $coOrientadores.= $objOrientador->nome.',';
            };
            if (empty($coOrientadores)) $coOrientadores = "---";
            $campos[] = $coOrientadores;
            
            $alunos = null;
            foreach($linha->alunos as $objAluno) {
                $alunos.=$objAluno->nome.",";
            }
            $campos[] = $alunos;
            $campos[] = $linha->ano;
            $campos[] = $linha->status;
        }
        return $campos;
    }
}
