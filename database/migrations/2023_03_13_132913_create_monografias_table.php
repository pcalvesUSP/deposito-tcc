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
            $table->enum('status', ['AGUARDANDO APROVACAO ORIENTADOR'
                                   ,'AGUARDANDO AVALIACAO'
                                   ,'AGUARDANDO CORRECAO DO PROJETO'
                                   ,'AGUARDANDO NOTA DO PROJETO'
                                   ,'AGUARDANDO ARQUIVO TCC'
                                   ,'AGUARDANDO APROVACAO BANCA'
                                   ,'AGUARDANDO VALIDACAO DA BANCA'
                                   ,'AGUARDANDO DEFESA'
                                   ,'AGUARDANDO NOTA DO TCC'
                                   ,'CONCLUIDO']);
            $table->enum('curriculo',['9012','9013']);
            $table->string('titulo',255);
            $table->text('resumo');
            $table->text('introducao');
            $table->text('objetivo');
            $table->text('material_metodo');
            $table->text('resultado_esperado');
            $table->text('aspecto_etico');
            $table->text('referencias');
            $table->boolean('publicar')->nullable();
            $table->boolean('aluno_autoriza_publicar')->nullable();
            $table->string('path_arq_tcc', 250)->nullable();
            $table->integer('ano');
            $table->integer('semestre');

            $table->index(['ano','semestre']);
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
