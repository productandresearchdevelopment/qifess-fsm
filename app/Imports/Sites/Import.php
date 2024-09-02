<?php

namespace App\Imports\Sites;

use App\Models\WorkOrders\Masters\Activity;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Import implements WithMultipleSheets
{
    protected $user;
    protected $activity;
    protected $sheet;

    public function __construct($user, $activity){
        $this->user = $user;
        $this->activity = Activity::find($activity);
    }

    public function sheets(): array{
        $this->sheet = new ImportSheet($this->user, $this->activity);
        return [
            'DATA' => $this->sheet
        ];
    }

    public function logs(){
        return $this->sheet->logs;
    }

}
