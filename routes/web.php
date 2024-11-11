<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConfigInicial;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/',[App\Http\Controllers\HomeController::class,'index']);
Route::get('/home',[App\Http\Controllers\HomeController::class,'index'])->name('home');

Route::get('loginExterno',[App\Http\Controllers\LoginExtController::class,'index'])->name('login.externo');
Route::post('loginExterno',[App\Http\Controllers\LoginExtController::class,'autenticar'])->name('login.externo');

Route::prefix('alunos')->group(function() {
    Route::get('disciplinaAluno/{nusp}',[App\Http\Controllers\AlunoController::class,'disciplinas']);
    Route::get('cadastroMonografia/{monografiaId?}/{ano?}/{msg?}', [App\Http\Controllers\AlunoController::class,'index'])->name('alunos.index');
    Route::put('corrigirMonografia/{id}', [App\Http\Controllers\MonografiaController::class,'update'])->name('alunos.corrigir');
    Route::post('salvarBanca',[App\Http\Controllers\AlunoController::class,'salvarBanca'])->name('aluno.salvarBanca');
    Route::put('corrigirBanca',[App\Http\Controllers\AlunoController::class,'corrigirBanca'])->name('aluno.corrigirBanca');
});

Route::post('salvarMonografia',[App\Http\Controllers\MonografiaController::class,'store'])->name('salvarMonografia');
Route::post('buscaMonografia', [App\Http\Controllers\MonografiaController::class,'buscaRegistroMonografia'])->name('busca.monografia');
Route::get('buscaMonografia/{orientador?}/{ano?}/{status?}/{filtro?}',[App\Http\Controllers\MonografiaController::class,'listMonografia'])->name('busca.monografia_filtro');
Route::get('comissao/orientador', [App\Http\Controllers\OrientadorController::class,'index'])->name('comissao.orientador');
Route::get('comissao/lista_monografia/',[App\Http\Controllers\OrientadorController::class,'listarMonografias'])->name('comissao.lista_monografia');
    
Route::prefix('/graduacao')->group(function() {
    Route::resource('administracao',App\Http\Controllers\ParametroController::class);
    Route::resource('area_tematica',App\Http\Controllers\AreasTematicasController::class);
    Route::resource('unitermos',App\Http\Controllers\UnitermoController::class);
    Route::resource('comissao',App\Http\Controllers\ComissaoController::class);
    Route::resource('banca', App\Http\Controllers\BancaController::class);
    
    Route::get('/lista_monografia/',[App\Http\Controllers\OrientadorController::class,'listarMonografias'])->name('graduacao.lista_monografia');
    Route::get('/edicao/{idMono}/{msg?}', [App\Http\Controllers\OrientadorController::class,'show'])->name('graduacao.edicao');
    
    Route::put('indicaParecerista',[App\Http\Controllers\MonografiaController::class,'indicaParecerista'])->name('indicaParecerista');
    Route::post('banca/filtro', [App\Http\Controllers\BancaController::class,'buscaRegistroBanca'])->name('banca.filtro');
    Route::get('unitermo/{msg}',[App\Http\Controllers\UnitermoController::class,'index'])->name('unitermo.index2');
    Route::post('unitermo/busca/',[App\Http\Controllers\UnitermoController::class,'buscaUnitermos'])->name('unitermos.busca');
    Route::get('/comissao/ajaxBuscaDadosComissao/{numUSP}',[App\Http\Controllers\ComissaoController::class,'ajaxBuscaDadosComissao'])->name('comissao.ajax');
    //Route::get('/edicao/{idMono}/{numUsp}', 'GraduacaoController@edicaoMonografia')->name('graduacao.edicao');
    Route::get('declaracao/{msg?}',[App\Http\Controllers\DeclaracoesController::class,'index'])->name('declaracao');
    Route::get('declaracao/moderador/{comissao}',[App\Http\Controllers\DeclaracoesController::class,'declaracaoModeradores'])->name('declaracao.moderador');
    Route::post('declaracao/aluno-tcc',[App\Http\Controllers\DeclaracoesController::class,'declaracaoAluno'])->name('declaracao.aluno');
    Route::delete('excluirMonografia/{id}',[App\Http\Controllers\MonografiaController::class,'destroy'])->name('graduacao.excluirMonografia');
    Route::post('validaBanca',[App\Http\Controllers\MonografiaController::class,'validaDefesa'])->name('graduacao.validaBanca');
    Route::get('aprovarCadastro/{id}/{aprovacao}',[App\Http\Controllers\OrientadorController::class,'aprovarCadastro'])->name('graduacao.aprova.cadastro');
    Route::post('validaDefesa',[App\Http\Controllers\ComissaoController::class,'validaDefesa'])->name('graduacao.validaDefesa');
    Route::put('alteraDataDefesa',[App\Http\Controllers\MonografiaController::class,'alteraDataDefesa'])->name('graduacao.alteradata');
    Route::post('relatorio/aluno-orientador',[App\Http\Controllers\RelatoriosController::class,'alunoOrientador'])->name('relatorio.aluno-orientador');
    Route::post('relatorio/publicacao',[App\Http\Controllers\RelatoriosController::class,'publicaBDTA'])->name('relatorio.publicaBDTA');
    Route::post('relatorio/emissao-certificado',[App\Http\Controllers\RelatoriosController::class,'emissaoCertificado'])->name('relatorio.emissaoCertificado');
    Route::post('relatorio/sugestao-banca',[App\Http\Controllers\RelatoriosController::class,'bancasSugeridas'])->name('relatorio.bancasSugeridas');
    Route::post('relatorio/notas',[App\Http\Controllers\RelatoriosController::class,'notasTccFinal'])->name('relatorio.notas-projeto-tcc');
    Route::post('relatorio/temas-defendidos',[App\Http\Controllers\RelatoriosController::class,'temasDefendido'])->name('relatorio.tema-defendido');
    Route::post('relatorio-final',[App\Http\Controllers\RelatoriosController::class,'relatorioFinal'])->name('relatorio.final');
    
    Route::post('parametro',[App\Http\Controllers\ParametroController::class,'modificaParametro'])->name('graduacao.parametro');
    Route::get('buscaDadosParametros/{ano}/{semestre}',[App\Http\Controllers\ParametroController::class,'ajaxBuscaDadosParametro'])->name('ajaxBuscaParametro');
    
    Route::get('exportacao/rel-aluno-orientador/{ano}',[App\Http\Controllers\RelatoriosController::class,'exportacaoAlunoOrientador'])->name('exportacao.rel-aluno-orientador');
    Route::get('exportacao/rel-publica-bdta/{ano}',[App\Http\Controllers\RelatoriosController::class,'exportacaoPublicacao'])->name('exportacao.rel-publica-bdta');
    Route::get('exportacao/rel-emissao-cert/{ano}',[App\Http\Controllers\RelatoriosController::class,'exportacaoEmissaoCert'])->name('exportacao.rel-emissao-cert');
    Route::get('exportacao/rel-banca-sugerada/{ano}/{semestre}',[App\Http\Controllers\RelatoriosController::class,'exportacaoBancaSugerida'])->name('exportacao.rel-banca-sugerida');
    Route::get('exportacao/rel-notas/{ano}/{semestre}',[App\Http\Controllers\RelatoriosController::class,'exportacaoNotasTcc'])->name('exportacao.rel-notas-tcc');
    Route::get('exportacao/rel-tema/{ano}/{semestre}',[App\Http\Controllers\RelatoriosController::class,'exportacaoTemas'])->name('exportacao.rel-temas-tcc');
});

Route::prefix('orientador')->group(function() {
    Route::get('/', [App\Http\Controllers\OrientadorController::class,'index'])->name('orientador.index');
    Route::get('/edicao/{idMono}/{msg?}', [App\Http\Controllers\OrientadorController::class,'show'])->name('orientador.edicao');
    Route::get('/lista_monografia/',[App\Http\Controllers\OrientadorController::class,'listarMonografias'])->name('orientador.lista_monografia');
    Route::get('editar_orientador/{id}',[App\Http\Controllers\OrientadorController::class,'edit'])->name('orientador.edit');
    Route::put('alterar_orientador/{id}',[App\Http\Controllers\OrientadorController::class,'update'])->name('orientador.update');
    Route::delete('remover_orientador/{id}',[App\Http\Controllers\OrientadorController::class,'destroy'])->name('orientador.destroy');
    Route::get('/novoCadastro/',[App\Http\Controllers\OrientadorController::class,'create'])->name('orientador.novo-cadastro');
    Route::post('/salvardados/',[App\Http\Controllers\OrientadorController::class,'store'])->name('orientador.salvardados');
    Route::get('/avaliacao/{idMonografia}/{acao}',[App\Http\Controllers\OrientadorController::class,'avaliacao'])->name('orientador.avaliacao');
    Route::post('/avaliacao/',[App\Http\Controllers\OrientadorController::class,'salvarAvaliacao'])->name('orientador.salvarParecer');
    Route::get('/ajaxBuscaDadosOrientador/{idOrient}',[App\Http\Controllers\OrientadorController::class,'ajaxBuscaDadosOrientador'])->name('orientador.ajaxbuscaorientador');
    Route::post('buscaOrientador',[App\Http\Controllers\OrientadorController::class,'getOrientadorByFiltro'])->name('busca.orientador');
    Route::put('aprovaProjeto',[App\Http\Controllers\MonografiaController::class,'aprovaProjeto'])->name('orientador.aprovacao');
    Route::post('/informaNotas',[App\Http\Controllers\OrientadorController::class,'informaNota'])->name('orientador.notas');
    Route::post('aprovacao-banca',[App\Http\Controllers\OrientadorController::class, 'aprovaBanca'])->name('orientador.aprova.banca');
});

Route::prefix('comissao')->group(function() {
    Route::get('/avaliacao/{idMonografia}/{acao}',[App\Http\Controllers\OrientadorController::class,'avaliacao'])->name('comissao.avaliacao');
    Route::post('/avaliacao/',[App\Http\Controllers\OrientadorController::class,'salvarAvaliacao'])->name('comissao.salvarParecer');
});

//Daqui para baixo desativar em produção
Route::get('notificacao/{msg}',function($nome) {
    $textoMensagem = "A monografia título **TESTE TITULO** tem uma correção a ser realizada.
    Orientador responsável: **TESTE RESP**.                                                 
    Parecer: *teste*                                                                          
    Clique no botão abaixo para acessar o sistema e efetuar a correção.";
    
    
    return new App\Mail\NotificacaoAluno($textoMensagem,$nome,"TESTE DE E-MAIL");
});

Route::get('declaracao-teste', function() {
    return view('templates_pdf.declaracao-defesa-banca',['nome_aluno' => 'Fulano Aluno da Silva'
                                                        ,'nome_membro' => 'Fulano Membro da Silva'
                                                        ,'papel_membro' => 'PRESIDENTE'
                                                        ,'titulo_trabalho' => 'Titulo TESTE'
                                                        ,'data_defesa' => 'dd/mm/aaaa'
                                                        ,'hora_defesa' => '00:00'
                                                        ,'pathImageAssinatura' => "/upload/assinatura/assinatura_priscila.png"
                                                        ,'nomeCoordenador' => 'Nome de Coordenador']);
});

Route::get('relatorio-teste', function() {
    return view('templates_pdf.relatorio-defesa-tcc',['nome_aluno' => 'Fulano Aluno da Silva'
                                                    ,'nusp_aluno' => '00000000'
                                                    ,'nome_orientador' => 'FULANO Orientador'
                                                    ,'nusp_orientador' => '000000'
                                                    ,'titulo_monografia' => 'Projeto de Remédios para Emagrecer'
                                                    ,'data_defesa' => 'dd/mm/aaaa'
                                                    ,'hora_defesa' => '00:00'
                                                    ,'local' => 'SALA GOOGLE MEET'
                                                    ,'media' => '10'
                                                    ,'frequencia' => '100%'
                                                    ,'resultado' => 'APROVADO'
                                                    ,'banca1' => 'Pedro'
                                                    ,'banca2' => 'Paulo'
                                                    ,'banca3' => 'João'
                                                    ,'publica' => 1]);
});

Route::get('/configuracao-inicial',[ConfigInicial::class,'index'])->name('config.index');
Route::get('/ajusteCadastro',[App\Http\Controllers\LoginExtController::class,'ajusteCadastroOrientadorExterno']);
