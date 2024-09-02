<?php
namespace App\Exports\Sites\ImportFormat;

use App\Models\City;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithTitle;

class Sheet5 implements FromView, WithTitle
{
    private $teams;

    public function __construct($teams){
        $this->teams = $teams;
    }

    public function view(): View {
        return view('exports.excel.site.import_format.sheet5', [
            'teams' => $this->teams
        ]);
    }

    public function title(): string
    {
        return 'TEAM';
    }
}
