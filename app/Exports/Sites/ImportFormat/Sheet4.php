<?php
namespace App\Exports\Sites\ImportFormat;

use App\Models\City;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithTitle;

class Sheet4 implements FromView, WithTitle
{
    private $services;

    public function __construct($services){
        $this->services = $services;
    }

    public function view(): View {
        return view('exports.excel.site.import_format.sheet4', [
            'services' => $this->services
        ]);
    }

    public function title(): string
    {
        return 'SERVICE';
    }
}
