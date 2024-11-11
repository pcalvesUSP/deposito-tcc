<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Banca;
use App\Models\Monografia;
use App\Models\User;

class BancaController extends Controller
{
    
    /**
     * Autenticação
     * 
     */
    public function __construct() {
        if (auth()->check() && !auth()->user()->can('userGraduacao') && !auth()->user()->can('admin')) {
            return "<script> alert('B1-Você não pode acessar essa parte do sistema.'); 
                                     window.location.assign('".route('home')."');
                    </script>";
        }
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(String $msg="", int $paginas = 30)
    {
        $listBanca = Banca::where('nome','like','%')
                          ->orderBy('ano','desc')
                          ->orderBy('nome')
                          ->paginate($paginas);
        $monografia = null;

        foreach($listBanca as $list) {
            if (!empty($list->id)) {
                $monografia[$list->id] = Monografia::find($list->monografia_id);
            }
        }

        return view('cadastro-banca', ['mensagem'=> $msg,'listBanca'=>$listBanca, 'monografia'=>$monografia]);
    }

    /**
     * Busca as bancas baseado no filtro informado
     * @param  \Illuminate\Http\Request  $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function buscaRegistroBanca(Request $request) {

        $ano = $request->input('filtro');
        $semestre = $request->input('filtro');
        if (strpos($request->input('filtro'),"-")) {
            $arr = explode("-",$request->input('filtro'));
            $semestre = $arr[0];
            $ano = $arr[1];
        }
        
        $listBanca = Banca::where('nome','like','%'.$request->input('filtro').'%')
                          ->orWhere('ano', 'like', "%$ano%")
                          ->orWhere('codpes','like','%'.$request->input('filtro').'%')
                          ->orWhere('email','like','%'.$request->input('filtro').'%')
                          ->orWhere(function ($query) use ($semestre) {
                                        $query->whereExists(function ($q) use ($semestre) {
                                                    $q->select('id')
                                                        ->from('monografias')
                                                        ->whereColumn('bancas.monografia_id','monografias.id')
                                                        ->where('semestre', $semestre); 
                                                });
                                    })   
                          ->orderBy('ano','desc')
                          ->orderBy('nome')->get();

        if ($listBanca->isEmpty()) {
            $listBanca[] = new Banca;
            $monografia[] = new Monografia;
        } else {
            foreach($listBanca as $banca) {
                $monografia[$banca->id] = Monografia::find($banca->monografia_id);
            }
        }

        return view('cadastro-banca', ['listBanca'=>$listBanca
                                      ,'monografia' => $monografia
                                      ,'buscaRegistro' => 1
                                      ,'filtro' => $request->input('filtro')]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('form-cadastro-banca');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = ['nomeBanca'   => ['required','min:3','max:100']
                 ,'emailBanca'  => ['required','email','min:5','max:100']
                 ];
        $messages = ['required' => 'Campo :attribute deve ser informado'
                    ,'min'      => 'O campo deve ter no mínimo :min caracteres'
                    ,'max'      => 'O campo deve ter no máximo :max caracteres'
                    ,'email'    => 'E-mail informado inválido'
                    ];

        $request->validate($rules, $messages);

        if (Banca::where('email',$request->input('emailBanca'))->where('ano',intval(date('Y')))->count() > 0) {
            return $this->index('Já existe um membro cadastrado com este e-mail para este ano');
        }

        $objBanca = new Banca;
        if (!empty($request->input('numUSPBanca'))) {
            if (Banca::where('codpes',$request->input('numUSPBanca'))->where('ano',intval(date('Y')))->count() > 0) {
                return $this->index('Já existe um membro cadastrado com este número USP para este ano');
            }
            $objBanca->codpes = $request->input('numUSPBanca');
        }
        $objBanca->nome = $request->input('nomeBanca');
        $objBanca->email = $request->input('emailBanca');
        $objBanca->ano =  intval(date('Y'));
        $objBanca->save();

        return redirect()->route('banca.index');
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
        $objBanca = Banca::find($id);
        return view('form-cadastro-banca',['objBanca'=>$objBanca]);
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
        //Não implementado
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $objBanca = Banca::find($id);
        $objBanca->delete();

        return $this->index("Membro da Banca ".$objBanca->nome." excluído do Sistema");
    }
}
