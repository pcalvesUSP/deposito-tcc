<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlunosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alunos', function (Blueprint $table) {
            $table->integer('id');
            $table->string('nome', 80);
            $table->foreignId('monografia_id')->constrained();
            $table->integer('projeto_nota')->nullable();
            $table->integer('projeto_frequencia')->nullable();
            $table->integer('tcc_nota')->nullable();
            $table->integer('tcc_frequencia')->nullable();
            $table->timestamps();

            $table->primary('id');
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
        Schema::dropIfExists('alunos');
        Schema::enableForeignKeyConstraints();
    }
}
