<?php
namespace App\Exports\Sites\ImportFormat;

use App\Models\City;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithTitle;

class Sheet6 implements FromView, WithTitle
{
    private $slots;

    public function __construct($slots){
        $this->slots = $slots;
    }

    public function view(): View {
        return view('exports.excel.site.import_format.sheet6', [
            'slots' => $this->slots
        ]);
    }

    public function title(): string
    {
        return 'SLOT';
    }
}
