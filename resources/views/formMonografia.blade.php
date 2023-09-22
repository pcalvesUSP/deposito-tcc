@extends('layouts.app')

@section('content')
  <p>
  @if ($userLogado != "Aluno") <a href="{{ route('orientador.lista_monografia') }}">Listar Monografias</a> @endif
  </p>

  <div class="erro" id="mensagem"> {{ $mensagem }} </div>
  
  @if (!empty($avaliar) && $avaliar === true)
  <div id="avaliacao" class="grupo">
      @include('_partials.form_avaliacao')
  </div>
  <hr/>
  @endif

  @if (!$avaliar && !empty($dadosAvaliacoes) && !$dadosAvaliacoes->isEmpty())
      @include('_partials.show_avaliacao')
  @endif

  @if($uploadTcc && $dadosDefesa->isEmpty() && 
      $userLogado=="Aluno" && 
      !empty($dadosMonografia->status) && 
      $dadosMonografia->status == "AGUARDANDO ARQUIVO TCC")
      @include('_partials.form_banca')
  @elseif ($validacaoTcc || (!empty($dadosDefesa) && !$dadosDefesa->isEmpty()))
      @include('_partials.show_banca')
  @endif

  <h1>Cadastro de Projeto de TCC</h1>
  <br/>
  <p class="aluno">
    ALUNO: {{ $numUSPAluno }} - {{ $nomeAluno }} <br/>
    STATUS: {{(isset($dadosMonografia->status)) ? $dadosMonografia->status :  "NÃO CADASTRADO" }} 
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
          encontra-se o botão "Aprovação do Projeto"</p>
        <input type="hidden" name="aprovacao_projeto" value="1">
      </div>
      @endif

      @if ($indicarParecerista) 
      <div class="campo">
        <label for="parecerista" style="color:red"> Para uso da Graduação, indicar um parecerista:</label>
          <select name="parecerista" id="parecerista" required>
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
        <label for="orientador_id">Selecione o orientador:</label>
        <select name="orientador_id" id="orientador_id" required @if ($readonly || $edicao) tabindex="-1" aria-disabled="true" class="selectReadonly" @endif>
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
      @elseif ($uploadTcc && $userLogado != "Graduacao")
      <div class="campo">
        <label for="path_arq_tcc">Arquivo do TCC: </label><input type="file" name="path_arq_tcc" id="path_arq_tcc">
      </div>
      <br/>
      @endif

      <div class="campo">
        <label for="unitermo1">1&ordf; Palavra Chave:<br/><b>Tenha certeza que não existe na lista antes de cadastrar um novo.</b> </label>
        <div style="float:left;">
        <select name="unitermo1" id="unitermo1" @if ($readonly) tabindex="-1" aria-disabled="true" class="selectReadonly" @endif>
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
        <select name="unitermo2" id="unitermo2" @if ($readonly) tabindex="-1" aria-disabled="true" class="selectReadonly" @endif>
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
        <select name="unitermo3" id="unitermo3" @if ($readonly) tabindex="-1" aria-disabled="true" class="selectReadonly" @endif>
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
        <select name="unitermo4" id="unitermo4" @if ($readonly) tabindex="-1" aria-disabled="true" class="selectReadonly" @endif>
            <optgroup label="Palavra Chave 4">
            <option value="">Selecione</option>
            @foreach ($unitermos as $objUnitermos)
            <option value="{{ $objUnitermos->id }}" 
              @if (old('unitermo4') == $objUnitermos->id || (!empty($dadosMonografia) && isset($dadosUnitermos[4]) && $dadosUnitermos[4]->id == $objUnitermos->id)) selected @endif>
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
        <select name="unitermo5" id="unitermo5" @if ($readonly) tabindex="-1" aria-disabled="true" class="selectReadonly" @endif>
            <optgroup label="Palavra Chave 5">
            <option value="">Selecione</option>
            @foreach ($unitermos as $objUnitermos)
            <option value="{{ $objUnitermos->id }}" 
              @if (old('unitermo5') == $objUnitermos->id || (!empty($dadosMonografia) && isset($dadosUnitermos[5]) && $dadosUnitermos[5]->id == $objUnitermos->id)) selected @endif>
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
        <select name="cod_area_tematica" id="cod_area_tematica" @if ($readonly) tabindex="-1" aria-disabled="true" class="selectReadonly" @endif>
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
        <label for="aprovacao_projeto"> <button id="aprovacao_projeto">Aprovar Projeto para Avaliação</button> </label>
      </div>
      @elseif (!$readonly || $edicao) 
          <input type="submit" name="enviar" id="buttonSubmit" value="Enviar">
      @endif
    <fieldset>
  </form>

  <script src="js/formMonografia.js"></script>
  <script>
    setTimeout(function() {
                $('#mensagem').fadeOut('fast');
              }, 3000);
  </script>
  
@endsection