<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ConfigInicial extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        try{
            $role = array();
            $role[] = Role::create(['name' => 'aluno']);
            $role[] = Role::create(['name' => 'orientador']);
            $role[] = Role::create(['name' => 'avaliador']);
            $role[] = Role::create(['name' => 'graduacao']);

            $permission = array();
            $permission[] = Permission::create(['name' => 'cadastro-tcc']); //aluno
            $permission[] = Permission::create(['name' => 'editar-tcc']); //aluno, graduação
            $permission[] = Permission::create(['name' => 'corrigir-tcc']); // aluno
            $permission[] = Permission::create(['name' => 'registrar-nota']); //orientador
            $permission[] = Permission::create(['name' => 'aprovar-projeto-tcc']); //orientador
            $permission[] = Permission::create(['name' => 'aprovar-tcc']); //avaliador
            $permission[] = Permission::create(['name' => 'devolver-tcc']); // avaliador
            $permission[] = Permission::create(['name' => 'cadastro-parametro']); //graduação
            $permission[] = Permission::create(['name' => 'emitir-declaracao']); //graduação
            $permission[] = Permission::create(['name' => 'emitir-relatorio']); //graduação

            $userAluno      = Permission::create(['name' => 'userAluno']); 
            $userOrientador = Permission::create(['name' => 'userOrientador']); 
            $userGraduacao  = Permission::create(['name' => 'userGraduacao']); 
            $userAvaliador  = Permission::create(['name' => 'userAvaliador']); 

            foreach ($role as $key => $rol) {
                switch ($key) {
                    case 0: //aluno
                        $role[$key]->givePermissionTo($permission[0]);
                        $role[$key]->givePermissionTo($permission[1]);
                        $role[$key]->givePermissionTo($permission[2]);
                        $role[$key]->givePermissionTo($userAluno);
                        break;
                    case 1: //orientador
                        $role[$key]->givePermissionTo($permission[3]);
                        $role[$key]->givePermissionTo($permission[4]);
                        $role[$key]->givePermissionTo($userOrientador);
                        break;
                    case 2: //avaliador
                        $role[$key]->givePermissionTo($permission[5]);
                        $role[$key]->givePermissionTo($permission[6]);
                        $role[$key]->givePermissionTo($userAvaliador);
                        break;
                    case 2: //graduacao
                        $role[$key]->givePermissionTo($permission[1]);
                        $role[$key]->givePermissionTo($permission[7]);
                        $role[$key]->givePermissionTo($permission[8]);
                        $role[$key]->givePermissionTo($permission[9]);
                        $role[$key]->givePermissionTo($userGraduacao);
                        break;
                }
            }
            echo "<p>Permissões Criadas</p>";
        } catch (Exception $e) {
            print_r($e);
        }
    }
}
