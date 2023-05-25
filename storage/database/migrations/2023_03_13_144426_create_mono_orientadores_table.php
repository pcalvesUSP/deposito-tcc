<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonoOrientadoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mono_orientadores', function (Blueprint $table) {
            $table->id();
            $table->foreign('orientadores_id')->references('id')->on('orientadores');
            $table->foreign('monografia_id')->references('id')->on('monografias');
            $table->boolean('principal')->default(false);
            $table->timestamps();
            
            $table->unique(['orientadores_id', 'monografia_id']);
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
        Schema::dropIfExists('mono_orientadores');
        Schema::enableForeignKeyConstraints();
    }
}
