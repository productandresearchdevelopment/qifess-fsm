<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateWoMail extends Mailable
{
    use Queueable, SerializesModels;

    private $wo;
    private $action;

    public function __construct($wo, $action){
        $this->wo = $wo;
        $this->action = $action;
    }

    public function build(){
        $params = [
            'wo' => $this->wo,
            'action' => $this->action,
        ];
        return $this->subject("UPDATE WO (".$this->wo->id.")")->view('mails.update_wo')->with($params);
    }
}
