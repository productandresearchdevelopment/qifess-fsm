<?php

namespace App\SystemModels\Globals;

use App\SystemModels\BaseModel;
use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    use BaseModel;

    protected $table = 'uploads';
    protected $guarded = ['created_at', 'updated_at'];
    public $paternId = 'uuid';
    public $modifyBy = false;

}
