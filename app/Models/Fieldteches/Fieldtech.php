<?php

namespace App\Models\Fieldteches;

use App\Models\ListVendor\ListVendor;
use App\Models\Vendors\Vendor;
use App\SystemModels\Auth\User;
use App\Models\WorkOrders\WorkOrder;
use Illuminate\Database\Eloquent\Model;
use App\SystemModels\Globals\Upload;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\SystemModels\BaseModel;

class Fieldtech extends Model
{
    use SoftDeletes;
    use BaseModel;

    protected $table   = 'po_m_fieldtech';
    protected $guarded = ['id'];

    public function users()
    {
        return $this->hasMany(User::class, 'fieldtech_id', 'id');
    }

    public function vendor()
    {
        return $this->hasOne(Vendor::class, 'id', 'vendor_id');
    }

    public function workorders()
    {
        return $this->hasMany(WorkOrder::class, 'fieldtech_id', 'id');
    }

    public function listvendors()
    {
        return $this->hasOne(ListVendor::class, 'id', 'listvendor_id');
    }

    public function files()
    {
        return $this->belongsToMany(
            Upload::class,
            'po_m_fieldtech_attachment',
            'employ_id',
            'file_id'
        );
    }
}
