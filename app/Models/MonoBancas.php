<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MonoBancas extends Model {
    
    use HasFactory;
    protected $table = "mono_bancas";

    /**
     * Relação Monografia N:1
     */
    public function monografia() {
        return $this->hasMany(Monografia::class, 'mono_bancas','id','monografia_id');
    }

    /**
     * Relação Banca N:1
     */
    public function banca() {
        return $this->hasMany(Banca::class, 'mono_bancas','id','banca_id');
    }
}