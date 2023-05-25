<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MonoOrientadores extends Model
{
    use HasFactory;
    protected $table = "mono_orientadores";
    protected $primaryKey = ['orientadores_id', 'monografia_id'];
    public $incrementing = false;

    /**
     * Apagar todos os registros de determinada monografia
     * @param id ID dos dados da monografia a ser cadastrada
     */
    static function excluirRegistroByMonografia($id) {
        return DB::table('mono_orientadores')->where('monografia_id', $id)->delete();
    }

}
