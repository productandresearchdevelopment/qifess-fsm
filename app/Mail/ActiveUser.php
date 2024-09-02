<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ActiveUser extends Mailable
{
    use Queueable, SerializesModels;

    private $user;
    private $password;

    public function __construct($user, $password){
        $this->user = $user;
        $this->password = $password;
    }

    public function build(){
        $params = [
            'user' => $this->user,
            'password' => $this->password,
        ];
        return $this->subject("Active User ".$this->user->name)->view('mails.active_user')->with($params);
    }
}
