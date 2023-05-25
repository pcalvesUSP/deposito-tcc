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
            $table->string('password', 250)->nullable();
            $table->boolean('externo')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->unique('CPF');
            $table->unique('codpes');
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
