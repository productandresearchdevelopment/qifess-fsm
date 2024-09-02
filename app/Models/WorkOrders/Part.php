<?php

namespace App\Models\WorkOrders;

use App\SystemModels\Auth\User;
use App\SystemModels\Globals\Upload;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\SystemModels\BaseModel;

class Part extends Model
{
    use SoftDeletes;
    use BaseModel;

    protected $table   = 'po_wo_part';
    protected $guarded = ['id'];

    public $paternId   = 'uuid';

    public function wo(){
        return $this->hasOne(WorkOrder::class, 'id', 'wo_id');
    }

    public function files(){
        return $this->belongsToMany(
            Upload::class,
            'po_wo_part_file',
            'part_id',
            'file_id'
        );
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
