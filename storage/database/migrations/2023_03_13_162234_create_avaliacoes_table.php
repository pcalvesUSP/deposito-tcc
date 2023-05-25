<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAvaliacoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('avaliacoes', function (Blueprint $table) {

            $table->id();
            $table->unsignedBigInteger('orientadores_id');
            $table->unsignedBigInteger('monografia_id');
            $table->dateTime('dataAvaliacao');
            $table->enum('status',['DEVOLVIDO','CORRIGIDO','APROVADO','REPROVADO']);
            $table->text('parecer')->nullable();
            $table->timestamps();

            $table->foreign(['orientadores_id','monografia_id'])
                  ->references(['orientadores_id','monografia_id'])
                  ->on('mono_orientadores')->constrained();

            $table->unique(['orientadores_id', 'monografia_id', 'dataAvaliacao']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('avaliacoes');

        Schema::enableForeignKeyConstraints();
    }
}
