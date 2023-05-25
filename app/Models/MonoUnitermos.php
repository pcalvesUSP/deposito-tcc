<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MonoUnitermos extends Model
{
    use HasFactory;
    protected $table = "mono-unitermos";

    /**
     * Apagar todos os registros de determinada monografia
     * @param id ID dos dados da monografia a ser cadastrada
     */
    static function excluirRegistroByMonografia($id, array $noDelete) {
        return DB::table('mono-unitermos')->where('monografia_id', $id)->whereNotIn('unitermo_id',$noDelete)->delete();
    }

    /**
     * Relação Unitermos N:1
     */
    public function monografia() {
        return $this->hasMany(Monografia::class, 'mono-unitermos','id','monografia_id');
    }

    /**
     * Relação Unitermos N:1
     */
    public function unitermos() {
        return $this->hasMany(Unitermos::class, 'mono-unitermos','id','unitermo_id');
    }
}
