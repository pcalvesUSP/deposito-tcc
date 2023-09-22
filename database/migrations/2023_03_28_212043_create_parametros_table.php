<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParametrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parametros', function (Blueprint $table) {
            $table->id();
            $table->date('dataAberturaDiscente');
            $table->date('dataFechamentoDiscente');
            $table->date('dataAberturaDocente');
            $table->date('dataFechamentoDocente');
            $table->date('dataAberturaAvaliacao');
            $table->date('dataFechamentoAvaliacao');
            $table->date('dataAberturaUploadTCC');
            $table->date('dataFechamentoUploadTCC');
            $table->string('disciplinas',255)->nullable();
            $table->unsignedBigInteger('codpes')->nullable();
            $table->integer('ano');
            $table->integer('semestre');
            $table->timestamps();

            $table->unique(['codpes','ano', 'semestre']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parametros');
    }
}
