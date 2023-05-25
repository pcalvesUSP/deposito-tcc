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
            $table->string('mostra',9)->nullable();
            $table->string('mesMostra',10)->nullable();
            $table->unsignedBigInteger('codpes')->nullable();
            $table->string('ano', 4);
            $table->timestamps();

            $table->unique(['codpes','ano']);
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
