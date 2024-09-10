<?php

namespace App\Exports\Users\ImportFormat;


use App\Models\Vendors\VendorArea;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithTitle;

class Sheet2 implements FromView, WithTitle
{
    private $vendors;

    public function __construct($vendors)
    {
        $this->vendors = $vendors;
    }

    public function view(): View
    {
        return view('exports.excel.user.import_format.sheet2', [
            'vendors' => $this->vendors
        ]);
    }

    public function title(): string
    {
        return 'AREA';
    }
}
