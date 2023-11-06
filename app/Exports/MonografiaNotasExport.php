<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

use App\Models\Monografia;

class MonografiaNotasExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $ano = substr($_GET['route'],strlen($_GET['route'])-6,4);
        $semestre = substr($_GET['route'],strlen($_GET['route'])-1,1);
        
        return Monografia::with(['notas','alunos','orientadores'])
                         ->where('ano',$ano)
                         ->where('semestre',$semestre)
                         ->get();
    }


    public function headings():array {
        return ['nusp aluno'
               ,'nome aluno'
               ,'nota projeto'
               ,'%frequencia projeto'
               ,'nota tcc'
               ,'%frequencia tcc'
               ];
    }

    public function map($linha):array {
        $campos = array();
        
        $campos[] = $linha->alunos->first()->id;
        $campos[] = $linha->alunos->first()->nome;
        foreach($linha->notas as $key => $nota) {
            if ($nota->tipo_nota == "TCC" && $key==0) {
                $campos[] = null;
                $campos[] = null;
            }
            $campos[] = $nota->nota;
            $campos[] = $nota->frequencia;
        }
        
        return $campos;
    }
}
