@extends('layouts.app')

@section('content')
  <p>
  @if ($userLogado != "Aluno") <a href="{{ route('orientador.lista_monografia') }}">Listar Monografias</a> @endif
  </p>
  
  @if (!empty($avaliar) && $avaliar === true)
  <div id="avaliacao" class="grupo">
      @include('_partials.form_avaliacao')
  </div>
  <hr/>
  @endif

  @if (!$dadosAvaliacoes->isEmpty())
      @include('_partials.show_avaliacao')
  @endif

  <h1>Cadastro de Monografia</h1>
  <p class="aluno">
    ALUNO: {{ $numUSPAluno }} - {{ $nomeAluno }} <br/>
    STATUS: {{(isset($dadosMonografia->status)) ? $dadosMonografia->status :  "NÃO CADASTRADO" }} 
  </p>
  <form id="formMonografia" method="post" action="@if ($edicao) {{ route("alunos.corrigir",['id'=>$dadosMonografia->id]) }} @else {{ route("salvarMonografia") }} @endif" enctype="multipart/form-data">
    <fieldset id="fieldsMonografia" class="grupo">
      @csrf
      @if ($edicao)
          @method("PUT")
      @endif

      @if (!$dadosAvaliacoes->isEmpty())
      <input type="hidden" name="av_id" value="{{ $dadosAvaliacoes->first()->id }}">
      @endif
      <div class="campo">
        <label for="dupla">Trabalho em Dupla?</label>  
          <input type="checkbox" name="dupla" id="dupla" value="1" 
          @if ($readonly || $edicao) disabled readonly class="inputReadonly" @endif 
          @if (old('dupla') == 1 || (isset($dadosMonografia->dupla) && $dadosMonografia->dupla)) checked @endif><br/>
        <div id="trabDupla" @if (old('dupla') == 1 || (isset($dadosMonografia->dupla) && $dadosMonografia->dupla==1) ) style="display:block" @endif>
          <label for="passoaDupla">Selecione o membro do grupo de trabalho:</label>
          <select name="pessoaDupla" id="pessoaDupla" 
            @if ($readonly || $edicao) tabindex="-1" aria-disabled="true" class="selectReadonly" @endif >
            <option value="">Selecione</option>
            @foreach ($listaAlunosDupla as $objAluno)
            <option value="{{ $objAluno->numUSP }}" 
              @if (old('pessoaDupla') == $objAluno->numUSP || 
                  (isset($dadosAlunoGrupo->id) && $dadosAlunoGrupo->id == $objAluno->numUSP))
                selected 
              @endif>{{ $objAluno->numUSP}} - {{$objAluno->nome }}</option>
            @endforeach
          </select>
        </div>
        
        <div class="erro" id="edupla">{{  $errors->has('pessoaDupla') ? $errors->first('pessoaDupla'):null }}</div>
        @if (isset($dadosMonografia->dupla) && $dadosMonografia->dupla == 0)
        <div style="width:30%;margin-left:10px;margin-right:auto;font-weight:bold;">TRABALHO INDIVIDUAL</div>
        @endif
      </div>
      
      <div class="campo">
        <label for="orientador_id">Selecione o orientador principal:</label>
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
      @if (empty($dadosMonografia))
        <div class="campo">
          <label for="orientador_secundario_id">Selecione o(s) orientador(es) secundário(s), se houver:</label>
          <div id="orientadorSecundario">
            <select name="orientador_secundario_id_1" id="orientador_secundario_id_1" @if ($readonly) tabindex="-1" aria-disabled="true" class="selectReadonly" @endif>
              <option value="">Selecione</option>
              @foreach ($listaOrientadores as $objOrientador)
              <option value="{{ $objOrientador->id }}">{{ $objOrientador->nome }}</option>
              @endforeach
            </select>
          </div>
          <div id="novosOrientadores"></div>
          @if (!$readonly)
          <button value="addOrientador" name="addOrientador" id="addOrientador" ind="1">+</button>
          @endif
        </div>
        <br/>
      @elseif (!empty($orientadorSecundario))
        <p>Orientadores Secundários</p>
        @foreach ($orientadorSecundario as $list)
        {{ $list }}<br/>
        @endforeach
      @else
        <div style="width:30%;margin-left:10px;margin-right:auto;font-weight:bold;">NÃO HÁ ORIENTADOR SECUNDÁRIO</div>
      @endif

      <div class="campo">
        <label for="titulo"> T&iacute;tulo: </label>
        <input type="text" name="titulo" id="titulo" maxlength="255" size="90"
             value="@if (isset($dadosMonografia->titulo)) {{ $dadosMonografia->titulo }} @else {{ trim(old('titulo')) }} @endif" required @if ($readonly) class="inputReadonly" readonly @endif>
        <div class="erro">{{  $errors->has('titulo') ? $errors->first('titulo'):null }}</div><br/>
      </div>
      <div class="campo">
        <label for="resumo">Resumo:</label> 
        <textarea name="resumo" id="resumo" rows="10" cols="150" required @if ($readonly) class="inputReadonly" readonly @endif>
          @if (isset($dadosMonografia->resumo)) {{$dadosMonografia->resumo }} @else {{ trim(old('resumo')) }} @endif
        </textarea>
        <div class="erro">{{ $errors->has('resumo') ? $errors->first('resumo'):null }}</div><br/>
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
      
      @if (isset($dadosMonografia->template_apres)) 
        <a href="upload/{{ $dadosMonografia->template_apres }}">Baixar o trabalho</a>
      @endif
      @if (!$readonly && $userLogado != "Graduacao")
      <div class="campo">
        <label for="template_apres">Arquivo do TCC: </label><input type="file" name="template_apres" id="template_apres">
      </div>
      <br/>
      @endif

      <div class="campo">
        <label for="unitermo1">Descritor 1 (Palavra Chave):<br/>Tenha certeza que não existe na lista antes de cadastrar um novo. </label>
        <div style="float:left;">
        <select name="unitermo1" id="unitermo1" @if ($readonly) class="js-example-disabled-results" @else class="js-example-basic-single" @endif>
            <optgroup label="Descritor 1">
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
          @if (old('cadastroUnitermo1') == 1) checked @endif> Novo
        </div>
        <div id="txtUnitermo1" style="display:none; float:left; margin-left: 5px;"><input type="text" name="txtUnitermo1" value="" size="30" maxlength="45"></div>
        <div class="erro">{{  $errors->has('unitermo1') ? $errors->first('unitermo1'):null }}</div>
      </div>
      <br/>
      
      <div class="campo">
        <label for="unitermo2">Descritor 2 (Palavra Chave):<br/>Tenha certeza que não existe na lista antes de cadastrar um novo. </label>
        <div style="float:left;">
        <select name="unitermo2" id="unitermo2" @if ($readonly) tabindex="-1" aria-disabled="true" class="selectReadonly" @endif>
            <optgroup label="Descritor 2">
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
          @if (old('cadastroUnitermo2') == 1) checked @endif> Novo
        </div>
        <div id="txtUnitermo2" style="display:none; float:left; margin-left: 5px;"><input type="text" name="txtUnitermo2" value="" size="30" maxlength="45"></div>
        <div class="erro">{{  $errors->has('unitermo2') ? $errors->first('unitermo3'):null }}</div>
      </div>
      <br/>
      
      <div class="campo">
        <label for="unitermo3">Descritor 3 (Palavra Chave):<br/>Tenha certeza que não existe na lista antes de cadastrar um novo. </label>
        <div style="float:left;">
        <select name="unitermo3" id="unitermo3" @if ($readonly) tabindex="-1" aria-disabled="true" class="selectReadonly" @endif>
            <optgroup label="Descritor 3">
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
          @if (old('cadastroUnitermo3') == 1) checked @endif> Novo
        </div>
        <div id="txtUnitermo3" style="display:none; float:left; margin-left: 5px;"><input type="text" name="txtUnitermo3" value="" size="30" maxlength="45"></div>
        <div class="erro">{{  $errors->has('unitermo3') ? $errors->first('unitermo3'):null }}</div>
      </div>
      <br/>

      <div class="campo">
        <label for="unitermo4">Descritor 4 (Palavra Chave) opcional:<br/>Tenha certeza que não existe na lista antes de cadastrar um novo. </label>
        <div style="float:left;">
        <select name="unitermo4" id="unitermo4" @if ($readonly) tabindex="-1" aria-disabled="true" class="selectReadonly" @endif>
            <optgroup label="Descritor 4">
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
          @if (old('cadastroUnitermo4') == 1) checked @endif> Novo
        </div>
        <div id="txtUnitermo4" style="display:none; float:left; margin-left: 5px;"><input type="text" name="txtUnitermo4" value="" size="30" maxlength="45"></div>
      </div>
      <br/>

      <div class="campo">
        <label for="unitermo5">Descritor 5 (Palavra Chave) opcional:<br/>Tenha certeza que não existe na lista antes de cadastrar um novo. </label>
        <div style="float:left;">
        <select name="unitermo5" id="unitermo5" @if ($readonly) tabindex="-1" aria-disabled="true" class="selectReadonly" @endif>
            <optgroup label="Descritor 5">
            <option value="">Selecione</option>
            @foreach ($unitermos as $objUnitermos)
            <option value="{{ $objUnitermos->id }}" 
              @if (old('unitermo5') == $objUnitermos->id || (!empty($dadosMonografia) && isset($dadosUnitermos[3]) && $dadosUnitermos[3]->id == $objUnitermos->id)) selected @endif>
              {{ $objUnitermos->unitermo }}</option>
            @endforeach
            </optgroup>
        </select>
        </div>

        <div style="float:left; margin-left: 5px;">
        <input type="checkbox" id="cadastroUnitermo5" value="1" 
          @if ($readonly) disabled readonly class="inputReadonly" @endif 
          @if (old('cadastroUnitermo5') == 1) checked @endif> Novo
        </div>
        <div id="txtUnitermo5" style="display:none; float:left; margin-left: 5px;"><input type="text" name="txtUnitermo5" value="" size="30" maxlength="45"></div>
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
      @if (!$readonly) 
          <input type="submit" name="enviar" id="buttonSubmit" value="Enviar">
      @endif
    <fieldset>
  </form>

  <script src="js/formMonografia.js"></script>
  
@endsection