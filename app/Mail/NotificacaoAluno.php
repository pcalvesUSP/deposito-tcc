<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificacaoAluno extends Mailable
{
    use Queueable, SerializesModels;

    public $textoMensagem;
    public $nome;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $textoMsg, string $nome)
    {
        $this->textoMensagem = $textoMsg;
        $this->nome = $nome;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.mensagem_aluno')->subject("[SISTEMA DEPÓSITO TCC] Novo Parecer Cadastrado");
    }
}
