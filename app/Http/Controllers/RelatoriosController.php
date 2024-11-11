<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\Monografia;
use App\Models\MonoOrientadores;
use App\Models\Orientador;
use App\Models\Parametro;

use Uspdev\Replicado\Pessoa;

use Barryvdh\DomPDF\Facade\Pdf;

use App\Exports\MonografiasExport;
use App\Exports\MonografiasPublicacaoExport;
use App\Exports\MonografiasCertificadoExport;
use App\Exports\MonografiasBancasExport;
use App\Exports\MonografiaNotasExport;
use App\Exports\MonografiasTemasExport;

class RelatoriosController extends Controller
{
    public function alunoOrientador(Request $request) {

        $rule['ano_aluno_orientador']      = 'required';
        $rule['semestre_aluno_orientador'] = 'required';

        $request->validate($rule,['required' => "Ano deve ser informado"]);

        $monografias = Monografia::with(['alunos','orientadores','avaliacoes'])
                                 ->where('ano',$request->input('ano_aluno_orientador'))
                                 ->where('semestre',$request->input('semestre_aluno_orientador'))
                                 ->paginate(30);
        
        return view('relatorios.aluno-orientador',['listMonografias' => $monografias
                                                  ,'ano' => $request->input('ano_aluno_orientador')
                                                  ,'semestre' => $request->input('semestre_aluno_orientador')]);
    }

    public function exportacaoAlunoOrientador($ano) {
        return Excel::download(new MonografiasExport, 'aluno-orientador-'.$ano.'.xlsx');
    }

    public function publicaBDTA(Request $request) {
        $rule['ano_publicacao']      = 'required';
        $rule['semestre_publicacao'] = 'required';

        $message['required'] = "O :attribute deve ser informado";

        $request->validate($rule,$message);

        $monografias = Monografia::with(['alunos','orientadores'])
                                 ->where('ano',$request->input('ano_publicacao'))
                                 ->where('semestre',$request->input('semestre_publicacao'))
                                 ->where('status','CONCLUIDO')
                                 ->paginate(30);

        return view('relatorios.publica-bdta',['listMonografias' => $monografias
                                              ,'ano' => $request->input('ano_publicacao')
                                              ,'semestre' => $request->input('semestre_publicacao')
                                              ]);
    }

    public function exportacaoPublicacao($ano) {
        return Excel::download(new MonografiasPublicacaoExport, 'publica-bdta-'.$ano.'.xlsx');
    }

    public function emissaoCertificado(Request $request) {
        
        $rule['ano_emissao'] = 'required';
        $rule['semestre_emissao'] = 'required';

        $message['required'] = "O :attribute deve ser informado";

        $request->validate($rule,$message);

        $monografias = Monografia::with(['alunos','orientadores','defesas'])
                                 ->where('ano',$request->input('ano_emissao'))
                                 ->where('semestre',$request->input('semestre_emissao'))
                                 ->where('status','CONCLUIDO')
                                 ->paginate(30);

        return view('relatorios.emissao-certificado',['listMonografias' => $monografias
                                                     ,'ano' => $request->input('ano_emissao')
                                                     ,'semestre' => $request->input('semestre_emissao')
                                                     ]);
    }

    public function exportacaoEmissaoCert($ano) {
        return Excel::download(new MonografiasCertificadoExport, 'emissao-certificado-'.$ano.'.xlsx');        
    }

    /**
     * Relatório de Bancas sugeridas
     * @param int semestre
     * @param int ano
     * @param int monografia_id [OPCIONAL]
     */
    public function bancasSugeridas(Request $request) {
        $monografias = Monografia::with(['bancas','alunos','orientadores','defesas'])
                                 ->where('ano',$request->input('ano_banca'))
                                 ->where('semestre',$request->input('semestre_banca'))
                                 ->get();

        if ($monografias->isEmpty()) {
            return '<script>alert("Não existem registros para este ano/semestre."); window.location="'.route('declaracao').'";</script>';
        }

        $emailAluno = Pessoa::emailusp($monografias->first()->alunos->first()->id);

        return view('relatorios.bancas-sugeridas',['listMonografia' =>$monografias
                                                  ,'emailAluno'     =>$emailAluno
                                                  ,'semestre'       => $request->input('semestre_banca')
                                                  ,'ano'            => $request->input('ano_banca')
                                                  ]);
        
    }

    public function exportacaoBancaSugerida($ano,$semestre) {
        return Excel::download(new MonografiasBancasExport, 'banca-sugerida-'.$ano.'-'.$semestre.'.xlsx');        
    }

    /**
     * Relatório de Notas e TCC Final
     */
    function notasTccFinal(Request $request) {
        
        $monografia = Monografia::with(['notas','alunos'])
                                ->where('ano', $request->input('ano_tcc'))
                                ->where('semestre',$request->input('semestre_tcc'))
                                ->get();

        return view('relatorios.notas-projetos-tcc',['listMonografia' => $monografia
                                                    ,'semestre'       => $request->input('semestre_tcc')
                                                    ,'ano'            => $request->input('ano_tcc')
                                                    ]);
    }

    public function exportacaoNotasTcc($ano,$semestre) {
        return Excel::download(new MonografiaNotasExport, 'notas-'.$ano.'-'.$semestre.'.xlsx');        
    }

    /** 
     * Relatório Temas defendidos
     */
    public function temasDefendido(Request $request) {

        $monografia = Monografia::with(['alunos','orientadores'])
                                ->where('ano', $request->input('ano_tema'))
                                ->where('semestre', $request->input('semestre_tema'))
                                ->get();

        return view('relatorios.temas-defendidos-tcc',['listMonografia' => $monografia
                                                     ,'semestre'       => $request->input('semestre_tema')
                                                     ,'ano'            => $request->input('ano_tema')
                                                     ]);
    }

    public function exportacaoTemas($ano,$semestre) {
        return Excel::download(new MonografiasTemasExport, 'temas-'.$ano.'-'.$semestre.'.xlsx');        
    }

    /**
     * Relatório final
     */
    public function relatorioFinal(Request $request) {

        $rule['nuspAluno'] = 'required';
        $message['required'] = 'N.º USP do aluno deve ser informado';

        $request->validate($rule,$message);

        $monografia = Monografia::with(['alunos','orientadores','defesas','bancas','notas'])
                                ->whereRelation('alunos','id', $request->input('nuspAluno'))
                                ->whereRelation('notas','tipo_nota','TCC')
                                ->whereRelation('bancas','arquivo_declaracao','<>', '""')
                                ->get();

        if ($monografia->isEmpty()) {
            return "<script>alert('Erro na busca de dados'); window.locatio.assign('".route('declaracao')."'); ";
        }

        $monografia = $monografia->first();

        $dataDefesa = date_create($monografia->defesas->first()->dataEscolhida);
        if ($monografia->notas->first()->nota) {
            $resultado = "APROVADO";
        } else {
            $resultado = "REPROVADO";
        }

        $paramPdfRel =  ['nome_aluno'       => $monografia->alunos->first()->nome
                        ,'nusp_aluno'       => $monografia->alunos->first()->id
                        ,'nome_orientador'  => $monografia->orientadores->first()->nome
                        ,'nusp_orientador'  => $monografia->orientadores->first()->codpes
                        ,'titulo_monografia'=> $monografia->titulo
                        ,'data_defesa'      => $dataDefesa->format('d/m/Y')
                        ,'hora_defesa'      => $dataDefesa->format('H:i')
                        ,'local'            => 'SALA GOOGLE MEET'
                        ,'media'            => $monografia->notas->first()->nota
                        ,'frequencia'       => $monografia->notas->first()->frequencia."%"
                        ,'resultado'        => $resultado
                        ,'publica'          => $request->input('publicar')];

        foreach($monografia->bancas as $key=>$objBanca) {
            if ($key == 0) {
                if (!empty($objBanca->codpes)) {
                    $paramPdfRel['banca1'] = $objBanca->codpes." ".$objBanca->nome;
                } else {
                    $paramPdfRel['banca1'] = $objBanca->nome."(".$objBanca->instituicao_vinculo.")";
                }
            }
            if ($key == 1) {
                if (!empty($objBanca->codpes)) {
                    $paramPdfRel['banca2'] = $objBanca->codpes." ".$objBanca->nome;
                } else {
                    $paramPdfRel['banca2'] = $objBanca->nome."(".$objBanca->instituicao_vinculo.")";
                }
            }
            if ($key == 2) {
                if (!empty($objBanca->codpes)) {
                    $paramPdfRel['banca3'] = $objBanca->codpes." ".$objBanca->nome;
                } else {
                    $paramPdfRel['banca3'] = $objBanca->nome."(".$objBanca->instituicao_vinculo.")";
                }
                
            }
        }

        $pdf = Pdf::loadView('templates_pdf.relatorio-defesa-tcc', $paramPdfRel);
        return $pdf->stream();
        //return $pdf->download('relatorio_final_'.$monografia->alunos->first()->id.'.pdf');
    }
}
