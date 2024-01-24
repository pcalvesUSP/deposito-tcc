<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

use App\Mail\NotificacaoAluno;

class EnviarEmailAluno implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected Array $details;

    /**
     * Create a new job instance.
     * @param Array details [string email, string textoMsg, string nome, string assunto = null]
     *
     * @return void
     */
    public function __construct(Array $details)
    {
        $this->details = $details;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $assunto = !empty($this->details['assunto'])?$this->details['assunto']:null;

        Mail::to($this->details['email'], $this->details['nome'])
                ->send(new NotificacaoAluno($this->details['textoMsg']
                                            ,$this->details['nome']
                                            ,$assunto
                                            )
                );

    }
}
