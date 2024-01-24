<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

use App\Mail\NotificacaoOrientador;

class EnviarEmailOrientador implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected Array $details;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Create a new job instance.
     * @param Array details [email, textoMsg, assuntoMsg, nome, attach = null]
     *
     * @return void
     */
    public function __construct(array $details)
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
        Mail::to($this->details['email'], $this->details['nome'])
                        ->send(new NotificacaoOrientador($this->details['textoMsg']
                                                        ,$this->details['assuntoMsg']
                                                        ,$this->details['nome']
                                                        ,!empty($this->details['attach'])?$this->details['attach']:null)
                        );
        
    }
}
