<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Replicado extends Model
{
    use HasFactory;

    /**
     * Método para pegar os dados de Pessoas na tabela do replicado
     * @param codpes int Número USP
     * @param tipoVinculo string [Opcional] Tipo de Vínculo
     */
    function getDadosPessoas(int $codpes = 0, string $tipoVinculo = null) {
        //Buscar no banco de dados replicado

        $query = "select vpu.[codpes] as numUSP
                       , vpu.[nompes] as nome
                       , vpu.[tipvin] as vinculo
                       , vpu.[dtainivin] as dt_iniVinculo
                       , vpu.[dtafimvin] as dt_fimVinculo
                       , vpu.[nivpgm] as Nivel
                       , ep.codema as email 
                   from [dbmaint].[VINCULOPESSOAUSP] as vpu 
                   left join [dbmaint].[EMAILPESSOA] as ep on vpu.codpes = ep.codpes 
                   where (vpu.codund = '7' OR vpu.codclg = '7') 
                     and (vpu.[dtafimvin] is null OR dtafimvin >= CONVERT(DATE, '2999-01-01 00:00:00', 20)) 
                     and ep.stausp = 'S' ";

        if (!empty($tipoVinculo)) {
            if ($tipoVinculo == "DOCENTE") {
                $query.= " and exists (select 1 from dbmaint.DOCENTE where codpes = vpu.codpes) ";
            } else {
                $query.= " and vpu.tipvin = '$tipoVinculo' ";
            }
        }

        if ($codpes > 0)
            $query.=" and vpu.codpes = $codpes ";

        $query.= " order by vpu.nompes ";

        $users = DB::connection('replicado')->select($query);
        
        /*$db->table('[dbmaint].[VINCULOPESSOAUSP] as vpu')
                           ->leftJoin('[dbmaint].[EMAILPESSOA] as ep', 'vpu.codpes', '=', 'ep.codpes')
                           ->select('vpu.[codpes] as numUSP','vpu.[nompes] as nome','vpu.[tipvin] as vinculo'
                                   ,'vpu.[dtainivin] as dt_iniVinculo','vpu.[dtafimvin] as dt_fimVinculo'
                                   ,'vpu.[nivpgm] as Nivel','ep.codema as email')
                           ->where('vpu.codclg',7)
                           ->orWhere(function($query) {
                                $query->whereNull('vpu.[dtafimvin]')
                                      ->where('vpu.dtafimvin', '>=', '2027-01-01 00:00:00');
                            })                
                           ->where('ep.stausp','S');

        if (!empty($tipoVinculo))
            $db->where('vpu.tipvin', $tipoVinculo);
        
        if ($codpes > 0)
            $db->where('vpu.codpes', $codpes);
                           
        $db->orderBy('vpu.nompes');*/

        return $users;
    }
}
