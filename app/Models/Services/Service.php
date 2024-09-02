<?php

namespace App\Models\Services;

use App\Models\WorkOrders\WorkOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\SystemModels\BaseModel;

class Service extends Model
{
    use SoftDeletes;
    use BaseModel;

    protected $table   = 'po_wo_m_service';
    protected $guarded = ['id'];


    public function workorders(){
        return $this->hasMany(WorkOrder::class, 'site_id', 'id');
    }
}
