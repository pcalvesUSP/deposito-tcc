<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Monografia extends Model
{
    use HasFactory;

    /**
     * Relação Unitermos N:N
     */
    public function unitermos() {
        return $this->belongsToMany(Unitermo::class, 'mono-unitermos', 'monografia_id', 'unitermo_id')->orderBy('unitermo')->withTrashed();
    }

    /**
     * Relação N:1
     */
    public function alunos() {
        return $this->hasMany(Aluno::class);
    }

    /**
     * Relação Orientadores N:N
     */
    public function orientadores() {
        return $this->belongsToMany(Orientador::class, 'mono_orientadores', 'monografia_id', 'orientadores_id')->orderBy('orientadores.nome')->withPivot('principal')->withTrashed();
    }

    /**
     * Relação Avaliações 1:N
     */
    public function avaliacoes() {
        return $this->hasMany(Avaliacao::class, 'monografia_id', 'id')->orderBy('avaliacoes.dataAvaliacao', 'desc');
    }
    
    /**
     * Busca os dados de Monografia
     * @param id INT Opcional - Id da Monografia
     * @param orientador_id INT Opcional Id do Orientador
     * @param ano string Opcional Ano da monografia
     */
    public function getMonografia($id = 0, $orientador_id = 0, $ano = null) {
        $alunos = array();
        $orientadores = array();

        $build = DB::table('monografias')
                   ->select("monografias.*", "o.id as orientador_id", "o.codpes as numUspOrientador", "o.nome as nomeOrientador",
                            "om.principal","alunos.id as numUspAluno", "alunos.nome as nomeAluno"
                            ) 
                   ->join("mono_orientadores as om","monografias.id", "=" ,"om.monografia_id")
                   ->join("orientadores as o","o.id", "=", "om.orientadores_id")
                   ->join("alunos","monografias.id","=","alunos.monografia_id");

         if ($id > 0)
            $build->where("monografias.id", $id);

        /*if ($orientador_id > 0) {
            $lOr = MonoOrientadores::where('orientadores_id',$orientador_id)->get();
            $lOr = DB::table('mono_orientadores')
                     ->select('monografia_id')
                     ->where('orientadores_id', '=', $orientador_id)
                     ->get();

            $arrArg = [];
            foreach ($lOr as $orientId) {
                $arrArg[] = $orientId->monografia_id;
            }
            
            $build->whereIn('monografias.id', $arrArg);
        }*/
        if ($orientador_id > 0) {
            $build->where('o.id',$orientador_id);
        }

        if (!empty($ano)) {
            $build->where('monografias.ano',$ano);
        }
        
        $build->orderBy("monografias.ano")->orderBy("alunos.nome");
        
        $listMonografia = $build->distinct()->get();
        
        /*foreach($listMonografia as $objMonografia) {
            if ($id > 0) {
                $alunos[] = $objMonografia->numUspAluno;
                $orientadores[] = $objMonografia->numUspOrientador;
            } else {
                $alunos[$objMonografia->id] = $objMonografia->numUspAluno;
                $orientadores[$objMonografia->id] = $objMonografia->numUspOrientador;               
            }
        }

        $this->setIdAluno = $alunos;
        $this->setIdOrientador = $orientadores;*/
        
        return $listMonografia;
    }


    /**
     * Busca os dados de Monografia
     * @param filtro STRING padrão para busca em título, nome do aluno e ano
     * @param id INT Opcional - Id da Monografia
     * @param orientador_id INT Opcional Id do Orientador
     */
    public function getMonografiaByFiltro($filtro, $id = 0, $orientador_id = 0) {
        $alunos = array();
        $orientadores = array();

        $build = DB::table('monografias')
                   ->select("monografias.*", "o.id as orientador_id", "o.codpes as numUspOrientador", "o.nome as nomeOrientador",
                            "om.principal","alunos.id as numUspAluno", "alunos.nome as nomeAluno"
                            ) 
                   ->join("mono_orientadores as om","monografias.id", "=" ,"om.monografia_id")
                   ->join("orientadores as o","o.id", "=", "om.orientadores_id")
                   ->join("alunos","monografias.id","=","alunos.monografia_id");
                    
        
        if ($id > 0)
            $build->where("monografias.id", $id);

        if ($orientador_id > 0) {
            $build->where('o.id',$orientador_id);
        }
        /*$build->where('alunos.nome','like','%'.$filtro.'%');
        $build->orWhere([['monografias.ano','=', $filtro]
                        ,['monografias.titulo','like','%'.$filtro.'%']
                        ]);*/

        $build->where('alunos.nome','like','%'.$filtro.'%');
        $build->orWhere('monografias.titulo','like','%'.$filtro.'%');
        $build->orWhere('monografias.ano',$filtro);
        
        
        $build->orderBy("monografias.ano", "desc")->orderBy("alunos.nome")->orderBy("monografias.id");
        
        $listMonografia = $build->distinct()->get();

        return $listMonografia;
    }
}
