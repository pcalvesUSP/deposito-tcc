<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Parametro;
use App\Rules\VerificaDatas;

class ParametroController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($msg = null)
    {
        $acao = "novo";
        $dadosParam = Parametro::where("ano",date("Y"))->get();

        if (!$dadosParam->isEmpty()) {
            $acao = "atualizacao";
            $dadosParam->first()->dataAberturaDiscente = date_create($dadosParam->first()->dataAberturaDiscente);
            $dadosParam->first()->dataFechamentoDiscente = date_create($dadosParam->first()->dataFechamentoDiscente);
            $dadosParam->first()->dataAberturaDocente = date_create($dadosParam->first()->dataAberturaDocente);
            $dadosParam->first()->dataFechamentoDocente = date_create($dadosParam->first()->dataFechamentoDocente);
        }
        return view('cadastro-parametro', ["dadosParam" => $dadosParam, "acao"=> $acao, "mensagem" => $msg]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->index();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = ["dataInicioAlunos"    => ["required", new VerificaDatas]
                 ,"dataFinalAlunos"     => ["required", new VerificaDatas]
                 ,"dataInicioDocentes"  => ["required", new VerificaDatas]
                 ,"dataFinalDocentes"   => ["required", new VerificaDatas]
                 ,"mostra"              => "required"
                 ,"mesMostra"           => "required"
                 ];
        
        $mensagens = ["required" => "O campo :attribute é obrigatório"];
        
        $request->validate($rules,$mensagens);

        $dtIniAlunos = explode("/",$request->input('dataInicioAlunos'));
        $dtFimAlunos = explode("/",$request->input('dataFinalAlunos'));
        $dtIniDocentes = explode("/",$request->input('dataInicioDocentes'));
        $dtFimDocentes = explode("/",$request->input('dataFinalDocentes'));
        
        $objParametro = new Parametro;
        $objParametro->dataAberturaDiscente     = $dtIniAlunos[2]."/".$dtIniAlunos[1]."/".$dtIniAlunos[0];
        $objParametro->dataFechamentoDiscente   = $dtFimAlunos[2]."/".$dtFimAlunos[1]."/".$dtFimAlunos[0];
        $objParametro->dataAberturaDocente      = $dtIniDocentes[2]."/".$dtIniDocentes[1]."/".$dtIniDocentes[0];
        $objParametro->dataFechamentoDocente    = $dtFimDocentes[2]."/".$dtFimDocentes[1]."/".$dtFimDocentes[0];
        $objParametro->ano                      = date('Y');
        $objParametro->mostra                   = $request->input('mostra');
        $objParametro->mesMostra                = $request->input('mesMostra');
        $retQuery = $objParametro->save();

        if ($retQuery)
            $msg = "Parâmetros cadastrados para ".date('Y');
        else
            $msg = "Erro no cadastro dos Parametros";

        return $this->index($msg);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->index();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->index();
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
        $rules = ["dataInicioAlunos"    => ["required", new VerificaDatas]
                 ,"dataFinalAlunos"     => ["required", new VerificaDatas]
                 ,"dataInicioDocentes"  => ["required", new VerificaDatas]
                 ,"dataFinalDocentes"   => ["required", new VerificaDatas]
                 ,"mostra"              => "required"
                 ,"mesMostra"           => "required"
                 ];
        
        $mensagens = ["required" => "O campo :attribute é obrigatório"];
        
        $request->validate($rules,$mensagens);

        $dtIniAlunos = explode("/",$request->input('dataInicioAlunos'));
        $dtFimAlunos = explode("/",$request->input('dataFinalAlunos'));
        $dtIniDocentes = explode("/",$request->input('dataInicioDocentes'));
        $dtFimDocentes = explode("/",$request->input('dataFinalDocentes'));

        $objParametro = Parametro::find($id);
        $objParametro->dataAberturaDiscente     = $dtIniAlunos[2]."/".$dtIniAlunos[1]."/".$dtIniAlunos[0];
        $objParametro->dataFechamentoDiscente   = $dtFimAlunos[2]."/".$dtFimAlunos[1]."/".$dtFimAlunos[0];
        $objParametro->dataAberturaDocente      = $dtIniDocentes[2]."/".$dtIniDocentes[1]."/".$dtIniDocentes[0];
        $objParametro->dataFechamentoDocente    = $dtFimDocentes[2]."/".$dtFimDocentes[1]."/".$dtFimDocentes[0];
        $objParametro->mostra = $request->input('mostra');
        $objParametro->mesMostra = $request->input('mesMostra');
        $objParametro->update();

        return $this->index("Parâmetro atualizado.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->index();
    }
}
