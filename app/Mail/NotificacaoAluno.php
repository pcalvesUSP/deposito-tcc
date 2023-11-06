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
    public $assunto;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $textoMsg, string $nome, string $assunto = null)
    {
        $this->textoMensagem = $textoMsg;
        $this->nome = $nome;
        $this->assunto = $assunto;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if (empty($this->assunto)) {
            $assunto = "Nova avaliação da Comissão";
        } else {
            $assunto = $this->assunto;
        }
        return $this->markdown('emails.mensagem_aluno')->subject("[SISTEMA DEPÓSITO TCC] ".$assunto);
    }
}
