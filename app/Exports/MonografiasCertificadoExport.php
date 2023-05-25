<?php

namespace App\Exports;

use App\Models\Monografia;
use App\Models\Parametro;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MonografiasCertificadoExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //return Monografia::all();
        $ano = substr($_GET['route'],strlen($_GET['route'])-4,4);
        $parametrosMostra = Parametro::where("ano",$ano)->whereNull('codpes')->get();
        if ($parametrosMostra->isEmpty()) {
            print "<script>alert('O Sistema não está parametrizado para o ano informado.');</script>";
            return redirect(route('declaracao'));
        }

        $monografias = Monografia::with(['alunos','orientadores','avaliacoes'])
                                 ->where('ano', $ano)
                                 ->where('status','CONCLUIDO')
                                 ->whereRelation('avaliacoes', 'status', 'APROVADO')
                                 ->get();
        
        foreach ($monografias as $key => $monografia) {
            $monografias[$key]->mostra = $parametrosMostra->first()->mostra;
            $monografias[$key]->mesMostra = $parametrosMostra->first()->mesMostra;
        }
        
        return $monografias;
    }

    public function headings():array {
        return ['título da monografia'
               ,'orientador'
               ,'co-orientadores'
               ,'nome do(s) aluno(s)'
               ,'ano'
               ,'número da mostra'
               ,'mes de apresentação'
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
        $campos[] = $linha->mostra;
        $campos[] = $linha->mesMostra;
        
        return $campos;
    }
}
