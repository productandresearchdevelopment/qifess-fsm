<?php

namespace App\SystemModels\Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\SystemModels\BaseModel;
use App\SystemModels\Projects\Clients;

class Role extends Model
{
    use BaseModel;
    use SoftDeletes;

    protected $softDelete   = true;
    protected $table   	    = 'auth_role';
    protected $hidden       = ['created_at', 'updated_at', 'deleted_at'];
    protected $guarded      = ['id', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by'];
    protected $casts        = ['properties' => 'object'];

    public function modules(){
        return $this->belongsToMany('App\SystemModels\Auth\Module', 'auth_role_module');
    }

    public function users(){
        return $this->hasMany(User::class);
    }

    public function module(){
        return $this->hasOne(Module::class, 'id', 'home');
    }

    public function hasRoute($routes){
        if($modules = $this->modules()->getResults()){
            foreach ($modules as $module) {
                if(is_array($routes)){
                    foreach ($routes as $route) {
                        if($module->route == $route) return true;
                    }
                }
                else if($module->route == $routes) {
                    return true;
                }
            }
        }
        return ($this->id < 10) ? true : false;
    }

    public function hasAuth($tags){
        if($modules = $this->modules()->getResults()){
            foreach ($modules as $module) {
                if(is_array($tags)){
                    foreach ($tags as $tag) {
                        if($module->auth == $tag) return true;
                    }
                }
                else if($module->auth == $tags) {
                    return true;
                }
            }
        }
        return false;
    }

}
