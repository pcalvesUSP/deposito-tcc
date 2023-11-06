<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

use App\Models\Monografia;

class MonografiasTemasExport implements FromCollection, WithHeadings, WithMapping
{
    
    protected $numLn = 0;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $ano = substr($_GET['route'],strlen($_GET['route'])-6,4);
        $semestre = substr($_GET['route'],strlen($_GET['route'])-1,1);
        
        return Monografia::with(['alunos','orientadores'])
                         ->where('ano',$ano)
                         ->where('semestre',$semestre)
                         ->get();
    }

    public function headings():array {
        return ['#'
               ,'nome aluno'
               ,'orientador'
               ,'departamento'
               ,'titulo'
               ];
    }

    public function map($linha):array {
        $campos = array();

        $this->numLn = ++$this->numLn;

        $campos[] = $this->numLn;        
        $campos[] = $linha->alunos->first()->nome;
        $campos[] = $linha->orientadores->first()->nome;
        $campos[] = $linha->orientadores->first()->instituicao_vinculo;
        $campos[] = $linha->titulo;
        
        return $campos;
    }
}
