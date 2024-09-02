<?php

namespace App\Models\WorkOrders;

use App\Models\Clients\Client;
use App\Models\Owners\Owner;
use App\Models\Fieldteches\Fieldtech;
use App\Models\Sites\Site;
use App\Models\Vendors\Vendor;
use App\Models\WorkOrders\Masters\Activity;
use App\Models\WorkOrders\Masters\Service;
use App\Models\WorkOrders\Masters\Slot;
use App\SystemModels\Auth\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\SystemModels\BaseModel;

class WorkOrder extends Model
{
    use SoftDeletes;
    use BaseModel;

    protected $table   = 'po_wo';
    protected $guarded = ['id'];

    public $paternId = 'prefix';

    public function actions(){
        return $this->hasMany(Action::class, 'wo_id', 'id')->orderBy('created_at');
    }

    public function parts(){
        return $this->hasMany(Part::class, 'wo_id', 'id')->orderBy('created_at');
    }

    public function lastAction(){
        return $this->hasOne(Action::class, 'id', 'last_action');
    }

    public function owner(){
        return $this->hasOne(Owner::class, 'id', 'owner_id');
    }

    public function site(){
        return $this->hasOne(Site::class, 'id', 'site_id')->withTrashed();
    }

    public function removeSite(){
        return $this->hasOne(Site::class, 'id', 'remove_site_id')->withTrashed();
    }

    public function activity(){
        return $this->hasOne(Activity::class, 'id', 'activity_id');
    }

    public function service(){
        return $this->hasOne(Service::class, 'id', 'service_id');
    }

    public function client(){
        return $this->hasOne(Client::class, 'id', 'client_id')->withTrashed();
    }

    public function vendor(){
        return $this->hasOne(Vendor::class, 'id', 'vendor_id')->withTrashed();
    }

    public function slot(){
        return $this->hasOne(Slot::class, 'id', 'slot_id');
    }

    public function fieldtech(){
        return $this->hasOne(Fieldtech::class, 'id', 'fieldtech_id')->withTrashed();
    }

    public function createdBy(){
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function updatedBy(){
        return $this->hasOne(User::class, 'id', 'updated_by');
    }

    public function deletedBy(){
        return $this->hasOne(User::class, 'id', 'deleted_by');
    }
}
