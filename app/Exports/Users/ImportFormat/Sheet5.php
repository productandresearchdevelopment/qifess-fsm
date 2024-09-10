<?php

namespace App\Exports\Users\ImportFormat;

use App\Models\City;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithTitle;

class Sheet5 implements FromView, WithTitle
{
    private $fieldtech;

    public function __construct($fieldtech)
    {
        $this->fieldtech = $fieldtech;
    }

    public function view(): View
    {
        return view('exports.excel.user.import_format.sheet5', [
            'fieldtech' => $this->fieldtech
        ]);
    }

    public function title(): string
    {
        return 'FIELDTECH';
    }
}
