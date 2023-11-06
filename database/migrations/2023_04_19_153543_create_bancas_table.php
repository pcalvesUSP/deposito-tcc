<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBancasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bancas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monografia_id')->constrained('monografias');
            $table->unsignedBigInteger('codpes')->nullable();
            $table->string('nome',100);
            $table->string('email',100);
            $table->string('telefone',15)->nullable();
            $table->string('instituicao_vinculo',150);
            $table->enum('papel',['PRESIDENTE','MEMBRO','SUPLENTE']);
            $table->integer('ordem');
            $table->integer('ano');
            $table->string('arquivo_declaracao',150)->nullable();
            $table->timestamps();

            $table->unique(['email','monografia_id']);
            $table->index('ano');
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
        Schema::dropIfExists('bancas');
        Schema::enableForeignKeyConstraints();
    }
}
