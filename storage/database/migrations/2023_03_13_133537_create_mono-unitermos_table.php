<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonoUnitermosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mono-unitermos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unitermo_id')->constrained('unitermos')->onDelete('cascade');;
            $table->foreignId('monografia_id')->constrained('monografias')->onDelete('cascade');;
            
            $table->timestamps();
            
            $table->unique(['unitermo_id', 'monografia_id']);
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
        Schema::dropIfExists('mono-unitermos');
        Schema::enableForeignKeyConstraints();
    }
}
