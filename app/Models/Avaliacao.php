<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Avaliacao extends Model
{
    use HasFactory;

    protected $table = "avaliacoes";


    /**
     * Relação N:1
     */
    public function monografias() {
        return $this->belongsTo(Monografia::class,'avaliacao','monografia_id','id');
    }

    /**
     * Relação N:1
     */
    public function comissoes() {
        return $this->belongsTo(Comissao::class,'comissoes_id','id','id')->withTrashed();
    }

    static function excluirRegistro($id) {
        return DB::table('avaliacoes')->where('id', $id)->delete();
    }

}
