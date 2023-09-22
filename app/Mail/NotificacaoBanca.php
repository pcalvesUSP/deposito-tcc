<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificacaoBanca extends Mailable
{
    use Queueable, SerializesModels;

    public $textoMensagem;
    public $assuntoMensagem;
    public $nome;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($nome, $textoMsg, $assuntoMsg)
    {
        $this->textoMensagem   = $textoMsg;
        $this->assuntoMensagem = $assuntoMsg;
        $this->nome            = $nome;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.mensagem_banca')
                    ->subject("[SISTEMA DEPÓSITO TCC] ".$this->assuntoMensagem);;
    }
}
