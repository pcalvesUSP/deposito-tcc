<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Unitermo;
use App\Models\Monografia;

class UnitermoController extends Controller
{
    public function __construct() {
        if (empty($_COOKIE['loginUSP']) || !auth()->check()) {
            return redirect('logout');
        }

        $this->autenticacao = auth()->user()->verificaIdentidade(); 

        if (!auth()->user()->can('userGraduacao') && !auth()->user()->can('admin')) {
            print "<script>alert('Você não tem acesso à esta parte do Sistema');</script>";
            return redirect('home');
        }
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($msg = null)
    {
        $unitermos = Unitermo::orderBy('unitermo')->paginate(30);
        return view("cadastro-unitermo", ["listUnitermos" => $unitermos, "mensagem" => $msg]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("form-cadastro-unitermo");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = ["unitermo" => "required"];
        $messages['required'] = "Favor informar o :attribute.";

        $request->validate($rules,$messages);
        $create = Unitermo::create($request->all());

        if ($create)
            $msg = "Unitermo ".$request->input('unitermo')." cadastrado.";
        else
            $msg = "Erro no cadastro";

        return redirect(route('unitermo.index2',['msg'=>$msg]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->edit($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $objUnitermo = Unitermo::find($id);
        return view("form-cadastro-unitermo", ['objUnitermo'=>$objUnitermo]);
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
        $rules = ["unitermo" => "required"];
        $messages['required'] = "Favor informar o :attribute.";

        $request->validate($rules,$messages);
        
        $objUnitermo = Unitermo::find($id);
        $txt_unitermo = $objUnitermo->unitermo;
        $objUnitermo->update($request->all());

        return redirect(route('unitermo.index2',['msg'=>'Unitermo alterado de '.$txt_unitermo.' para '.$objUnitermo->unitermo ]));
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $objUnitermo = Unitermo::find($id);
        $objUnitermo->delete();
        return redirect(route('unitermo.index2',['msg'=>'Unitermo '.$objUnitermo->unitermo.' excluído do Sistema.']));
    }

    /**
     * Busca dados de Unitermos
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function buscaUnitermos(Request $request) {

        $filtro = $request->input('filtro');

        $unitermos = Unitermo::where('unitermo','like',"%$filtro%")->orderBy('unitermo')->paginate(30);
        return view("cadastro-unitermo", ["listUnitermos" => $unitermos
                                         ,"mensagem"      => null
                                         ,"filtro"        => $filtro]);
    }
}
