<?php

namespace App\Models\WorkOrders\Masters;

use Illuminate\Database\Eloquent\Model;

class Slot extends Model
{
    protected $table = 'po_wo_m_slot';
    protected $guarded = ['id'];
}
