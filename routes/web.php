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
    Route::get('cadastroMonografia/{monografiaId?}/{ano?}/{msg?}', [App\Http\Controllers\AlunoController::class,'index'])->name('alunos.index');
    Route::put('corrigirMonografia/{id}', [App\Http\Controllers\MonografiaController::class,'update'])->name('alunos.corrigir');
});

Route::post('salvarMonografia',[App\Http\Controllers\MonografiaController::class,'store'])->name('salvarMonografia');
Route::post('buscaMonografia', [App\Http\Controllers\MonografiaController::class,'buscaRegistroMonografia'])->name('busca.monografia');

Route::prefix('/graduacao')->group(function() {
    Route::resource('administracao',App\Http\Controllers\ParametroController::class);
    Route::resource('area_tematica',App\Http\Controllers\AreasTematicasController::class);
    Route::resource('unitermos',App\Http\Controllers\UnitermoController::class);
    Route::resource('comissao',App\Http\Controllers\ComissaoController::class);
    Route::resource('banca', App\Http\Controllers\BancaController::class);
    Route::post('banca/filtro', [App\Http\Controllers\BancaController::class,'buscaRegistroBanca'])->name('banca.filtro');
    Route::get('unitermo/{msg}',[App\Http\Controllers\UnitermoController::class,'index'])->name('unitermo.index2');
    Route::post('unitermo/busca/',[App\Http\Controllers\UnitermoController::class,'buscaUnitermos'])->name('unitermos.busca');
    Route::get('/comissao/ajaxBuscaDadosComissao/{numUSP}',[App\Http\Controllers\ComissaoController::class,'ajaxBuscaDadosComissao'])->name('comissao.ajax');
    //Route::get('/edicao/{idMono}/{numUsp}', 'GraduacaoController@edicaoMonografia')->name('graduacao.edicao');
    Route::get('declaracao/{msg?}',[App\Http\Controllers\DeclaracoesController::class,'index'])->name('declaracao');
    Route::get('declaracao/moderador/{comissao}',[App\Http\Controllers\DeclaracoesController::class,'declaracaoModeradores'])->name('declaracao.moderador');
    Route::post('declaracao/aluno-tcc',[App\Http\Controllers\DeclaracoesController::class,'declaracaoAluno'])->name('declaracao.aluno');
    Route::delete('excluirMonografia/{id}',[App\Http\Controllers\MonografiaController::class,'destroy'])->name('graduacao.excluirMonografia');

    Route::post('relatorio/aluno-orientador',[App\Http\Controllers\RelatoriosController::class,'alunoOrientador'])->name('relatorio.aluno-orientador');
    Route::post('relatorio/publicacao',[App\Http\Controllers\RelatoriosController::class,'publicaBDTA'])->name('relatorio.publicaBDTA');
    Route::post('relatorio/emissao-certificado',[App\Http\Controllers\RelatoriosController::class,'emissaoCertificado'])->name('relatorio.emissaoCertificado');

    Route::get('exportacao/rel-aluno-orientador/{ano}',[App\Http\Controllers\RelatoriosController::class,'exportacaoAlunoOrientador'])->name('exportacao.rel-aluno-orientador');
    Route::get('exportacao/rel-publica-bdta/{ano}',[App\Http\Controllers\RelatoriosController::class,'exportacaoPublicacao'])->name('exportacao.rel-publica-bdta');
    Route::get('exportacao/rel-emissao-cert/{ano}',[App\Http\Controllers\RelatoriosController::class,'exportacaoEmissaoCert'])->name('exportacao.rel-emissao-cert');
});

Route::prefix('orientador')->group(function() {
    Route::get('/', [App\Http\Controllers\OrientadorController::class,'index'])->name('orientador.index');
    Route::get('/edicao/{idMono}', [App\Http\Controllers\OrientadorController::class,'show'])->name('orientador.edicao');
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
});

Route::get('notificacao/{msg}',function($msg) {
    $textoMensagem = "A monografia título **TESTE TITULO** tem uma correção a ser realizada.
    Orientador responsável: **TESTE RESP**.                                                 
    Parecer: *teste*                                                                          
    Clique no botão abaixo para acessar o sistema e efetuar a correção.";
    
    
    return new App\Mail\NotificacaoAluno($textoMensagem,$msg);
});

Route::get('/configuracao-inicial',[ConfigInicial::class,'index'])->name('config.index');
