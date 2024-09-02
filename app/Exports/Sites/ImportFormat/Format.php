<?php
namespace App\Exports\Sites\ImportFormat;

use App\Models\Clients\Client;
use App\Models\Fieldteches\Fieldtech;
use App\Models\Services\Service;
use App\Models\Vendors\Vendor;
use App\Models\WorkOrders\Masters\Slot;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Format implements WithMultipleSheets {

    public function __construct(){

    }

    public function sheets(): array {
        $vendors = Vendor::all();
        $clients = Client::all();
        $services = Service::all();
        $teams = Fieldtech::all();
        $slots = Slot::all();

        return [
            'DATA' => new Sheet1($vendors, $clients, $services, $teams, $slots),
            'AREA' => new Sheet2($vendors),
            'CLIENT' => new Sheet3($clients),
            'SERVICE' => new Sheet4($services),
            'TEAM' => new Sheet5($teams),
            'SLOT' => new Sheet6($slots),
        ];
    }

    public function onUnknownSheet($sheetName) {
        info("Sheet {$sheetName} was skipped");
    }
}
