<?php

namespace App\Models\WorkOrders\Masters;

use App\Models\WorkOrders\ActionDetail;
use Illuminate\Database\Eloquent\Model;

class StatusDetail extends Model
{
    protected $table   = 'po_wo_m_status_detail';
    protected $casts = ['property' => 'object', 'default' => 'object'];

    public function status(){
        return $this->hasOne(Status::class, 'id', 'status_id');
    }

    public function options(){
        return $this->hasMany(StatusDetailOption::class, 'detail_id', 'id')->orderBy('option');
    }

    public function actionDetails(){
        return $this->hasMany(ActionDetail::class, 'detail_id', 'id');
    }
}
