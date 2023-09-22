<label style="background-color: #9b51e0; color:white; font-weight: bold; width: 100%; text-align: center;">Informe a Nota do Aluno</label>
<form id="form_nota_{{ $numUSPAluno }}" method="post">
    @csrf
    @if (!empty($dadosMonografia->alunos[0]->projeto_nota))
        @method("PUT")
    @endif
    <input type="hidden" name="nuspAluno" value="{{ $numUSPAluno }}">
    <input type="hidden" name="idTccNota" value="{{ $dadosMonografia->id }}">
    ALUNO: {{ $numUSPAluno }} - {{ $nomeAluno }} <br/>
    <div id="campo">
        <label style="background-color: #9b51e0; color:white; font-weight: bold; text-align: right; margin-left: 105px;">Nota do Projeto</label>
        Nota: <input @if($dadosMonografia->status =="CONCLUIDO") @readonly(true) @endif type="text" name="projeto_nota" value="{{ !empty($dadosMonografia->alunos[0]->projeto_nota)?$dadosMonografia->alunos[0]->projeto_nota:old('projeto_nota') }}" style="border: solid 1px black;">&nbsp; 
        Frequência: <input @if($dadosMonografia->status =="CONCLUIDO") @readonly(true) @endif type="text" name="projeto_freq" value="{{ !empty($dadosMonografia->alunos[0]->projeto_freq)?$dadosMonografia->alunos[0]->projeto_freq:old('projeto_freq') }}" style="border: solid 1px black;">
    </div>
    <div id="campo">
        <label style="background-color: #9b51e0; color:white; font-weight: bold; text-align: right;">Nota da Apresentação do TCC</label>
        Nota: <input @if($dadosMonografia->status =="CONCLUIDO") @readonly(true) @endif type="text" name="tcc_nota" value="{{ !empty($dadosMonografia->alunos[0]->tcc_nota)?$dadosMonografia->alunos[0]->tcc_nota:old('tcc_nota') }}" style="border: solid 1px black;">&nbsp; 
        Frequência: <input @if($dadosMonografia->status =="CONCLUIDO") @readonly(true) @endif  type="text" name="tcc_freq" {{ !empty($dadosMonografia->alunos[0]->tcc_freq)?$dadosMonografia->alunos[0]->tcc_freq:old('tcc_freq') }} style="border: solid 1px black;">
    </div>
    <input type="submit" name="enviar" id="buttonNota" value="{{ !empty($dadosMonografia->alunos[0]->projeto_nota)?'Atualizar Nota':'Registrar a Nota' }}">
</form>
