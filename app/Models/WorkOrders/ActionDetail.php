<?php

namespace App\Models\WorkOrders;

use App\Models\Fieldteches\Fieldtech;
use App\Models\WorkOrders\Masters\Slot;
use App\Models\WorkOrders\Masters\StatusDetail;
use App\Models\WorkOrders\Masters\StatusDetailOption;
use App\SystemModels\Globals\Upload;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\SystemModels\BaseModel;

class ActionDetail extends Model
{
    use BaseModel;

    protected $table   = 'po_wo_action_detail';
    protected $guarded = ['id'];
    protected $casts = ['value' => 'object'];

    public $timestamps = false;
    public $paternId   = 'uuid';

    public function action(){
        return $this->hasOne(Action::class, 'id', 'action_id');
    }

    public function detail(){
        return $this->hasOne(StatusDetail::class, 'id', 'detail_id');
    }

    public function valueOption(){
        return $this->hasOne(StatusDetailOption::class, 'id', 'value');
    }

    public function fieldtech(){
        return $this->hasOne(Fieldtech::class, 'id', 'value')->withTrashed();
    }

    public function slot(){
        return $this->hasOne(Slot::class, 'id', 'value');
    }

    public function files(){
        return $this->belongsToMany(
            Upload::class,
            'po_wo_action_detail_file',
            'detail_id',
            'file_id'
        );
    }
}
