<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonografiasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monografias', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->boolean('dupla')->default(0);
            $table->enum('status', ['EM ANDAMENTO', 'CONCLUIDO'])->default("EM ANDAMENTO");
            $table->text('titulo');
            $table->text('resumo');
            $table->boolean('publicar')->nullable();
            $table->string('template_apres', 250);
            $table->string('ano', 4);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('monografias');
    }
}
