<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SeedPermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $role[] = Role::firstOrCreate(['name' => 'aluno']);
        $role[] = Role::firstOrCreate(['name' => 'orientador']);
        $role[] = Role::firstOrCreate(['name' => 'avaliador']);
        $role[] = Role::firstOrCreate(['name' => 'graduacao']);
        
        Permission::firstOrCreate(['name' => 'cadastro-tcc']); //aluno
        Permission::firstOrCreate(['name' => 'editar-tcc']); //aluno, graduação
        Permission::firstOrCreate(['name' => 'corrigir-tcc']); // aluno
        Permission::firstOrCreate(['name' => 'registrar-nota']); //orientador
        Permission::firstOrCreate(['name' => 'aprovar-projeto-tcc']); //orientador
        Permission::firstOrCreate(['name' => 'aprovar-tcc']); //avaliador
        Permission::firstOrCreate(['name' => 'devolver-tcc']); // avaliador
        Permission::firstOrCreate(['name' => 'cadastro-parametro']); //graduação
        Permission::firstOrCreate(['name' => 'emitir-declaracao']); //graduação
        Permission::firstOrCreate(['name' => 'emitir-relatorio']);
        Permission::firstOrCreate(['name' => 'userAluno']); 
        Permission::firstOrCreate(['name' => 'userOrientador']); 
        Permission::firstOrCreate(['name' => 'userGraduacao']); 
        Permission::firstOrCreate(['name' => 'userAvaliador']); 
        Permission::firstOrCreate(['name' => 'userAdmin']); 
        Permission::firstOrCreate(['name' => 'userComissao']);

        $role[0]->givePermissionTo(['cadastro-tcc', 'editar-tcc', 'corrigir-tcc','userAluno']);
        $role[1]->givePermissionTo(['registrar-nota', 'aprovar-projeto-tcc','userOrientador']);
        $role[2]->givePermissionTo(['aprovar-tcc','devolver-tcc','userAvaliador']);
        $role[3]->givePermissionTo(['editar-tcc','cadastro-parametro','emitir-declaracao','emitir-relatorio','userGraduacao']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
