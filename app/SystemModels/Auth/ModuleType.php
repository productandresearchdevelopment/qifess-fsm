<?php

namespace App\SystemModels\Auth;

use Illuminate\Database\Eloquent\Model;

class ModuleType extends Model
{
    protected $table = 'auth_module_type';

    public function modules(){
        return $this->hasMany(Module::class, 'type', 'id');
    }
}
