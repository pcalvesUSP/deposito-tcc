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
            $role[] = Role::create(['name' => 'graduacao']);
            $role[] = Role::create(['name' => 'orientador']);
            $role[] = Role::create(['name' => 'aluno']);

            $permission = array();
            $permission[] = Permission::create(['name' => 'cadastro-tcc']);
            $permission[] = Permission::create(['name' => 'editar-tcc']);
            $permission[] = Permission::create(['name' => 'aprovar-tcc']);
            $permission[] = Permission::create(['name' => 'devolver-tcc']);
            $permission[] = Permission::create(['name' => 'corrigir-tcc']);
            $permission[] = Permission::create(['name' => 'cadastro-parametro']);
            $permission[] = Permission::create(['name' => 'emitir-declaracao']);
            $permission[] = Permission::create(['name' => 'emitir-relatorio']);

            Permission::create(['name' => 'userAluno']);
            Permission::create(['name' => 'userOrientador']);
            Permission::create(['name' => 'userGraduacao']);            

            foreach ($role as $key => $rol) {
                switch ($key) {
                    case 0:
                        $role[$key]->givePermissionTo($permission[1]);
                        $role[$key]->givePermissionTo($permission[5]);
                        $role[$key]->givePermissionTo($permission[6]);
                        $role[$key]->givePermissionTo($permission[7]);
                        break;
                    case 1:
                        $role[$key]->givePermissionTo($permission[2]);
                        $role[$key]->givePermissionTo($permission[3]);
                        break;
                    case 2:
                        $role[$key]->givePermissionTo($permission[0]);
                        $role[$key]->givePermissionTo($permission[4]);
                        break;
                }
            }
            echo "<p>Permissões Criadas</p>";
        } catch (Exception $e) {
            print_r($e);
        }
    }
}
