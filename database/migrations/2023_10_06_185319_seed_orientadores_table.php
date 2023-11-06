<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Uspdev\Replicado\Pessoa;

class SeedOrientadoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $orientadores = Pessoa::ativosVinculo('Docente', '9');
        foreach($orientadores as $orientador) {
            $setores = Pessoa::listarVinculosSetores($orientador['codpes'],"9");
            $ramalUsp= Pessoa::obterRamalUsp($orientador['codpes']);
            $ramalUsp= str_replace("x","",$ramalUsp);
            $ramalUsp= str_replace("(0","(",$ramalUsp);
            $ramalUsp= trim(substr($ramalUsp,0,14));

            if (count($setores)) {
                $instituicao = "Faculdade de Ciências Farmacêuticas - ".$setores[2];
            }
            DB::table('orientadores')->insertOrIgnore([
                'codpes'             => $orientador['codpes'],
                'nome'               => $orientador['nompes'],
                'email'              => Pessoa::emailusp($orientador['codpes']),
                'instituicao_vinculo'=> $instituicao,
                'telefone'           => $ramalUsp,
                'aprovado'           => 1,
                'created_at'         => date_create('now'),
                'updated_at'         => date_create('now')
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
