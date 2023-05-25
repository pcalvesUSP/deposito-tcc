<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Aluno;
use App\Models\Parametro;

class AlunoController extends Controller
{
    
    public function __construct() {
        if (!auth()->check()) {
            return redirect('home');
        } else {
            $this->autenticacao = auth()->user()->verificaIdentidade();
            if (!auth()->user()->hasRole('aluno') && !auth()->user()->can('admin')) {
                print("<script>alert('Você não tem acesso a esta parte do sistema');</script>");
                return redirect('home');
            }
        }
            
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($monografia_id = 0, $ano = null, $mensagem = null)
    {
        $parametroSistema = Parametro::where('ano',date('Y'))->whereNull('codpes')->get();
        if ($parametroSistema->isEmpty() && auth()->user()->hasRole('aluno')) {
            print("<script>alert('Sistema ainda não parametrizado. Entre em contato com a Graduação.');</script>");
            return redirect('home');
        }
        
        $monografia = new MonografiaController();
        return $monografia->index($monografia_id, $ano, $mensagem);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return $this->index();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->index();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->index();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return $this->index();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return $this->index();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->index();
    }
    
}
