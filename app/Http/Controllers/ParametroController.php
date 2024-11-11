<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Parametro;
use App\Models\Aluno;
use App\Rules\VerificaDatas;

class ParametroController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($msg = null, $ano = null, $semestre = null)
    {
        $acao = "novo";
        if (empty($semestre)) {
            $dataAtual = date_create('now');
            if ($dataAtual->format('n') <= 6 ) {
                $semestre = '1';
            } else {
                $semestre = '2';
            }
        }
        if (empty($ano))
            $ano = date("Y");

        $dadosSemestre = Parametro::select('semestre','ano')->distinct()->orderBy('semestre')->orderBy('ano','desc')->get();
        $dadosParam = Parametro::where("ano",$ano)->where('semestre',$semestre)->get();

        if (!$dadosParam->isEmpty()) {
            $acao = "atualizacao";
            $dadosParam->first()->dataAberturaDiscente = date_create($dadosParam->first()->dataAberturaDiscente);
            $dadosParam->first()->dataFechamentoDiscente = date_create($dadosParam->first()->dataFechamentoDiscente);
            $dadosParam->first()->dataAberturaDocente = date_create($dadosParam->first()->dataAberturaDocente);
            $dadosParam->first()->dataFechamentoDocente = date_create($dadosParam->first()->dataFechamentoDocente);
            $dadosParam->first()->dataAberturaAvaliacao = date_create($dadosParam->first()->dataAberturaAvaliacao);
            $dadosParam->first()->dataFechamentoAvaliacao = date_create($dadosParam->first()->dataFechamentoAvaliacao);
            $dadosParam->first()->dataAberturaUploadTCC = date_create($dadosParam->first()->dataAberturaUploadTCC);
            $dadosParam->first()->dataFechamentoUploadTCC = date_create($dadosParam->first()->dataFechamentoUploadTCC);
        }
        return view('cadastro-parametro', ["dadosParam" => $dadosParam, 
                                           "acao"=> $acao, 
                                           "mensagem" => $msg,
                                           "dadosSemestre" =>$dadosSemestre
                                          ]);
    }

    /**
     * Ajax de busca de dados de Parâmetros
     * @param int ano
     * @param int semestre 
     */
    public function ajaxBuscaDadosParametro($ano,$semestre) {
        $dadosParam = Parametro::where("ano",$ano)->where('semestre',$semestre)->get();

        return $dadosParam->first();

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
        $rules = ["dataInicioAlunos"       => ["required", new VerificaDatas]
                 ,"dataFinalAlunos"        => ["required", new VerificaDatas]
                 ,"dataInicioDocentes"     => ["required", new VerificaDatas]
                 ,"dataFinalDocentes"      => ["required", new VerificaDatas]
                 ,"dataAberturaAvaliacao"  => ["required", new VerificaDatas]
                 ,"dataFechamentoAvaliacao"=> ["required", new VerificaDatas]
                 ,"dataAberturaUploadTCC"  => ["required", new VerificaDatas]
                 ,"dataFechamentoUploadTCC"=> ["required", new VerificaDatas]
                 ];
        
        $mensagens = ["required" => "O campo :attribute é obrigatório"];
        
        $request->validate($rules,$mensagens);

        $dtIniAlunos    = explode("/",$request->input('dataInicioAlunos'));
        $dtFimAlunos    = explode("/",$request->input('dataFinalAlunos'));
        $dtIniDocentes  = explode("/",$request->input('dataInicioDocentes'));
        $dtFimDocentes  = explode("/",$request->input('dataFinalDocentes'));
        $dtAbertAval    = explode("/",$request->input('dataAberturaAvaliacao'));
        $dtFechaAval    = explode("/",$request->input('dataFechamentoAvaliacao'));
        $dtIniUpTCC     = explode("/",$request->input('dataAberturaUploadTCC'));
        $dtFimUpTCC     = explode("/",$request->input('dataFechamentoUploadTCC'));

        $objParametro = new Parametro;
        $objParametro->dataAberturaDiscente     = $dtIniAlunos[2]."/".$dtIniAlunos[1]."/".$dtIniAlunos[0];
        $objParametro->dataFechamentoDiscente   = $dtFimAlunos[2]."/".$dtFimAlunos[1]."/".$dtFimAlunos[0];
        $objParametro->dataAberturaDocente      = $dtIniDocentes[2]."/".$dtIniDocentes[1]."/".$dtIniDocentes[0];
        $objParametro->dataFechamentoDocente    = $dtFimDocentes[2]."/".$dtFimDocentes[1]."/".$dtFimDocentes[0];
        $objParametro->dataAberturaAvaliacao    = $dtAbertAval[2]."/".$dtAbertAval[1]."/".$dtAbertAval[0];
        $objParametro->dataFechamentoAvaliacao  = $dtFechaAval[2]."/".$dtFechaAval[1]."/".$dtFechaAval[0];
        $objParametro->dataAberturaUploadTCC    = $dtIniUpTCC[2]."/".$dtIniUpTCC[1]."/".$dtIniUpTCC[0];
        $objParametro->dataFechamentoUploadTCC  = $dtFimUpTCC[2]."/".$dtFimUpTCC[1]."/".$dtFimUpTCC[0];
        $objParametro->ano                      = date('Y');
        $objParametro->semestre                 = ((int)$dtIniAlunos[1] > 6)?2:1;
        
        if ($request->filled('numUSP'))
            $objParametro->codpes = $request->input('numUSP');

        $retQuery = $objParametro->save();

        if ($retQuery)
            if ($request->filled('numUSP')) {
                $msg = "Sistema aberto para n.º USP ".$request->input('numUSP')." no ".$objParametro->semestre."º semestre de ".date('Y');
            } else {
                $msg = "Parâmetros cadastrados para ".$objParametro->semestre."º semestre de ".date('Y');
            }
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
        $msg = null;
        $rules = ["dataInicioAlunos"       => ["required","date_format:d/m/Y"]
                 ,"dataFinalAlunos"        => ["required","date_format:d/m/Y"]
                 ,"dataInicioDocentes"     => ["required","date_format:d/m/Y"]
                 ,"dataFinalDocentes"      => ["required","date_format:d/m/Y"]
                 ,"dataAberturaAvaliacao"  => ["required","date_format:d/m/Y"]
                 ,"dataFechamentoAvaliacao"=> ["required","date_format:d/m/Y"]
                 ,"dataAberturaUploadTCC"  => ["required","date_format:d/m/Y"]
                 ,"dataFechamentoUploadTCC"=> ["required","date_format:d/m/Y"]
                 ,"semestreAno"            => ["required"]
                 ];
        
        $mensagens = ["required" => "O campo :attribute é obrigatório"];
        
        $request->validate($rules,$mensagens);

        $semAno = explode('-',$request->input('semestreAno'));

        $dtIniAlunos    = explode("/",$request->input('dataInicioAlunos'));
        $dtFimAlunos    = explode("/",$request->input('dataFinalAlunos'));
        $dtIniDocentes  = explode("/",$request->input('dataInicioDocentes'));
        $dtFimDocentes  = explode("/",$request->input('dataFinalDocentes'));
        $dtAbertAval    = explode("/",$request->input('dataAberturaAvaliacao'));
        $dtFechaAval    = explode("/",$request->input('dataFechamentoAvaliacao'));
        $dtIniUpTCC     = explode("/",$request->input('dataAberturaUploadTCC'));
        $dtFimUpTCC     = explode("/",$request->input('dataFechamentoUploadTCC'));        
        
        $objParametro = Parametro::find($id);
        $objParametro->dataAberturaDiscente     = $dtIniAlunos[2]."/".$dtIniAlunos[1]."/".$dtIniAlunos[0];
        $objParametro->dataFechamentoDiscente   = $dtFimAlunos[2]."/".$dtFimAlunos[1]."/".$dtFimAlunos[0];
        $objParametro->dataAberturaDocente      = $dtIniDocentes[2]."/".$dtIniDocentes[1]."/".$dtIniDocentes[0];
        $objParametro->dataFechamentoDocente    = $dtFimDocentes[2]."/".$dtFimDocentes[1]."/".$dtFimDocentes[0];
        $objParametro->dataAberturaAvaliacao    = $dtAbertAval[2]."/".$dtAbertAval[1]."/".$dtAbertAval[0];
        $objParametro->dataFechamentoAvaliacao  = $dtFechaAval[2]."/".$dtFechaAval[1]."/".$dtFechaAval[0];
        $objParametro->dataAberturaUploadTCC    = $dtIniUpTCC[2]."/".$dtIniUpTCC[1]."/".$dtIniUpTCC[0];
        $objParametro->dataFechamentoUploadTCC  = $dtFimUpTCC[2]."/".$dtFimUpTCC[1]."/".$dtFimUpTCC[0];
        $objParametro->ano                      = $semAno[0];
        $objParametro->semestre                 = $semAno[1];
        if ($request->filled('numUSP'))
            $objParametro->codpes = $request->input('numUSP');

        $objParametro->update();

        if ($request->filled('numUSP')) {
            $msg = "Sistema aberto para n.º USP ".$request->input('numUSP')." no ".$objParametro->semestre."º semestre de ".date('Y');
        } else {
            $msg = "Parâmetros cadastrados para ".$objParametro->semestre."º semestre de ".date('Y');
        }

        return $this->index($msg);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function modificaParametro(Request $request)
    {
        
        $msg = null;
        $rules = ["paramMonografia" => ["required"]];
        
        $mensagens = ["required" => "O campo :attribute é obrigatório"];
        
        $request->validate($rules,$mensagens);
        
        $aluno = Aluno::where('monografia_id',$request->input('monografiaId'))->get();
        $parametro = Parametro::find($request->input('paramMonografia'));
        
        $buscaParam = Parametro::where('codpes',$aluno->first()->id)->get();
        if (!$buscaParam->isEmpty()) {
            $buscaParam->first()->delete();
        }
        $objParametro = new Parametro;
        $objParametro->dataAberturaDiscente     = $parametro->dataAberturaDiscente;
        $objParametro->dataFechamentoDiscente   = $parametro->dataFechamentoDiscente;
        $objParametro->dataAberturaDocente      = $parametro->dataAberturaDocente;
        $objParametro->dataFechamentoDocente    = $parametro->dataFechamentoDocente;
        $objParametro->dataAberturaAvaliacao    = $parametro->dataAberturaAvaliacao;
        $objParametro->dataFechamentoAvaliacao  = $parametro->dataFechamentoAvaliacao;
        $objParametro->dataAberturaUploadTCC    = $parametro->dataAberturaUploadTCC;
        $objParametro->dataFechamentoUploadTCC  = $parametro->dataFechamentoUploadTCC;
        $objParametro->codpes                   = $aluno->first()->id;
        $objParametro->ano                      = $parametro->ano;
        $objParametro->semestre                 = $parametro->semestre;
        $objParametro->save();

        return redirect()->route('graduacao.edicao',['idMono'=>$request->input('monografiaId')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        $parametro = Parametro::find($id);
        if (!empty($parametro->codpes)) {
            $parametro->delete();
        }
        
        $this->index();
    }
}
