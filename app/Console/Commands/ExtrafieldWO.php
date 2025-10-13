<?php

namespace App\Console\Commands;

use App\Jobs\NotifJob;
use App\Libraries\BuildExtrafieldWo;
use App\Mail\UpdateWoMail;
use App\Models\WorkOrders\Action;
use App\Models\WorkOrders\ActionDetail;
use App\Models\WorkOrders\WorkOrder;
use App\SystemModels\Auth\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ExtrafieldWO extends Command
{

    protected $signature = 'wo:extrafield';
    protected $description = 'Build Extrafield';

    public function __construct(){
        parent::__construct();
    }

    public function handle(){
        $this->info('Running.....');
        BuildExtrafieldWo::build();
        $this->info('Finished.....');
    }
}
