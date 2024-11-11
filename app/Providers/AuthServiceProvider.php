<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

use App\Models\Comissao;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('listar_monografia', function ($user) {
            return ($user->can('userGraduacao') || $user->can('userOrientador') || $user->can('userAvaliador') || $user->can('admin'));
        });

        Gate::define('is_orientador', function($user) {
            return ($user->hasRole('orientador') || $user->can('admin'));
        });

        Gate::define('presidente', function($user) {
            if (empty($user->codpes)) return false;
            
            $presidente = Comissao::where('papel','COORDENADOR')
                                  ->whereDate('dtInicioMandato','<=',date('Y-m-d'))
                                  ->whereDate('dtFimMandato','>=',date('Y-m-d'))
                                  ->where('codpes',$user->codpes)
                                  ->get();

            return ($presidente->isNotEmpty() || $user->can('admin'));
        });

        Gate::define('is_comissao',function($user) {
            return ($user->can('userGraduacao') || $user->can('admin'));
        });
    }
}
