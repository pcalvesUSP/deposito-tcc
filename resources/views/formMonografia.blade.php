@extends('layouts.app')

@section('content')
  <p>
  @if (strpos($userLogado,"Aluno") === false) <a href="{{ route('orientador.lista_monografia') }}">Listar Projetos</a> @endif
  </p>

  <div class="erro" id="mensagem"> {{ $mensagem }} @if ($errors->any()) ERRO NO CADASTRO DO TCC, VERIFIQUE ABAIXO. @endif </div>
  
  @if ($avaliar)
  <div id="avaliacao" class="grupo">
      @include('_partials.form_avaliacao')
  </div>
  <hr/>
  @elseif (strpos($userLogado,"Graduacao") !== false && (empty($dadosAvaliacoes) || $dadosAvaliacoes->isEmpty()) && !empty($dadosMonografia->status) && $dadosMonografia->status == "AGUARDANDO AVALIACAO" )
  <p style="color:red;">AGUARDANDO MEMBRO DA COMISSÃO INDICAR PARECERISTA</p>
  @endif

  @if (!$avaliar && !empty($dadosAvaliacoes) && !$dadosAvaliacoes->isEmpty())
      @include('_partials.show_avaliacao')
  @endif

  @if($uploadTcc && $dadosDefesa->isEmpty() && 
      strpos($userLogado,"Aluno") !== false && 
      !empty($dadosMonografia->status) && 
      $dadosMonografia->status == "AGUARDANDO ARQUIVO TCC")
      @include('_partials.form_banca')
  @elseif ($uploadTcc && !$dadosDefesa->isEmpty() &&
          strpos($userLogado,"Aluno") !== false &&
          $dadosDefesa->first()->aprovacao_orientador == 0 &&
          !empty($dadosMonografia->status) && 
          $dadosMonografia->status == "AGUARDANDO VALIDACAO DA BANCA")
      @include('_partials.form_banca')
  @elseif ($aprovaBanca &&
          strpos($userLogado,"Orientador") !== false && 
          !empty($dadosMonografia->status) && 
          $dadosMonografia->status == "AGUARDANDO VALIDACAO DA BANCA")
      @include('_partials.form_banca')
  @elseif ($validacaoTcc || (!empty($dadosDefesa) && !$dadosDefesa->isEmpty()))
      @include('_partials.show_banca')
  @endif

  @if ((!empty($dadosNotasProjeto) && !$dadosNotasProjeto->isEmpty()) || (!empty($dadosNotasTcc) && !$dadosNotasTcc->isEmpty()))
      @include('_partials.form_nota_projeto')
  @elseif ($inserirNota && !empty($dadosMonografia->status) && ($dadosMonografia->status == "AGUARDANDO NOTA DO PROJETO" || $dadosMonografia->status == "AGUARDANDO NOTA DO TCC"))
      @include('_partials.form_nota_projeto')
  @endif

  <h1>Cadastro de Projeto</h1>
  @if ($modificaParametro) 
       <p style="color: red; font-weight: bold;">Parâmetro utilizado: {{ $paramUtilizado }}</p>
       <p><input type="checkbox" id="modificarParametro"> Modificar Parâmetro</p>
       <div id="divParametro" style="display: none;">
       @include('_partials.form_parametro')
       </div>
  @endif
  <br/>
  <p class="aluno">
    ALUNO: {{ $numUSPAluno }} - {{ $nomeAluno }} <br/>
    STATUS: {{(isset($dadosMonografia->status)) ? $dadosMonografia->status :  "NÃO CADASTRADO" }} <br/>
    SEMESTRE/ ANO: {{(isset($dadosMonografia->ano)) ? $dadosMonografia->semestre."/ ".$dadosMonografia->ano :  "NÃO CADASTRADO" }}
  </p>
  <form id="formMonografia" method="post" action="@if ($edicao && !empty($dadosMonografia->id)) {{ route("alunos.corrigir",['id'=>$dadosMonografia->id]) }} @elseif($aprovOrientador) {{ route("orientador.aprovacao") }} @elseif($indicarParecerista) {{ route('indicaParecerista')}} @else {{ route("salvarMonografia") }} @endif" enctype="multipart/form-data">
    <fieldset id="fieldsMonografia" class="grupo">
      @csrf
      @if ($edicao || $aprovOrientador || $indicarParecerista)
          @method("PUT")
      @endif
      <input type="hidden" name="idTcc" id="idTcc" value="{{ isset($dadosMonografia->id)?$dadosMonografia->id:null }}">
      @if ($aprovOrientador)
      <div class="campo">
        <p style="color:red;font-weight:bold;">Verifique o conteúdo do projeto e caso seja necessário corrija. No final do formulário 
          encontra-se o botão "Encaminhar para Comissão"</p>
        <input type="hidden" name="aprovacao_projeto" value="1">
      </div>
      @endif

      @if ($indicarParecerista) 
      <div class="campo">
        <label for="parecerista" style="color:red"> Para uso da Comissão de TCC, indicar um parecerista:</label>
          <select name="parecerista" id="parecerista" required>
            <option>Selecione um parecerista</option>
            @foreach ($dadosParecerista as $parecerista) 
              <option value="{{ $parecerista->id }}">{{ $parecerista->nome }}</option>
            @endforeach
          </select><input type="submit" name="enviar" id="buttonSubmit" value="OK">
          <div class="erro" id="eparecerista">{{  $errors->has('parecerista') ? $errors->first('parecerista'):null }}</div>
      </div>
      @endif

      @if (!empty($dadosAvaliacoes) && !$dadosAvaliacoes->isEmpty())
      <input type="hidden" name="av_id" value="{{ $dadosAvaliacoes->first()->id }}">
      @endif

      <div class="campo">
        <label for="curriculo">Selecione o currículo:</label>
        <select name="curriculo" id="curriculo" required @if ($readonly || $edicao || $aprovadoOrientador) tabindex="-1" aria-disabled="1" class="selectReadonly" @endif>
          <option value="">Selecione</option>
          <option value="9012" {{ ((old('curriculo')=='9012') || (isset($dadosMonografia->curriculo) && $dadosMonografia->curriculo == 9012))?'selected':null }}>Currículo 9012 - alunos ingressantes até 2019</option>
          <option value="9013" {{ ((old('curriculo')=='9013') || (isset($dadosMonografia->curriculo) && $dadosMonografia->curriculo == 9013))?'selected':null }}>Currículo 9013 - alunos ingressantes a partir de 2020</option>
        </select>
        <div class="erro" id="ecurriculo">{{  $errors->has('curriculo') ? $errors->first('curriculo'):null }}</div>
      </div>
      <br/>
      
      <div class="campo">
        <label for="orientador_id">Selecione o orientador:</label>
        <select name="orientador_id" id="orientador_id" required @if ($readonly || $edicao || $aprovadoOrientador) tabindex="-1" aria-disabled="1" class="selectReadonly" @endif>
          <option value="">Selecione</option>
          @foreach ($listaOrientadores as $objOrientador)
          <option value="{{ $objOrientador->id }}" 
            @if (old('orientador_id') == $objOrientador->id ||
                 $orientadorId == $objOrientador->id)
            selected @endif>{{ $objOrientador->nome }}</option>
          @endforeach
        </select>
        <div class="erro" id="eorientador">{{  $errors->has('orientador_id') ? $errors->first('orientador_id'):null }}</div>
      </div>
      <br/>
      <div class="campo">
        <label for="titulo"> T&iacute;tulo: </label>
        <input type="text" name="titulo" id="titulo" maxlength="255" size="90"
             value="@if (isset($dadosMonografia->titulo)) {{ $dadosMonografia->titulo }} @else {{ trim(old('titulo')) }} @endif" required @if ($readonly) class="inputReadonly" readonly @endif>
        <div class="erro">{{  $errors->has('titulo') ? $errors->first('titulo'):null }}</div><br/>
      </div>
      <br/>
      <div class="campo">
        <label for="resumo">Resumo:</label> 
        <textarea name="resumo" id="resumo" rows="10" cols="150" required @if ($readonly) class="inputReadonly" readonly @endif>
          @if (isset($dadosMonografia->resumo)) {{$dadosMonografia->resumo }} @else {{ trim(old('resumo')) }} @endif
        </textarea>
        <div class="erro">{{ $errors->has('resumo') ? $errors->first('resumo'):null }}</div><br/>
      </div>
      <br/>
      <div class="campo">
        <label for="introducao">Introdução:</label> 
        <textarea name="introducao" id="introducao" rows="10" cols="150" required @if ($readonly) class="inputReadonly" readonly @endif>
          @if (isset($dadosMonografia->introducao)) {{$dadosMonografia->introducao }} @else {{ trim(old('introducao')) }} @endif
        </textarea>
        <div class="erro">{{ $errors->has('introducao') ? $errors->first('introducao'):null }}</div><br/>
      </div>
      <br/>
      <div class="campo">
        <label for="objetivo">Objetivo:</label> 
        <textarea name="objetivo" id="objetivo" rows="10" cols="150" required @if ($readonly) class="inputReadonly" readonly @endif>
          @if (isset($dadosMonografia->objetivo)) {{$dadosMonografia->objetivo }} @else {{ trim(old('objetivo')) }} @endif
        </textarea>
        <div class="erro">{{ $errors->has('objetivo') ? $errors->first('objetivo'):null }}</div><br/>
      </div>
      <br/>
      <div class="campo">
        <label for="material_metodo">Materiais e Métodos:</label> 
        <textarea name="material_metodo" id="material_metodo" rows="10" cols="150" required @if ($readonly) class="inputReadonly" readonly @endif>
          @if (isset($dadosMonografia->material_metodo)) {{$dadosMonografia->material_metodo }} @else {{ trim(old('material_metodo')) }} @endif
        </textarea>
        <div class="erro">{{ $errors->has('material_metodo') ? $errors->first('material_metodo'):null }}</div><br/>
      </div>
      <br/>
      <div class="campo">
        <label for="resultado_esperado">Resultado Esperado:</label> 
        <textarea name="resultado_esperado" id="resultado_esperado" rows="10" cols="150" required @if ($readonly) class="inputReadonly" readonly @endif>
          @if (isset($dadosMonografia->resultado_esperado)) {{$dadosMonografia->resultado_esperado }} @else {{ trim(old('resultado_esperado')) }} @endif
        </textarea>
        <div class="erro">{{ $errors->has('resultado_esperado') ? $errors->first('resultado_esperado'):null }}</div><br/>
      </div>
      <br/>
      <div class="campo">
        <label for="aspecto_etico">Aspectos Éticos:</label> 
        <textarea name="aspecto_etico" id="aspecto_etico" rows="10" cols="150" required @if ($readonly) class="inputReadonly" readonly @endif>
          @if (isset($dadosMonografia->aspecto_etico)) {{$dadosMonografia->aspecto_etico }} @else {{ trim(old('aspecto_etico')) }} @endif
        </textarea>
        <div class="erro">{{ $errors->has('aspecto_etico') ? $errors->first('aspecto_etico'):null }}</div><br/>
      </div>
      <br/>
      <div class="campo">
        <label for="referencias">Referências:</label> 
        <textarea name="referencias" id="referencias" rows="10" cols="150" required @if ($readonly) class="inputReadonly" readonly @endif>
          @if (isset($dadosMonografia->referencias)) {{$dadosMonografia->referencias }} @else {{ trim(old('referencias')) }} @endif
        </textarea>
        <div class="erro">{{ $errors->has('referencias') ? $errors->first('referencias'):null }}</div><br/>
      </div>
      <br/>
      @if ($publicar)
        <div class="campo">
          <label for="publicar">Publicar Trabalho?</label>
          Sim -> <input type="radio" id="publicar" name="publicar" value="1" 
             @if (old('publicar') == 1 || (isset($dadosMonografia->publicar) && $dadosMonografia->publicar == 1)) checked @endif >&nbsp;&nbsp;&nbsp;&nbsp;
          Não -> <input type="radio" id="publicar" name="publicar" value="0"
             @if ((isset($dadosMonografia->publicar) && $dadosMonografia->publicar == 0)) checked @endif>
          <div class="erro">{{  $errors->has('publicar') ? $errors->first('publicar'):null }}</div>
        </div>
        <br/>
      @endif
      
      @if (!empty($dadosMonografia->path_arq_tcc)) 
        <b>Para visualizar o TCC do aluno, <a href="upload/{{ $dadosMonografia->path_arq_tcc }}">baixe o trabalho</a></b>
      @elseif ($uploadTcc && strpos($userLogado,"Admin") !== false)
      <div class="campo">
        <label for="path_arq_tcc">Arquivo do TCC: </label><input type="file" name="path_arq_tcc" id="path_arq_tcc">
      </div>
      <br/>
      @endif

      <div class="campo">
        <label for="unitermo1">1&ordf; Palavra Chave:<br/><b>Tenha certeza que não existe na lista antes de cadastrar um novo.</b> </label>
        <div style="float:left;">
        <select name="unitermo1" id="unitermo1" @if ($readonly) tabindex="-1" aria-disabled="1" class="selectReadonly" @endif>
            <optgroup label="Palavra Chave 1">
            <option value="">Selecione</option>
            @foreach ($unitermos as $objUnitermos)
            <option value="{{ $objUnitermos->id }}" 
              @if (old('unitermo1') == $objUnitermos->id || (!empty($dadosMonografia) && isset($dadosUnitermos[0]) && $dadosUnitermos[0]->id == $objUnitermos->id)) selected @endif>
              {{ $objUnitermos->unitermo }}</option>
            @endforeach
            </optgroup>
        </select>
        </div>

        <div style="float:left; margin-left: 5px;">
        <input type="checkbox" id="cadastroUnitermo1" value="1" 
          @if ($readonly) disabled readonly class="inputReadonly" @endif 
          @if (!empty(old('txtUnitermo1'))) checked @endif> Novo
        </div>
        <div id="txtUnitermo1" style="display:none; float:left; margin-left: 5px;"><input type="text" name="txtUnitermo1" value="{{ old('txtUnitermo1') }}" size="30" maxlength="45"></div>
        <div class="erro">{{  $errors->has('unitermo1') ? $errors->first('unitermo1'):null }}</div>
      </div>
      <br/>
      
      <div class="campo">
        <label for="unitermo2">2&ordf; Palavra Chave:<br/><b>Tenha certeza que não existe na lista antes de cadastrar um novo.</b> </label>
        <div style="float:left;">
        <select name="unitermo2" id="unitermo2" @if ($readonly) tabindex="-1" aria-disabled="1" class="selectReadonly" @endif>
            <optgroup label="Palavra Chave 2">
            <option value="">Selecione</option>
            @foreach ($unitermos as $objUnitermos)
            <option value="{{ $objUnitermos->id }}" 
              @if (old('unitermo2') == $objUnitermos->id || (!empty($dadosMonografia) && isset($dadosUnitermos[1]) && $dadosUnitermos[1]->id == $objUnitermos->id)) selected @endif>
              {{ $objUnitermos->unitermo }}</option>
            @endforeach
            </optgroup>
        </select>
        </div>

        <div style="float:left; margin-left: 5px;">
        <input type="checkbox" id="cadastroUnitermo2" value="1" 
          @if ($readonly) disabled readonly class="inputReadonly" @endif 
          @if (!empty(old('txtUnitermo2'))) checked @endif> Novo
        </div>
        <div id="txtUnitermo2" style="display:none; float:left; margin-left: 5px;"><input type="text" name="txtUnitermo2" value="{{ old('txtUnitermo2') }}" size="30" maxlength="45"></div>
        <div class="erro">{{  $errors->has('unitermo2') ? $errors->first('unitermo2'):null }}</div>
      </div>
      <br/>
      
      <div class="campo">
        <label for="unitermo3">3&ordf; Palavra Chave:<br/><b>Tenha certeza que não existe na lista antes de cadastrar um novo.</b></label>
        <div style="float:left;">
        <select name="unitermo3" id="unitermo3" @if ($readonly) tabindex="-1" aria-disabled="1" class="selectReadonly" @endif>
            <optgroup label="Palavra Chave 3">
            <option value="">Selecione</option>
            @foreach ($unitermos as $objUnitermos)
            <option value="{{ $objUnitermos->id }}" 
              @if (old('unitermo3') == $objUnitermos->id || (!empty($dadosMonografia) && isset($dadosUnitermos[2]) && $dadosUnitermos[2]->id == $objUnitermos->id)) selected @endif>
              {{ $objUnitermos->unitermo }}</option>
            @endforeach
            </optgroup>
        </select>
        </div>

        <div style="float:left; margin-left: 5px;">
        <input type="checkbox" id="cadastroUnitermo3" value="1" 
          @if ($readonly) disabled readonly class="inputReadonly" @endif 
          @if ( !empty(old('txtUnitermo3'))) checked @endif> Novo
        </div>
        <div id="txtUnitermo3" style="display:none; float:left; margin-left: 5px;"><input type="text" name="txtUnitermo3" value="{{ old('txtUnitermo3') }}" size="30" maxlength="45"></div>
        <div class="erro">{{  $errors->has('unitermo3') ? $errors->first('unitermo3'):null }}</div>
      </div>
      <br/>

      <div class="campo">
        <label for="unitermo4">4&ordf; Palavra Chave:<br/><b>Tenha certeza que não existe na lista antes de cadastrar um novo.</b> </label>
        <div style="float:left;">
        <select name="unitermo4" id="unitermo4" @if ($readonly) tabindex="-1" aria-disabled="1" class="selectReadonly" @endif>
            <optgroup label="Palavra Chave 4">
            <option value="">Selecione</option>
            @foreach ($unitermos as $objUnitermos)
            <option value="{{ $objUnitermos->id }}" 
              @if (old('unitermo4') == $objUnitermos->id || (!empty($dadosMonografia) && isset($dadosUnitermos[3]) && $dadosUnitermos[3]->id == $objUnitermos->id)) selected @endif>
              {{ $objUnitermos->unitermo }}</option>
            @endforeach
            </optgroup>
        </select>
        </div>

        <div style="float:left; margin-left: 5px;">
        <input type="checkbox" id="cadastroUnitermo4" value="1" 
          @if ($readonly) disabled readonly class="inputReadonly" @endif 
          @if (!empty(old('txtUnitermo4'))) checked @endif> Novo
        </div>
        <div id="txtUnitermo4" style="display:none; float:left; margin-left: 5px;"><input type="text" name="txtUnitermo4" value="{{ old('txtUnitermo4') }}" size="30" maxlength="45"></div>
      </div>
      <br/>

      <div class="campo">
        <label for="unitermo5">5&ordf; Palavra Chave:<br/><b>Tenha certeza que não existe na lista antes de cadastrar um novo.</b> </label>
        <div style="float:left;">
        <select name="unitermo5" id="unitermo5" @if ($readonly) tabindex="-1" aria-disabled="1" class="selectReadonly" @endif>
            <optgroup label="Palavra Chave 5">
            <option value="">Selecione</option>
            @foreach ($unitermos as $objUnitermos)
            <option value="{{ $objUnitermos->id }}" 
              @if (old('unitermo5') == $objUnitermos->id || (!empty($dadosMonografia) && isset($dadosUnitermos[4]) && $dadosUnitermos[4]->id == $objUnitermos->id)) selected @endif>
              {{ $objUnitermos->unitermo }}</option>
            @endforeach
            </optgroup>
        </select>
        </div>

        <div style="float:left; margin-left: 5px;">
        <input type="checkbox" id="cadastroUnitermo5" value="1" 
          @if ($readonly) disabled readonly class="inputReadonly" @endif 
          @if (!empty(old('txtUnitermo5'))) checked @endif> Novo
        </div>
        <div id="txtUnitermo5" style="display:none; float:left; margin-left: 5px;"><input type="text" name="txtUnitermo5" value="{{ old('txtUnitermo5') }}" size="30" maxlength="45"></div>
      </div>
      <br/>
      
      <div class="campo">
      <label for="cod_area_tematica"> Área Temática: </label>
        <select name="cod_area_tematica" id="cod_area_tematica" @if ($readonly) tabindex="-1" aria-disabled="1" class="selectReadonly" @endif>
            <option value="">Selecione</option>
            @foreach ($areas_tematicas as $area)
            <option value="{{ $area->id }}" @if (old('cod_area_tematica') == $area->id || (isset($dadosMonografia->areastematicas_id) && $dadosMonografia->areastematicas_id == $area->id) ) selected @endif> {{ $area->descricao }} </option>
            @endforeach
        </select>
        <div class="erro">{{  $errors->has('cod_area_tematica') ? $errors->first('cod_area_tematica'):null }}</div><br/>
      </div>

      <input type="hidden" name="ano" vaue = "<?=date('Y'); ?>">
      @csrf
      @if ($aprovOrientador)
      <div class="campo">
        <label for="aprovacao_projeto"> <button id="aprovacao_projeto">Encaminhar projeto para Comissão</button> </label>
      </div>
      @elseif (!$readonly || $edicao) 
          <input type="submit" name="enviar" id="enviarProjeto" value="Enviar">
      @endif
    <fieldset>
  </form>
  <div id="process" title="Aguarde, pode levar algum tempo..."></div>
  <script src="js/formMonografia.js"></script>
  <script>

    $(document).ready(function(){

      function aguarde() {
            $("#process").dialog("open").html("<img src='{{ asset('/vendor/laravel-usp-theme/fcf/images/aguarde.gif') }}'>");
           
           /*$.ajax({
                type: 'POST',
                url:'url.php',
                data:{ 'id': 999999 },
                beforeSend: function() {
                    $( "#process" ).dialog( "open" ).html("<p>Aguarde a validação e envio de e-mails para todos os Membros da Banca</p>");
                    //$('body').css("background-color","rgba(0,0,0,0.1)")
                }
            }).done(function(data) {
                $("#process").html(data);
            });*/
      } 

      $( "#process" ).dialog({
            autoOpen: false,
            width: 400,
            resizable: false,
            draggable: false,
            close: function(){
                // executa uma ação ao fechar
                //alert("você fechou a janela");
                aguarde();
            }
      });
        
      $('#enviarProjeto').click(function () {
        var enviar = true;

        if (unitermo1.length > 0 && (unitermo1 == unitermo2 || unitermo1 == unitermo3 || unitermo1 == unitermo4 || unitermo1 == unitermo5)) {
            alert("Não podem ser informadas 2 palavras chaves iguais! Verifique a Palavra-chave 1");
            enviar = false;
        }

        if (unitermo2.length > 0 && (unitermo2 == unitermo3 || unitermo2 == unitermo4 || unitermo2 == unitermo5)) {
            alert("Não podem ser informadas 2 palavras chaves iguais! Verifique a Palavra Chave 2");
            enviar = false;
        }

        if (unitermo3.length > 0 && (unitermo3 == unitermo4 || unitermo3 == unitermo5) ) {
            alert("Não podem ser informadas 2 palavras chaves iguais! Verifique a Palavra Chave 3");
            enviar = false;
        }

        if (unitermo4.length > 0 && unitermo4 == unitermo5 ) {
            alert("Não podem ser informadas 2 palavras chaves iguais! Verifique a Palavra chave 4");
            enviar = false;
        }

        if (enviar == true) {
            $('body').css("background-color","rgba(0,0,0,0.1)");
            $(this).css("display","none");
            $('#formMonografia').submit();

            aguarde();
            
        } else {
            return false;
        }
      });	

    });
  </script>
  
@endsection