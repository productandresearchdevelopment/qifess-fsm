<?php

namespace App\Exports\Users\ImportFormat;

use App\Models\Clients\Client;
use App\Models\Fieldteches\Fieldtech;
use App\Models\Services\Service;
use App\Models\Vendors\Vendor;
use App\Models\WorkOrders\Masters\Slot;
use App\SystemModels\Auth\Role;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Format implements WithMultipleSheets
{

    public function __construct() {}

    public function sheets(): array
    {
        $vendors = Vendor::all();
        $clients = Client::all();
        $fieldtech = Fieldtech::all();
        $role = Role::all();

        return [
            'DATA' => new Sheet1($vendors, $clients, $role, $fieldtech),
            'AREA' => new Sheet2($vendors),
            'CLIENT' => new Sheet3($clients),
            'ROLE' => new Sheet4($role),
            'FIELDTECH' => new Sheet5($fieldtech),
        ];
    }


    public function onUnknownSheet($sheetName)
    {
        info("Sheet {$sheetName} was skipped");
    }
}
