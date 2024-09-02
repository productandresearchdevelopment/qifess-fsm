<?php

namespace App\Models\WorkOrders\Masters;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $table   = 'po_wo_m_status';
    protected $casts = [
        'roles' => 'array',
        'send_email_roles' => 'array',
        'activities' => 'array',
        'show_on' => 'array'
    ];

    public function details(){
        return $this->hasMany(StatusDetail::class, 'status_id', 'id')
            ->orderBy('group')
            ->orderBy('sort');
    }

    public static function getStatusOpen($activityId){
        $status = static::where('type', 0)->get();
        foreach ($status AS $sts){
            foreach ($sts->activities AS $activity){
                if($activity == $activityId) return $sts;
            }
        }
        return null;
    }
}
