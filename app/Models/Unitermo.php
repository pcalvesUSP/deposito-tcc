<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unitermo extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'unitermo',
    ];

    /**
     * Relação Unitermos N:N
     */
    public function monografia() {
        return $this->belongsToMany(Monografia::class, 'mono-unitermos','unitermo_id','monografia_id');
    }

    /**
     * Usado para excluir alunos em lote através do id da Monografia
     * @param id Id da Monografia
     */
    static function excluirRegistroByMonografia($id) {
        return DB::table('mono-unitermos')->where('monografia_id', $id)->delete();
    }
}
