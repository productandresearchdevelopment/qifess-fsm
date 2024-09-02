<?php

namespace App\Models\Owners;

use App\Models\Sites\Site;
use App\Models\WorkOrders\WorkOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\SystemModels\BaseModel;

class Owner extends Model
{
    protected $table   = 'po_m_owner';
    protected $guarded = ['id'];

    public function workorders(){
        return $this->hasMany(WorkOrder::class, 'owner_id', 'id');
    }
}
