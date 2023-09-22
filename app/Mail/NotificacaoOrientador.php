<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificacaoOrientador extends Mailable
{
    use Queueable, SerializesModels;

    public $textoMensagem;
    public $assuntoMensagem;
    public $nome;
    public $attach;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($textoMsg, $assuntoMsg, $nome, $attach = null)
    {
        $this->textoMensagem = $textoMsg;
        $this->assuntoMensagem = $assuntoMsg;
        $this->nome = $nome;
        $this->attach = $attach;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if (!empty($this->attach)) {
            return $this->markdown('emails.mensagem_orientador')
                        ->subject("[SISTEMA DEPÓSITO TCC] ".$this->assuntoMensagem)
                        ->attach($this->attach);
        } else {
            return $this->markdown('emails.mensagem_orientador')
                        ->subject("[SISTEMA DEPÓSITO TCC] ".$this->assuntoMensagem);
        }
    }
}
