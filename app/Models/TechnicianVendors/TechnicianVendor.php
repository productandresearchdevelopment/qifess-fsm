<?php

namespace App\Models\TechnicianVendors;

use App\Models\ListVendor\ListVendor;
use App\SystemModels\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TechnicianVendor extends Model
{
    use SoftDeletes;
    use BaseModel;

    protected $table = 'po_m_technician_vendor';
    protected $guarded = ['id'];

    public function listvendors()
    {
        return $this->hasOne(ListVendor::class, 'id', 'listvendor_id');
    }
}
