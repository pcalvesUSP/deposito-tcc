<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\AreasTematica;
use App\Models\Monografia;

class AreasTematicasController extends Controller
{
    
    private $autenticacao;

    /**
     * Autenticação
     */
    public function __contruct() {

        if (!auth()->check()) {
            return redirect('home');
        } else {
            $this->autenticacao = auth()->user()->verificaIdentidade();
            if (!auth()->user()->hasRole('graduacao') && !auth()->user()->can('admin')) {
                print("<script>alert('Você não tem acesso a esta parte do sistema');</script>");
                return redirect(route('home'));
            }
        }
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($msg = null)
    {
        $areas_tematicas = AreasTematica::all();

        $parametros = ["listAreaTematica" => $areas_tematicas, "mensagem" => $msg];

        return view('cadastro-areatematica',$parametros);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('form-cadastro-areatematica');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = ["areaTematica" => ["required","min:3","max:100"]];
        $mensagem = ["required" => "O campo :attribute deve ser informado."
                    ,"min"      => "O campo :attribute deve ter no mínimo :min caracteres"
                    ,"max"      => "O campo :attribute dever ter no máximo :max caracteres"];

        $request->validate($rules,$mensagem);

        $areatematica = new AreasTematica;
        $areatematica->descricao = $request->input('areaTematica');
        $areatematica->save();

        return redirect(route('area_tematica.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect(route('area_tematica.index'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $objAreaTematica = AreasTematica::find($id);

        return view('form-cadastro-areatematica',["objAreaTematica" => $objAreaTematica]);

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
        $rules = ["areaTematica" => ["required","min:3","max:100"]];
        $mensagem = ["required" => "O campo :attribute deve ser informado."
                    ,"min"      => "O campo :attribute deve ter no mínimo :min caracteres"
                    ,"max"      => "O campo :attribute dever ter no máximo :max caracteres"];

        $request->validate($rules,$mensagem);

        $objAreaTematica = AreasTematica::find($id);
        $objAreaTematica->descricao = $request->input('areaTematica');
        $objAreaTematica->update();

        return redirect(route('area_tematica.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Monografia::where('areastematicas_id',$id)->count() > 0) {
            print("<script>alert('Área temática utilizada em alguma monografia não pode ser excluída.');</script>");
            return redirect(route('area_tematica.index'));
        }
        
        $objAreaTematica = AreasTematica::find($id);
        $objAreaTematica->delete();

        return redirect(route('area_tematica.index'));
    }
}
