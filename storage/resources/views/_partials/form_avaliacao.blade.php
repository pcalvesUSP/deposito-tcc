<form action="{{ route('orientador.salvarParecer') }}" method="post">
    @csrf
    <input type="hidden" name="orientadorId" value="{{ $orientadorId }}" >
    <input type="hidden" name="monografiaId" value="{{ $monografiaId }}" >
    <div class="campo">
    <label for=>Ação: </label>
    <select id="acao" name="acao">
        <option value="0">Selecione</option>
        <option value="DEVOLVIDO" @if (!empty($acao) && ($acao == "DEVOLVIDO" || old('acao') == "DEVOLVIDO")) selected @endif>Devolver para Ajuste</option>
        <option value="APROVADO" @if (!empty($acao) && ($acao == "APROVADO" || old('acao') == "APROVADO")) selected @endif>Aprovar</option>
        <option value="REPROVADO" @if (!empty($acao) && ($acao == "REPROVADO" || old('acao') == "REPROVADO")) selected @endif>Reprovar</option>
    </select>
    <div class="erro" id="erroacao">{{  $errors->has('acao') ? $errors->first('acao'):null }}</div><br/>
    </div>
    <div id="publicacao" class="campo">
    <label for="publicar">Indico Trabalho para a publicação na BDTA (Biblioteca Digital de Trabalhos Acadêmicos)</label>
          Sim -> <input type="radio" id="publicar" name="publicar" value="1" 
             @if (old('publicar') == 1) checked @endif >&nbsp;&nbsp;&nbsp;&nbsp;
          Não -> <input type="radio" id="publicar" name="publicar" value="0" @if (old('publicar') == 1) checked @endif >
          <div class="erro">{{  $errors->has('publicar') ? $errors->first('publicar'):null }}</div>
    </div>
    <div id="parecer" class="campo">
        <label for=>Parecer: </label>
        <textarea id="textParecer" name="parecer" style="width: 100%;height: 150px;">{{ trim(old('textPArecer')) }}</textarea>
        <div class="erro" id="erroparecer">{{  $errors->has('parecer') ? $errors->first('parecer'):null }}</div>
    </div>
    <input type="submit" id="buttonSubmit" value="Enviar">
</form>

<script src="js/avaliacao.js"></script>