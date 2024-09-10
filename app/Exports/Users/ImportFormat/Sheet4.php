<?php

namespace App\Exports\Users\ImportFormat;


use App\Models\Owners\OwnerArea;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithTitle;

class Sheet4 implements FromView, WithTitle
{
    private $roles;

    public function __construct($roles)
    {
        $this->roles = $roles;
    }

    public function view(): View
    {
        return view('exports.excel.user.import_format.sheet4', [
            'roles' => $this->roles
        ]);
    }

    public function title(): string
    {
        return 'ROLE';
    }
}
