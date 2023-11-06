<label style="background-color: #9b51e0; color:white; font-weight: bold; width: 100%; text-align: center;">Modificar Parâmetro</label>

<form action="{{ route('graduacao.parametro') }}" method="post" id="formParametro">
    @csrf
    <input type="hidden" name="monografiaId" value="{{ $monografiaId }}" >
    <div class="campo">
        <label>Selecione o parâmetro a ser utilizado</label>
        <select name="paramMonografia" id="paramMonografia" required>
            <option value="">Selecione</option>
            @foreach ($listaParam as $param)
            <option value="{{ $param->id }}">{{ $param->semestre }}-{{ $param->ano }}</option>
            @endforeach
        </select>
        <div class="erro" id="eparamMonografia">{{  $errors->has('paramMonografia') ? $errors->first('paramMonografia'):null }}</div>
    </div>
    <input type="submit" id="modificaParametro" value="Enviar">
</form>