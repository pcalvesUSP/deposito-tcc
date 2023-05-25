<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAreastematicasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('areastematicas', function (Blueprint $table) {
            $table->id();
            $table->string('descricao',100);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('monografias', function (Blueprint $table) {
            $table->foreignId('areastematicas_id')->nullable();
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
        
        Schema::table('monografias', function (Blueprint $table) {
            $table->dropForeign('monografias_area_tematica_foreign');

            $table->dropColumn('area_tematica');
        });

        Schema::dropIfExists('areastematicas');
        Schema::enableForeignKeyConstraints();
    }
}
