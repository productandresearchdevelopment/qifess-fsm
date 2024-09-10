<?php

namespace App\Exports\Services\ImportFormat;

use App\Models\Services\Service;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Format implements WithMultipleSheets
{

    public function __construct() {}

    public function sheets(): array
    {
        $services = Service::all();

        return [
            'DATA' => new Sheet1($services),
        ];
    }

    public function onUnknownSheet($sheetName)
    {
        info("Sheet {$sheetName} was skipped");
    }
}
