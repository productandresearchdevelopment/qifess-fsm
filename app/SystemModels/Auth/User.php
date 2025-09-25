<?php

namespace App\SystemModels\Auth;

use App\Models\Clients\Client;
use App\Models\Fieldteches\Fieldtech;
use App\Models\Vendors\Vendor;
use App\SystemModels\Globals\Upload;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\SystemModels\BaseModel;

class User extends Authenticatable
{
    use SoftDeletes;
    use BaseModel;

    protected $softDelete   = true;
    protected $guarded      = ['id', 'created_at', 'updated_at', 'deleted_at'];
    protected $hidden       = ['password', 'token'];
    protected $table        = 'auth_user';
    protected $dates           = ['deleted_at'];
    protected $casts        = ['activities' => 'array', 'owners' => 'array'];

    public $paternId        = 'uuid';
    public $modifyBy        = true;

    public function role()
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }

    public function lastModule()
    {
        return $this->hasOne(Module::class, 'id', 'last_module');
    }

    public function photoFile()
    {
        return $this->hasOne(Upload::class, 'id', 'photo');
    }

    public function hasRoute($routes)
    {
        if ($role = $this->role()->getResults()) {
            return $role->hasRoute($routes);
        }
        return false;
    }

    public function hasAuth($tags)
    {
        if ($role = $this->role()->getResults()) {
            return $role->hasAuth($tags);
        }
        return false;
    }

    // EXTEND RELATION --------------------------------------------------------

    public function vendor()
    {
        return $this->hasOne(Vendor::class, 'id', 'vendor_id');
    }

    public function client()
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }

    public function fieldtech()
    {
        return $this->hasOne(Fieldtech::class, 'id', 'fieldtech_id');
    }

    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'po_m_vendor_user', 'user_id', 'vendor_id');
    }

    public function listvendors()
    {
        return $this->hasOne(ListVendor::class, 'id', 'listvendor_id');
    }
}
