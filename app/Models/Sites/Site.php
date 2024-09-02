<?php

namespace App\Models\Sites;

use App\Models\Clients\Client;
use App\Models\Services\Service;
use App\Models\Vendors\Vendor;
use App\Models\WorkOrders\WorkOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\SystemModels\BaseModel;

class Site extends Model
{
    use SoftDeletes;
    use BaseModel;

    protected $table   = 'po_m_site';
    protected $guarded = ['id'];

    public function client(){
        return $this->hasOne(Client::class, 'id', 'client_id')->withTrashed();
    }

    public function service(){
        return $this->hasOne(Service::class, 'id', 'service_id')->withTrashed();
    }

    public function vendor(){
        return $this->hasOne(Vendor::class, 'id', 'vendor_id')->withTrashed();
    }

    public function workorders(){
        return $this->hasMany(WorkOrder::class, 'site_id', 'id');
    }
}
