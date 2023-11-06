<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nota extends Model
{
    use HasFactory;

    /**
     * Relação N:1
     */
    public function monografias() {
        return $this->belongsTo(Monografia::class);
    }
}
