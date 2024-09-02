<?php
namespace App\Exports\Sites\ImportFormat;


use App\Models\Owners\OwnerArea;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithTitle;

class Sheet3 implements FromView, WithTitle
{
    private $clients;

    public function __construct($clients){
        $this->clients = $clients;
    }

    public function view(): View {
        return view('exports.excel.site.import_format.sheet3', [
            'clients' => $this->clients
        ]);
    }

    public function title(): string
    {
        return 'CLIENT';
    }
}
