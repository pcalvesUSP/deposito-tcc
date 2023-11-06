<label style="background-color: #9b51e0; color:white; font-weight: bold; width: 100%; text-align: center;">PARA AVALIAÇÃO</label>
@if (!empty($correcaoSolicitada))
<p style="color:red;"><b>Correção solicitado ao aluno para verificação</b>:<br/>
{{ $correcaoSolicitada }}
</p>
@endif

<p>Senhor(a) Avaliador,</p>
<p>O projeto deverá ser avaliado quanto aos seguintes aspectos:
    <ul>
        <li>Definição, pertinência e originalidade dos objetivos</li>
        <li>Importância da contribuição pretendida para a área farmacêutica</li>
        <li>Fundamentação científica</li>
        <li>Métodos empregados</li>
        <li>Se há deficiências notadas na proposta [objetivos mal definidos, incongruentes ou limitado; projeto pouco original; contribuição pouco significativa]</li>
    </ul>
</p>

<form action="{{ route('orientador.salvarParecer') }}" method="post" id="formAvaliacao">
    @csrf
    <input type="hidden" name="pareceristaid" value="{{ $idParecerista }}" >
    <input type="hidden" name="monografiaId" value="{{ $monografiaId }}" >
    <div class="campo">
    <label for=>Ação: </label>
    <select id="acao" name="acao">
        <option value="0">Selecione</option>
        <option value="DEVOLVIDO" @if ((!empty($acao) && $acao == "DEVOLVIDO") || old('acao') == "DEVOLVIDO") selected @endif>Devolver para Ajuste</option>
        <option value="APROVADO" @if ((!empty($acao) && acao == "APROVADO") || old('acao') == "APROVADO") selected @endif>Aprovar</option>
    </select>
    <div class="erro" id="erroacao">{{  $errors->has('acao') ? $errors->first('acao'):null }}</div><br/>
    </div>
    <div id="publicacao" class="campo" style="display:@if ( empty($acao) && old('acao') == "" ) none @else inline @endif ;">
    <label for="publicar">Indico Trabalho para a publicação na BDTA (Biblioteca Digital de Trabalhos Acadêmicos)</label>
          Sim -> <input type="radio" id="publicar" name="publicar" value="1" 
             @if (old('publicar') == 1) checked @endif >&nbsp;&nbsp;&nbsp;&nbsp;
          Não -> <input type="radio" id="publicar" name="publicar" value="0" @if (old('publicar') == 1) checked @endif >
          <div class="erro">{{  $errors->has('publicar') ? $errors->first('publicar'):null }}</div>
    </div>
    <div id="parecer" class="campo" style="display:none;">
        <label for=>Parecer: </label>
        <textarea id="textParecer" name="parecer" style="width: 100%;height: 150px;">{{ trim(old('parecer')) }}</textarea>
        <div class="erro" id="erroparecer">{{  $errors->has('parecer') ? $errors->first('parecer'):null }}</div>
    </div>
    <input type="submit" id="enviarAvaliacao" value="Enviar">
</form>

<script src="js/avaliacao.js"></script>