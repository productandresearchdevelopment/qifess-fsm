<?php

namespace App\Models\WorkOrders;

use App\Models\WorkOrders\Masters\Status;
use App\Models\WorkOrders\Masters\StatusDetail;
use App\SystemModels\Auth\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\SystemModels\BaseModel;

class Action extends Model
{
    use SoftDeletes;
    use BaseModel;

    protected $table   = 'po_wo_action';
    protected $guarded = ['id'];

    public $paternId   = 'uuid';

    public function wo(){
        return $this->hasOne(WorkOrder::class, 'id', 'wo_id');
    }

    public function status(){
        return $this->hasOne(Status::class, 'id', 'status_id');
    }

    public function details(){
        return $this->hasMany(ActionDetail::class, 'action_id', 'id');
    }

    public function createdBy(){
        return $this->hasOne(User::class, 'id', 'created_by')->withTrashed();
    }

    public function updatedBy(){
        return $this->hasOne(User::class, 'id', 'updated_by');
    }

    public function deletedBy(){
        return $this->hasOne(User::class, 'id', 'deleted_by');
    }
}
