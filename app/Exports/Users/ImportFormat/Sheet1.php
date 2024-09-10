<?php

namespace App\Exports\Users\ImportFormat;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class Sheet1 implements FromView, WithTitle, WithColumnFormatting
{
    private $vendors;
    private $clients;
    private $roles;
    private $fieldtech;

    public function __construct($vendors, $clients, $roles, $fieldtech)
    {
        $this->vendors = $vendors;
        $this->clients = $clients;
        $this->roles = $roles;
        $this->fieldtech = $fieldtech;
    }

    public function view(): View
    {
        $vendors = $this->vendors;
        $clients = $this->clients;
        $roles = $this->roles;
        $fieldtech = $this->fieldtech;

        return view('exports.excel.user.import_format.sheet1', [
            'vendors' => $vendors,
            'clients' => $clients,
            'roles' => $roles,
            'fieldtech' => $fieldtech,
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
