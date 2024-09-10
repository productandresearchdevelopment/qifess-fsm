<?php

namespace App\Exports\Services\ImportFormat;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class Sheet1 implements FromView, WithTitle, WithColumnFormatting
{
    private $services;

    public function __construct($services)
    {
        $this->services = $services;
    }

    public function view(): View
    {
        $services = $this->services;

        return view('exports.excel.service.import_format.sheet1', [
            'services' => $services,
        ]);
    }

    public function columnFormats(): array
    {
        return [
            'H' => NumberFormat::FORMAT_DATE_YYYYMMDD,
        ];
    }

    public function title(): string
    {
        return 'DATA';
    }
}
