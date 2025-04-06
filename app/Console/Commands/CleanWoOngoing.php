<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\WorkOrders\WorkOrderOngoing;

class CleanWoOngoing extends Command
{
    protected $signature = 'clean:woongoing';
    protected $description = 'Hapus data dari WoOngoing jika sudah memiliki close_date';

    public function handle()
    {
        Log::info('clean:woongoing dijalankan pada ' . now());

        $workOrders = WorkOrderOngoing::whereNotNull('close_date')->get();

        $count = $workOrders->count();

        foreach ($workOrders as $wo) {
            $wo->forceDelete();
        }

        $this->info("Data close_date not null sebanyak {$count} telah dihapus dari WoOngoing.");
        Log::info("clean:woongoing menghapus {$count} data pada " . now());
    }
}
