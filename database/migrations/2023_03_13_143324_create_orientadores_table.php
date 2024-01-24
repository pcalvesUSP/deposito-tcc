<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrientadoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orientadores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('codpes')->nullable();
            $table->string('CPF',11)->nullable();
            $table->string('nome', 80);
            $table->string('email',100);
            $table->string('instituicao_vinculo',150);
            $table->string('comprovante_vinculo',150)->nullable();
            $table->string('telefone',15)->nullable();
            $table->string('password', 255)->nullable();
            $table->boolean('externo')->default(false);
            $table->boolean('aprovado')->default(false);
            $table->text('area_atuacao')->nullable();
            $table->string('link_lattes',255)->nullable();
            $table->unsignedBigInteger('nusp_aprovador')->nullable();
            $table->timestamps();
            $table->softDeletes();

            //$table->unique('CPF'); //no sqlserver não podem haver valores nulos
            //$table->unique('codpes'); //no sqlserver não podem haver valores nulos
            $table->unique('email');
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
        Schema::dropIfExists('orientadores');
        Schema::enableForeignKeyConstraints();
    }
}
