<?php

namespace App\Models\Vendors;

use App\Models\Fieldteches\Fieldtech;
use App\Models\WorkOrders\WorkOrder;
use App\SystemModels\Auth\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\SystemModels\Globals\Upload;
use App\SystemModels\BaseModel;

class Vendor extends Model
{
    use SoftDeletes;
    use BaseModel;

    protected $table   = 'po_m_vendor';
    protected $guarded = ['id'];

    public function fieldteches(){
        return $this->hasMany(Fieldtech::class, 'vendor_id', 'id');
    }

    public function workorders(){
        return $this->hasMany(WorkOrder::class, 'vendor_id', 'id');
    }

    public function users(){
        return $this->belongsToMany(User::class, 'po_m_vendor_user', 'vendor_id', 'user_id');
    }

    public function files(){
        return $this->belongsToMany(
            Upload::class,
            'po_m_vendor_attachment',
            'vendor_id',
            'file_id'
        );
    }

}
