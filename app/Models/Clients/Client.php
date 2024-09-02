<?php

namespace App\Models\Clients;

use App\Models\Sites\Site;
use App\Models\WorkOrders\WorkOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\SystemModels\BaseModel;

class Client extends Model
{
    use SoftDeletes;
    use BaseModel;

    protected $table   = 'po_m_client';
    protected $guarded = ['id'];

    public function sites(){
        return $this->hasMany(Site::class, 'client_id', 'id');
    }

    public function workorders(){
        return $this->hasMany(WorkOrder::class, 'client_id', 'id');
    }
}
