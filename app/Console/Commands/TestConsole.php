<?php

namespace App\Console\Commands;

use App\Jobs\NotifJob;
use App\Mail\UpdateWoMail;
use App\Models\WorkOrders\WorkOrder;
use App\SystemModels\Auth\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestConsole extends Command
{

    protected $signature = 'test:command';
    protected $description = 'Testing Command';

    public function __construct(){
        parent::__construct();
    }

    public function handle(){
        //dispatch(new NotifJob('202207000001'));
        $wo = WorkOrder::find(202210000005);
        Mail::to(['andika2000@gmail.com','ade.mugianto@gmail.com'])->send(new UpdateWoMail($wo, $wo->lastAction));
    }


}
