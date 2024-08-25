<?php

namespace App\Http\Controllers\Systems;

use App\Mail\ActiveUser;
use App\Models\Clients\Client;
use App\Models\Fieldteches\Fieldtech;
use App\Models\Owners\Owner;
use App\Models\Vendors\Vendor;
use App\Models\WorkOrders\Masters\Activity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SystemModels\Auth;
use App\Libraries\Query;
use Illuminate\Support\Facades\Mail;

class User extends Controller
{
    public function index(Request $request){
        $user   = $request->user();
        $view   = 'systems.users.main';

        if($user->vendors && count($user->vendors)){
            $vendors = $user->vendors;
        }
        else $vendors = Vendor::orderBy('name')->get();

        $params = [
            'user' => $user,
            'roles' => Auth\Role::where('id', '>=' , $user->role_id)->get(),
            'activities' => Activity::all(),
            'owners' => Owner::all(),
            'clients' => $user->client_id ? Client::where('id', $user->client_id)->get() : Client::all(),
            'vendors' => $vendors,
        ];
        return view($view, $params);
    }

    public function data(Request $request){
        $user   = $request->user();
        $search = ['id', 'name', 'username', 'last_ip', 'email', 'phone'];
        $query  = Auth\User::with('fieldtech', 'vendors');

        $query->where('role_id', '>=', $user->role_id);

        if($user->client_id) $query->where('client_id', '>=', $user->client_id);
        if($user->vendor_id) $query->where('vendor_id', '>=', $user->vendor_id);

        if($filter = $request->role) $query->where('role_id', $filter);
        if($filter = $request->client) $query->where('client_id', $filter);
        if($filter = $request->vendor) $query->where('vendor_id', $filter);

        if(!$request->trash) $query->withTrashed();
        if($request->trash == 2) $query->onlyTrashed();

        return Query::open($query, $search);
    }

    public function dataFieldtech(Request $request){
        $query = Fieldtech::where('vendor_id', $request->vendor);
        return Query::open($query, ['nik','name'], false);
    }

    public function push(Request $request, $id = null){
        DB::beginTransaction();
        try{
            $input = [
                'role_id' => $request->input('role_id'),
                'username' => $request->input('username'),
                'email' => $request->input('email') ?: null,
                'name' => $request->input('name'),
                'phone' => $request->input('phone'),
                'description' => $request->input('description'),
                'role_id' => $request->input('role_id'),
                'vendor_id' => ($val = $request->input('vendor_id')) ? $val : null,
                'client_id' => ($val = $request->input('client_id')) ? $val : null,
                'fieldtech_id' => ($val = $request->input('fieldtech_id')) ? $val : null,
                'owners' => null,
                'activities' => null,
            ];

            if(($val = $request->input('owners')) && count($val) && $val[0]) {
                $val = array_map(function($value) { return intval($value); }, $val);
                $input['owners'] = $val;
            }
            if(($val = $request->input('activities')) && count($val) && $val[0]) {
                $val = array_map(function($value) { return intval($value); }, $val);
                $input['activities'] = $val;
            }

            if($id){
                if(!$input['email'] && Auth\User::where('email', $input['email'])->where('id','<>',$id)->withTrashed()->first()){
                    return ['success' => false, 'message' => 'Email Duplicate'];
                }
                else if(Auth\User::where('username', $input['username'])->where('id','<>',$id)->withTrashed()->first()){
                    return ['success' => false, 'message' => 'Username Duplicate'];
                }
                if($password = $request->password) $input['password'] = Hash::make($password);
                $user = Auth\User::find($id);
                $user->update($input);
            }
            else {
                if($input['email'] && Auth\User::where('email', $input['email'])->withTrashed()->first()){
                    return ['success' => false, 'message' => 'Email Duplicate'];
                }
                else if(Auth\User::where('username', $input['username'])->withTrashed()->first()){
                    return ['success' => false, 'message' => 'Username Duplicate'];
                }
                if($password = $request->password) $input['password'] = Hash::make($password);
                else return ['success' => false, 'message' => 'Password Is Null'];
                $user = Auth\User::create($input);
            }

            if($vendors = json_decode($request->input('vendors'))){
                if(count($vendors)){
                    $user->vendors()->sync($vendors);
                }
            }


            if($password){
                //Mail::to($user->email)->send(new ActiveUser($user, $password));
            }


            DB::commit();
            return ['success' => true, 'message' => 'Success...'];
        }
        catch(Exception $error){
            DB::rollback();
            return ['success' => false, 'message' => '500 '.$error->getMessage()];
        }

    }

    public function setRole(Request $request){
        if($data = json_decode($request->data)){
            Auth\User::whereIn('id', $data)->update(['role_id' => $request->role_id]);
            return ['success' => true, 'message' => 'Success!'];
        }
        return ['success' => false, 'message' => 'No Data!'];
    }

    public function setPassword(Request $request){
        if($data = json_decode($request->data)){
            Auth\User::whereIn('id', $data)->update(['password' => Hash::make($request->password)]);
            return ['success' => true, 'message' => 'Success!'];
        }
        return ['success' => false, 'message' => 'No Data!'];
    }

    public function restore(Request $request){
        if($data = json_decode($request->data)){
            Auth\User::withTrashed()->whereIn('id', $data)->restore();
            return ['success' => true, 'message' => 'Success!'];
        }
        return ['success' => false, 'message' => 'No Data!'];
    }

    public function forceDelete(Request $request){
        if($data = json_decode($request->data)){
            Auth\User::withTrashed()->whereIn('id', $data)->forcedelete();
            return ['success' => true, 'message' => 'Success!'];
        }
        return ['success' => false, 'message' => 'No Data!'];
    }

    public function delete(Request $request){
        $user = $request->user();
        if($data = json_decode($request->data)) {
            Auth\User::whereIn('id', $data)->update(['deleted_at' => date('Y-m-d H:i:s'), 'deleted_by' => $user->id]);
            return ['success' => true, 'message' => 'Success!'];
        }
        return ['success' => false, 'message' => 'No Data!'];
    }

}
