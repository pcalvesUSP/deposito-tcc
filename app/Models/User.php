<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use Uspdev\Replicado\Pessoa;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    use \Spatie\Permission\Traits\HasRoles;
    use \Uspdev\SenhaunicaSocialite\Traits\HasSenhaunica;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'codpes',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Criação de Regras (nível de permissão)
     */
    static function role($role, $perm = null) {
        try {
            if (empty($perm))
                return Role::create(['name' => $role]);
            else {
                $role = Role::create(['name' => $role]); 
                $permission = Permission::create(['name' => $perm]);
                $role->givePermissionTo($permission);
            }

            return true;
        } catch (Exception $e) {
            print_r($e);
            return false;
        }
    }

    /**
     * Usado para excluir usuários em lote através do e-mail
     * @param email Endereço do e-mail
     */
    static function excluirRegistroByEmail($email) {
        return DB::table('users')->where('email', $email)->delete();
    }

    /**
     * Verifica Autenticação Senha Única USP
     */
    public function verificaIdentidade() {

        $retArray = array();
        $retArray["objUser"] = auth()->user();
        
        $user = ["name"     => $this->name
                ,"email"    => $this->email
                ,"codpes"   => $this->codpes
        ];
        $retArray["usuarioLogado"] = $user;

        if (empty(auth()->user()->codpes)) {   

            if (Orientador::where('email', auth()->user()->email)->count() > 0) {
                
                if (!auth()->user()->hasRole('orientador'))
                    auth()->user()->assignRole('orientador');
                            
                if (!auth()->user()->can('userOrientador'))
                    auth()->user()->givePermissionTo('userOrientador');
            }

        } else {
            $dadosLoginas = session("senhaunica-socialite.undo_loginas");
    
            $pessoaVinculos = Pessoa::vinculosSiglas(auth()->user()->codpes);
            //$vinculo = $pessoaVinculos[array_key_first($pessoaVinculos)];

            if (is_array($pessoaVinculos) && array_search('ALUNOGR',$pessoaVinculos) !== false)
               $vinculo = "ALUNOGR";
            elseif (is_array($pessoaVinculos) && array_search('SERVIDOR',$pessoaVinculos) !== false)
               $vinculo = "SERVIDOR";
            else
               $vinculo = "SEM VINCULO"; //$pessoaVinculos[array_key_first($pessoaVinculos)];

            switch($vinculo) {
                case "ALUNOGR":
                    if (User::where('codpes',$user["codpes"])->count() == 0) {
                        User::create($user);
                    }
                        
                    if (!auth()->user()->hasRole('aluno')) 
                        auth()->user()->assignRole('aluno');
                    
                    if (!auth()->user()->can('userAluno'))
                        auth()->user()->givePermissionTo('userAluno');

                    break;
                case "SERVIDOR":
                case "SEM VINCULO":

                    if (Orientador::where("email",auth()->user()->email)->count() > 0) {
                        if (User::where('email',$user['email'])->count() == 0) {
                            User::create($user);
                        }
                        
                        if (!auth()->user()->hasRole('orientador')) 
                            auth()->user()->assignRole('orientador');
                            
                        if (!auth()->user()->can('userOrientador'))
                            auth()->user()->givePermissionTo('userOrientador');

                    } elseif (Comissao::where("codpes",auth()->user()->codpes)->count() > 0) {
                        if (User::where('codpes',$user['codpes'])->count() == 0) {
                            User::create($user);
                        }

                        if (!auth()->user()->hasRole('avaliador')) 
                            auth()->user()->assignRole('avaliador');
                            
                        if (!auth()->user()->can('userAvaliador'))
                            auth()->user()->givePermissionTo('userAvaliador');
                        
                    } else {
                        
                        $arrayGerentes = explode(',',env('SENHAUNICA_GERENTES'));
                        $arrayAdmins = explode(',',env('SENHAUNICA_ADMINS'));

                        if (array_search(auth()->user()->codpes,$arrayGerentes) !== false ||
                            array_search(auth()->user()->codpes,$arrayAdmins) !== false ) {
                            
                            if ( User::where('codpes',auth()->user()->codpes)->count() == 0 ) {
                                User::create($user);
                            }
                            
                            if (array_search(auth()->user()->codpes,$arrayAdmins) !== false && 
                                !auth()->user()->can('admin')) 
                            {
                                auth()->user()->givePermissionTo('admin');
                            } else {
                                if (!auth()->user()->hasRole('graduacao')) 
                                    auth()->user()->assignRole('graduacao');
                                
                                if (!auth()->user()->can('userGraduacao')) 
                                    auth()->user()->givePermissionTo('userGraduacao');
                            }
                        } 
                    }
                    break;
            }

        }
        return $retArray;
    }
}
