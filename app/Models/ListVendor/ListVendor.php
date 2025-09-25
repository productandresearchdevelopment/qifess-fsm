<?php

namespace App\Models\ListVendor;

use App\SystemModels\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ListVendor extends Model
{
    use SoftDeletes;
    use BaseModel;

    protected $table = 'po_m_list_vendor';
    protected $guarded = ['id'];
}
