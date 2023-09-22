<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAvaliacaoTable extends Migration
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
            $table->foreignId('monografia_id')->constrained('monografias');
            $table->foreignId('comissoes_id')->constrained('comissoes');
            $table->dateTime('dataAvaliacao');
            $table->enum('status',['AGUARDANDO','DEVOLVIDO','CORRIGIDO','APROVADO']);
            $table->text('parecer')->nullable();
            $table->timestamps();

            $table->unique(['monografia_id', 'comissoes_id', 'dataAvaliacao']);
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
