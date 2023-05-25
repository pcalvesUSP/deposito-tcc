<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;

class HomeController extends Controller
{
    
    private $autenticacao;

    /**
     * Controle de usuários e acessos
     */
    public function index() {  
        $user = Auth::user();
        
        if (!empty($user->id))
            $this->autenticacao = auth()->user()->verificaIdentidade();

        return view('home');
    }
}
