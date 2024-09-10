<?php

namespace App\Imports\Users;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Import implements WithMultipleSheets
{
    protected $user;
    protected $sheet;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function sheets(): array
    {
        $this->sheet = new ImportSheet($this->user);
        return [
            'DATA' => $this->sheet
        ];
    }

    public function logs()
    {
        return $this->sheet->logs;
    }
}
