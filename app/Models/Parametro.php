<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parametro extends Model
{
    use HasFactory;

    /**
     * Método para busca de parâmetros gerais ou do usuário.
     */
    static function getDadosParam() {
        
        $dtAtual = date_create('now');
        $semestre = 1;
        if ($dtAtual->format('n') > 6)
            $semestre = 2;

        $dadosParam = Parametro::where("ano",date("Y"))->where('semestre',$semestre)->where('codpes',auth()->user()->codpes)->get();
        if ($dadosParam->isEmpty()) {
            $dadosParam = Parametro::where("ano",date("Y"))->where('semestre',$semestre)->whereNull('codpes')->get();
            if ($dadosParam->isEmpty())
                return false;
        }
        
        $dadosParam->first()->dataAberturaDiscente = date_create($dadosParam->first()->dataAberturaDiscente);
        $dadosParam->first()->dataFechamentoDiscente = date_create($dadosParam->first()->dataFechamentoDiscente);
        $dadosParam->first()->dataAberturaDocente = date_create($dadosParam->first()->dataAberturaDocente);
        $dadosParam->first()->dataFechamentoDocente = date_create($dadosParam->first()->dataFechamentoDocente);
        $dadosParam->first()->dataAberturaAvaliacao = date_create($dadosParam->first()->dataAberturaAvaliacao);
        $dadosParam->first()->dataFechamentoAvaliacao = date_create($dadosParam->first()->dataFechamentoAvaliacao);
        $dadosParam->first()->dataAberturaUploadTCC = date_create($dadosParam->first()->dataAberturaUploadTCC);
        $dadosParam->first()->dataFechamentoUploadTCC = date_create($dadosParam->first()->dataFechamentoUploadTCC);
        
        return $dadosParam->first();
    }
}
