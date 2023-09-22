<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDefesasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('defesas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monografia_id')->constrained('monografias');
            $table->dateTime('dataDefesa1');
            $table->dateTime('dataDefesa2');
            $table->dateTime('dataDefesa3');
            $table->dateTime('dataEscolhida')->nullable();
            $table->unsignedBigInteger('user_data')->nullable();
            $table->timestamps();
            
            $table->unique('monografia_id');
            
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
        Schema::dropIfExists('defesas');
        Schema::enableForeignKeyConstraints();
    }
}
