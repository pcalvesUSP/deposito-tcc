<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('monografia_id');
            $table->enum('tipo_nota',['PROJETO','TCC']);
            $table->integer('frequencia')->nullable();
            $table->float('nota')->nullable();
            $table->timestamps();

            $table->foreign('monografia_id')->references('id')->on('monografias');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notas');
    }
}
