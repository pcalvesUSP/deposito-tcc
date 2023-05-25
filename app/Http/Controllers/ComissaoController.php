<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

use App\Models\Comissao;

use Uspdev\Replicado\Pessoa;

class ComissaoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(String $msg = null)
    {
        $listComissao = Comissao::all();
        return view('cadastro-comissao', ['mensagem' => $msg, 'listComissao' => $listComissao]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('form-cadastro-comissao');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rule = ["nuspComissao" => ["required","numeric"]
                ,"nomeComissao" => ["required","min:3","max:100"]
                ,"emailComissao"=> ["required","email","max:100"]
                ,"papelComissao"=> ["required"]
                ,"assinatura"   => ["image","file"]
                ];

        $messages = ["required"         => "O campo :attribute deve ser preenchido"
                    ,"min"              => "O campo :attribute deve ter no mínimo :min caracteres"
                    ,"max"              => "O campo :attribute deve ter no máximo :max caracteres"
                    ,"email"            => "O campo :attribute deve ser um e-mail válido"
                    ,"assinatura.image" => "O arquivo de assinatura deve ser do tipo JPG ou PNG"
                    ,"assinatura.file"  => "O upload do arquivo de assinatura falhou"
                    ];

        $request->validate($rule,$messages);

        $upload = false;
        if ($request->hasFile('assinatura') && $request->file('assinatura')->isValid()) {
            $arquivo = $request->file('assinatura');
                
            $arquivo->move(public_path('upload/assinatura'),$arquivo->getClientOriginalName());
            $upload = true;                
        }       
        
        $objComissao = new Comissao;
        $objComissao->codpes = $request->input('nuspComissao');
        $objComissao->nome = $request->input('nomeComissao');
        $objComissao->email = $request->input('emailComissao');
        $objComissao->papel = $request->input('papelComissao');
        $objComissao->assinatura = ($upload)?$arquivo->getClientOriginalName():null;
        $objComissao->save();

        return redirect()->route('comissao.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $objComissao = Comissao::find($id);
        return view('form-cadastro-comissao',['objComissao'=>$objComissao]);
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
        $rule = ["nomeComissao" => ["required","min:3","max:100"]
                ,"emailComissao"=> ["required","email","max:100"]
                ,"papelComissao"=> ["required"]
                ,"assinatura"   => ["image","file"]
                ];

        $messages = ["required"         => "O campo :attribute deve ser preenchido"
                    ,"min"              => "O campo :attribute deve ter no mínimo :min caracteres"
                    ,"max"              => "O campo :attribute deve ter no máximo :max caracteres"
                    ,"email"            => "O campo :attribute deve ser um e-mail válido"
                    ,"assinatura.image" => "O arquivo de assinatura deve ser do tipo JPG ou PNG"
                    ,"assinatura.file"  => "O upload do arquivo de assinatura falhou"
                    ];

        $request->validate($rule,$messages);
        
        $upload = false;
        if ($request->hasFile('assinatura') && $request->file('assinatura')->isValid()) {
            $arquivo = $request->file('assinatura');
            
            $arquivo->move(public_path('upload/assinatura'),$arquivo->getClientOriginalName());
            $upload = true;
        } 

        $objComissao = Comissao::find($id);
        $objComissao->nome = $request->input('nomeComissao');
        $objComissao->email = $request->input('emailComissao');
        $objComissao->papel = $request->input('papelComissao');
        if ($upload) {
            if (!empty($objComissao->assinatura) && 
                $arquivo->getClientOriginalName() != $objComissao->assinatura) 
            {
                File::delete('upload/assinatura/'.$objComissao->assinatura);
            }
            $objComissao->assinatura = $arquivo->getClientOriginalName();
        }
        $objComissao->update();

        return redirect()->route('comissao.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $objComissao = Comissao::find($id);
        if (!empty($objComissao->assinatura)) {
            File::delete('upload/assinatura/'.$objComissao->assinatura);
        }
        $objComissao->delete();

        return redirect()->route('comissao.index');

    }

    /**
     * Busca dados de Membro da Comissão
     * @param id int Número USP
     * @return Array
     */
    public function ajaxBuscaDadosComissao(int $id) {
        $arrRetorno = array();
        $vinculos = Pessoa::vinculosSiglas($id);
        $testeV = array_search("SERVIDOR",$vinculos);

        if ($testeV === false) {
            $arrRetorno = ["id" => ""
                          ,"nome" => ""
                          ,"email" => ""
                          ,"papel" => ""
                          ,"vinculos" => $vinculos];

            return $arrRetorno;
        }

        $dadosPessoa = Pessoa::dump($id);
        $email = Pessoa::email($id);
        
        $arrRetorno = ["id" => $dadosPessoa["codpes"]
                      ,"nome" => $dadosPessoa["nompes"]
                      ,"email" => $email
                      ,"papel" => ""
                      ,"vinculos" => $vinculos];

        return $arrRetorno;
    }
}
