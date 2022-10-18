<?php

namespace App\SystemModels;

use Auth;
use Illuminate\Support\Str;

trait BaseModel {
    private $model;

    protected static function boot(){
        parent::boot();

        static::creating(function ($model) {
            if(!$model->getKey()) {
                if(strtolower($model->paternId) == 'uuid'){
                    $model->{$model->getKeyName()} = (string) Str::uuid();
                }
                else if(strtolower($model->paternId) == 'prefix'){
                    $prefix  = (isset($model->prefix) && $model->prefix) ? $model->prefix : '';
                    $prefix .= date('Ym');
                    $data    = $model->select('id')->where('id', 'LIKE',  "$prefix%")->withTrashed()->orderBy('id', 'DESC')->first();
                    $no      = 1;
                    if($data){
                        $no = $data->id;
                        $no = str_replace($prefix, '', $no);
                        $no = $no + 1;
                    }
                    $model->{$model->getKeyName()} = $prefix . str_pad($no, 6, "0", STR_PAD_LEFT);
                }
            }


            if($model->timestamps && (!isset($model->modifyBy) || $model->modifyBy)){
                $user = Auth::user();
                $model->created_by = $user->id;
                $model->updated_by = $user->id;
            }
        });

        static::updating(function($model){
           if($model->timestamps && (!isset($model->modifyBy) || $model->modifyBy)){
                $user = Auth::user();
                $model->updated_by = ($user ? $user->id : null);
            }
        });

        static::deleting(function($model){
            if(!$model->forceDeleting && (!isset($model->modifyBy) || $model->modifyBy)) {
                $user = Auth::user();
                if(isset($model->deleted_by)) {
                    $model->deleted_by = $user->id;
                    $model->save();
                }
            }
        });
    }

    public function getIncrementing(){
        if(strtolower($this->paternId) == 'uuid') return false;
        else if(strtolower($this->paternId) == 'prefix') return false;
        return $this->incrementing;
    }

    public function getKeyType(){
        if(strtolower($this->paternId) == 'uuid') return 'string';
        return $this->keyType;
    }

}

