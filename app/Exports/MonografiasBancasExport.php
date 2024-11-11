<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

use App\Models\Monografia;
use Uspdev\Replicado\Pessoa;

class MonografiasBancasExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $ano = substr($_GET['route'],strlen($_GET['route'])-6,4);
        $semestre = substr($_GET['route'],strlen($_GET['route'])-1,1);
        
        return Monografia::with(['bancas','alunos','orientadores','defesas'])
                         ->where('ano',$ano)
                         ->where('semestre',$semestre)
                         ->whereRelation('defesas','dataEscolhida', null)
                         ->get();
    }

    public function headings():array {
        return ['aluno'
               ,'nusp aluno'
               ,'email aluno'
               ,'ciente'
               ,'orientador'
               ,'email orientador'
               ,'depto orientador'
               ,'membro 2'
               ,'email membro 2'
               ,'depto membro 2'
               ,'membro 3'
               ,'email membro 3'
               ,'depto membro 3'
               ,'suplente'
               ,'email suplente'
               ,'depto suplente'
               ,'titulo'
               ,'data hora 1'
               ,'data hora 2'
               ,'data hora 3'
               ];
    }

    public function map($linha):array {
        $campos = array();
        
        $emailAluno = Pessoa::emailusp($linha->alunos->first()->id);
        
        $campos[] = $linha->alunos->first()->nome;
        $campos[] = $linha->alunos->first()->id;
        $campos[] = $emailAluno;
        $campos[] = 'S';
        $campos[] = $linha->orientadores->first()->nome;
        $campos[] = $linha->orientadores->first()->email;
        $campos[] = $linha->orientadores->first()->instituicao_vinculo;
        foreach($linha->bancas as $banca) {
            if ($banca->papel == 'PRESIDENTE') {
                continue;
            }
            $campos[] = $banca->nome;
            $campos[] = $banca->email;
            $campos[] = $banca->instituicao_vinculo;
        }
        $campos[] = $linha->titulo;
        $campos[] = date_create($linha->defesas->first()->dataDefesa1)->format('d/m/Y H:i:s');
        $campos[] = date_create($linha->defesas->first()->dataDefesa2)->format('d/m/Y H:i:s');
        $campos[] = date_create($linha->defesas->first()->dataDefesa3)->format('d/m/Y H:i:s');
        
        return $campos;
    }
}
