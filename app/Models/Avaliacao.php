<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

}
