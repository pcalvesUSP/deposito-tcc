<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComissoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comissoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('codpes');
            $table->string('nome',100);
            $table->string('email',100);
            $table->enum('papel', ['COORDENADOR', 'VICE-COORDENADOR', 'MEMBRO'])->default('MEMBRO');
            $table->date('dtInicioMandato');
            $table->date('dtFimMandato');
            $table->string('assinatura',100)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['codpes','papel']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comissoes');
    }
}
