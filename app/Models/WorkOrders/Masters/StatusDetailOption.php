<?php

namespace App\Models\WorkOrders\Masters;

use Illuminate\Database\Eloquent\Model;

class StatusDetailOption extends Model
{
    protected $table   = 'po_wo_m_status_detail_option';
    protected $casts = ['option' => 'object'];

    public function detail(){
        return $this->hasOne(StatusDetail::class, 'id', 'detail_id');
    }
}
