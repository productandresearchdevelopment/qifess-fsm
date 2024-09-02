<?php

namespace App\Models\WorkOrders\Masters;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\SystemModels\BaseModel;

class Service extends Model
{
    use SoftDeletes;
    use BaseModel;

    protected $table = 'po_wo_m_service';
    protected $guarded = ['id'];
}
