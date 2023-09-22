<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MonoOrientadores extends Model
{
    use HasFactory;
    protected $table = "mono_orientadores";

    /**
     * Apagar todos os registros de determinada monografia
     * @param id ID dos dados da monografia a ser cadastrada
     */
    static function excluirRegistroByMonografia($id) {
        return DB::table('mono_orientadores')->where('monografia_id', $id)->delete();
    }

    public function orientadores() {
        return $this->hasMany(Orientador::class,'id','orientadores_id');
    }

    public function monografias() {
        return $this->hasMany(Monografia::class,'id','monografia_id');
    }

}
