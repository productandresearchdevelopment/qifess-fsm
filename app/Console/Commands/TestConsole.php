<?php

namespace App\Console\Commands;

use App\Jobs\NotifJob;
use App\Models\WorkOrders\WorkOrder;
use App\SystemModels\Auth\User;
use Illuminate\Console\Command;

class TestConsole extends Command
{

    protected $signature = 'test:command';
    protected $description = 'Testing Command';

    public function __construct(){
        parent::__construct();
    }

    public function handle(){
        dispatch(new NotifJob('202207000001'));
    }


}
