<?php

namespace App\Mail;

use App\Models\Tarea;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TaskReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $tarea;

    public function __construct(Tarea $tarea)
    {
        $this->tarea = $tarea;
    }

    public function build()
    {
        return $this->from('rosinethesis2018@gmail.com', 'CARE App')
            ->replyTo('rosinethesis2018@gmail.com', 'Soporte CARE')
            ->subject('🌱 Recordatorio: ' . $this->tarea->tipo . ' para ' . $this->tarea->planta->nombre)
            ->markdown('emails.task-reminder');
    }
}