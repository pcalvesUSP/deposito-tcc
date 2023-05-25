<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Aluno;
use App\Models\Orientador;
use App\Models\Monografia;
use App\Models\MonoOrientadores;
use App\Models\Parametro;
use App\Models\Avaliacao;
use App\Models\Comissao;
use App\Models\Banca;

class DeclaracoesController extends Controller
{
    private $autenticacao;
    protected $parametroSistema;

    /**
     * Autenticação
     */
    public function __construct() {

        if (!auth()->check()) {
            return redirect('home');
        } else {
            $this->autenticacao = auth()->user()->verificaIdentidade();
            if (!auth()->user()->hasRole('graduacao') && !auth()->user()->can('admin')) {
                print("<script>alert('Você não tem acesso a esta parte do sistema');</script>");
                return redirect('home');
            }
        }

        $parametroSistema = Parametro::where('ano',date('Y'))->whereNull('codpes')->get();
        if ($parametroSistema->isEmpty()) {
            return redirect()->route('declaracao',["msg"=>"Sistema ainda não parametrizado."]);
        }
        $this->parametroSistema = $parametroSistema;
    }

    /**
     * Display a listing of the resource.
     * Mostra o cadastro da monografia
     *
     * @return \Illuminate\Http\Response
     */
    public function index($msg = null) {
        
        if (!empty($msg)) {
            print "<script>alert('$msg'); window.close;</script>";
        }

        $anosCadastrados = Monografia::select('ano')->distinct()->get();

        return view('declaracoes.index',['anosCadastrados' => $anosCadastrados]);

    }

    /**
     * Emissão de declaração para aluno e orientadores da apresentação do TCC
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function declaracaoAluno(Request $request) {

        $parametros = array();
        $parametros["msgErro"] = null;
        
        if (empty($request->input('nuspAluno'))) {
            $parametros["msgErro"] = "Informe o número USP do Aluno. Feche essa guia.";
            return view('declaracoes.declaracao-aluno', $parametros);
        }

        $parametros = $this->buscaCoordenador();

        $dadosAluno = Aluno::find($request->input('nuspAluno'));
        if (!isset($dadosAluno->id)) {
            $parametros["msgErro"].= "Aluno não encontrado no Sistema. Feche essa guia.";
            return view('declaracoes.declaracao-aluno', $parametros);
            //return redirect()->route('declaracao',["msg"=>"Aluno não encontrado no Sistema"]);
        }
        $dadosMonografia = Monografia::find($dadosAluno->monografia_id);
        $MonoOrientador = MonoOrientadores::where("monografia_id",$dadosAluno->monografia_id)->get();
        $dadosOrientadores = array();
        
        foreach($MonoOrientador as $monoOr) {
            $objOrientador = Orientador::withTrashed()->where('id',$monoOr->orientadores_id)->get();
            if (isset($objOrientador->first()->id)) {
                if ($monoOr->principal) {
                    $parametros["nomeOrientador"] = $objOrientador->first()->nome;
                } else {
                    $dadosOrientadores[] = $objOrientador->first();
                }
            }
        }

        if (empty($parametros["msgErro"])) {
            if (Avaliacao::where("monografia_id",$dadosMonografia->id)->where("status","APROVADO")->count() > 0 &&
                $dadosMonografia->status == "CONCLUIDO") 
            {
                $parametroSistema = Parametro::where('ano',$dadosMonografia->ano)->whereNull('codpes')->get();
            
                $parametros["nuspAluno"]        = $request->input('nuspAluno');
                $parametros["nomeAluno"]        = $dadosAluno->nome;
                $parametros["orientadores"]     = $dadosOrientadores;
                $parametros["tituloMonografia"] = $dadosMonografia->titulo;
                $parametros["mostra"]           = $parametroSistema->first()->mostra;
                $parametros["mesMostra"]        = $parametroSistema->first()->mesMostra;
                $parametros["ano"]              = $dadosMonografia->ano;
            } else {
                $parametros["msgErro"].= "A Monografia do aluno ainda não foi concluída ou está reprovada. Feche essa guia.";
                //return redirect()->route('declaracao',["msg"=>"A Monografia do aluno ainda não foi concluída ou está reprovada"]);
            }
        }
        
        return view('declaracoes.declaracao-aluno', $parametros);
    }

    /**
     * Emissão de declaração para Moderadores
     * @param int id do Membro da Banca (estes são moderadores dos trabalhos)
     * @return \Illuminate\Http\Response
     */
    public function declaracaoModeradores($id) {

        $parametros = array();
        
        $parametros = $this->buscaCoordenador();

        if (empty($parametros['msgErro'])) {
            
            $objBanca = Banca::find($id);
            $parametroSistema = Parametro::where('ano',$objBanca->ano)->whereNull('codpes')->get();
            
            $parametros["nomeModerador"] = $objBanca->nome;
            $parametros["ano"]           = $objBanca->ano;
            $parametros["mostra"]        = $parametroSistema->first()->mostra;
            $parametros["mesMostra"]     = $parametroSistema->first()->mesMostra;
        }

        return view('declaracoes.declaracao-moderador', $parametros);
    }

    /** 
     * Busca informações de Assinatura do Coordenador do Curso
     * @return Array
    */
    private function buscaCoordenador() {
        $Comissao = Comissao::where('papel','COORDENADOR')->whereNotNull('assinatura')->get();
        if ($Comissao->isEmpty()) {
            $parametros["msgErro"] = "O Coordenador do curso precisa estar cadastrado e com a assinatura incluída. Feche essa guia.";
        } else {
            $parametros["imgAssinatura"] = env('APP_URL').'/upload/assinatura/'.$Comissao->first()->assinatura;
            $parametros["nomeCoordenador"] = $Comissao->first()->nome;
        }

        return $parametros;
    }
}
