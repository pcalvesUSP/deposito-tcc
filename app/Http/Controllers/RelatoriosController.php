<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\Monografia;
use App\Models\MonoOrientadores;
use App\Models\Orientador;
use App\Models\Parametro;

use App\Exports\MonografiasExport;
use App\Exports\MonografiasPublicacaoExport;
use App\Exports\MonografiasCertificadoExport;

class RelatoriosController extends Controller
{
    public function alunoOrientador(Request $request) {

        $rule['ano_aluno_orientador'] = 'required';

        $request->validate($rule,['required' => "Ano deve ser informado"]);

        $monografias = Monografia::with(['alunos','orientadores','avaliacoes'])
                                 ->where('ano',$request->input('ano_aluno_orientador'))
                                 ->paginate(30);
        
        return view('relatorios.aluno-orientador',['listMonografias' => $monografias
                                                  ,'ano' => $request->input('ano_aluno_orientador')]);
    }

    public function exportacaoAlunoOrientador($ano) {
        return Excel::download(new MonografiasExport, 'aluno-orientador-'.$ano.'.xlsx');
    }

    public function publicaBDTA(Request $request) {
        $rule['ano_publicacao'] = 'required';

        $request->validate($rule,['required' => "Ano deve ser informado"]);

        $monografias = Monografia::with(['alunos','orientadores','avaliacoes'])
                                 ->where('ano',$request->input('ano_publicacao'))
                                 ->where('status','CONCLUIDO')
                                 ->whereRelation('avaliacoes', 'status', 'APROVADO')
                                 ->paginate(30);

        return view('relatorios.publica-bdta',['listMonografias' => $monografias
                                              ,'ano' => $request->input('ano_publicacao')
                                              ]);
    }

    public function exportacaoPublicacao($ano) {
        return Excel::download(new MonografiasPublicacaoExport, 'publica-bdta-'.$ano.'.xlsx');
    }

    public function emissaoCertificado(Request $request) {
        
        $parametrosMostra = Parametro::where("ano",$request->input('ano_emissao'))->whereNull('codpes')->get();
        if ($parametrosMostra->isEmpty()) {
            print "<script>alert('O Sistema não está parametrizado para o ano informado.');</script>";
            return redirect(route('declaracao'));
        }
        
        $rule['ano_emissao'] = 'required';
        $request->validate($rule,['required' => "Ano deve ser informado"]);

        $monografias = Monografia::with(['alunos','orientadores','avaliacoes'])
                                 ->where('ano',$request->input('ano_emissao'))
                                 ->where('status','CONCLUIDO')
                                 ->whereRelation('avaliacoes', 'status', 'APROVADO')
                                 ->paginate(30);

        $mostra = array();
        $mostra['numero'] = $parametrosMostra->first()->mostra;
        $mostra['mes'] = $parametrosMostra->first()->mesMostra;

        return view('relatorios.emissao-certificado',['listMonografias' => $monografias
                                                     ,'ano' => $request->input('ano_emissao')
                                                     ,'mostra' => (object)$mostra
                                                     ]);
    }

    public function exportacaoEmissaoCert($ano) {
        return Excel::download(new MonografiasCertificadoExport, 'emissao-certificado-'.$ano.'.xlsx');        
    }
}
