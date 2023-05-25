<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Orientador extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "orientadores";

    /**
     * Recupera todos os dados de Orientador e suas respectivas monografias
     * @var number $id_orientador Id do Orientador
     * @var int $id_monografias Id da monografia, opcional
     */
    public function listOrientador($id_orientador = 0, $id_monografias = 0) {

        $build = DB::table('orientadores as o')
                   ->join("mono_orientadores as om","o.id","=","om.orientadores_id")
                   ->select("o.*","om.principal");
        
        if ($id_orientador > 0)
            $build->where("o.id",$id_orientador);
        
        if ($id_monografias > 0) {
            $build->where("om.monografia_id",$id_monografias);
        }
        
        return $build->get();
    }

    /**
     * Relação N:N
     */
    public function monografias() {
        return $this->belongsToMany(Monografias::class,'mono_orientadores','orientadores_id','monografia_id')->withPivot('principal');
    }
}
