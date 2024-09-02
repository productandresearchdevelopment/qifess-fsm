<?php
namespace App\Exports\Sites\ImportFormat;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class Sheet1 implements FromView, WithTitle, WithColumnFormatting
{
    private $vendors;
    private $clients;
    private $services;
    private $teams;
    private $slots;

    public function __construct($vendors, $clients, $services, $teams, $slots){
        $this->vendors = $vendors;
        $this->clients = $clients;
        $this->services = $services;
        $this->teams = $teams;
        $this->slots = $slots;
    }

    public function view(): View {
        $vendors = $this->vendors;
        $clients = $this->clients;
        $services = $this->services;
        $teams = $this->teams;
        $slots = $this->slots;

        return view('exports.excel.site.import_format.sheet1', [
            'vendors' => $vendors,
            'clients' => $clients,
            'services' => $services,
            'teams' => $teams,
            'slots' => $slots,
        ]);
    }

    public function columnFormats(): array
    {
        return [
            'H' => NumberFormat::FORMAT_DATE_YYYYMMDD,
        ];
    }

    public function title(): string {
        return 'DATA';
    }
}

