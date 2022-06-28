<?php

namespace App\Controllers\Fieldtechs;

use App\Mail\ActiveUser;
use App\Http\Controllers\Controller;
use App\Libraries\FileUpload;
use App\Models\Sites\Site;
use App\Models\WorkOrders\Masters AS Master;
use App\Libraries\Query;
use App\Models\Clients\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\SystemModels\Auth;
use App\Models\Fieldteches\Fieldtech as Mod;
use App\Models\Vendors\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;


class Fieldtech extends Controller
{
    public function index(Request $request){
        $user = $request->user();

        $params = [
            'user' => $user,
            'vendors' => Vendor::all(),
            'title' => 'Fieldtech Data'
        ];
        return view('fieldtechs.main', $params);
    }

    public function data(Request $request){
        $user = $request->user();
        $search = ['nik', 'name'];
        $query = Mod::with(['user','files','workorders']);
        $query ->withCount(['workorders']);
        if($user->vendor_id) $query = $query->where('vendor_id', $user->vendor_id);
        if($filter = $request->input('filter-vendor')) $query = $query->where('vendor_id', $filter);
        return Query::open($query, $search);
    }

    public function push(Request $request, $id = null){
        DB::beginTransaction();
        try{
            $photo = FileUpload::upload('photo', 'fieldtech');

            $input = [
                'nik' => $request->input('nik'),
                'name' => $request->input('name'),
                'phone' => $request->input('phone'),
                'address' => $request->input('address'),
                'email' => $request->input('email'),
                'vendor_id' => $request->input('vendor_id'),
            ];
            if($photo)$input['photo']=$photo;
            if($id) {
                $data = Mod::find($id);
                $data->update($input);
            }
            else {
                $data = Mod::create($input);
                $id = $data->id;
            }
            $input_user = [
                'username' => $request->input('username'),
                'name' => $request->input('name'),
                'phone' => $request->input('phone'),
                'email' => $request->input('email'),
                'fieldtech_id' => $id,
                'role_id' => 1110,
                'photo' => $photo,
                'vendor_id' => $request->input('vendor_id'),
            ];
            if($password = $request->input('password'))$input_user['password'] = Hash::make($password);
            if($user= Auth\User::where('fieldtech_id', $id)->first()){
                if(Auth\User::where('email', $input_user['email'])->where('id','<>',$user->id)->withTrashed()->first()){
                    return ['success' => false, 'message' => 'Email Duplicate'];
                }
                else if(Auth\User::where('username', $input_user['username'])->where('id','<>',$user->id)->withTrashed()->first()){
                    return ['success' => false, 'message' => 'Username Duplicate'];
                }
                $user->update($input_user);

            }
            else {
                if(Auth\User::where('email', $input_user['email'])->withTrashed()->first()){
                    return ['success' => false, 'message' => 'Email Duplicate'];
                }
                else if(Auth\User::where('username', $input_user['username'])->withTrashed()->first()){
                    return ['success' => false, 'message' => 'Username Duplicate'];
                }
                $user = Auth\User::create($input_user);

            }
            if($files = $request->input('attachment')){
                $data->files()->detach();
                    $files = json_decode($files);
                    foreach ($files AS $file) {
                        if (is_object($file)) $data->files()->attach($file->id);
                        else if($fid = FileUpload::push($file, 'fieldtech-attachment')){
                            $data->files()->attach($fid);
                        }
                    }

            }

            if($password){
                Mail::to($user->email)->send(new ActiveUser($user, $password));
            }
            DB::commit();
            return ['success' => true, 'message' => 'Success...'];
        }
        catch(Exception $error){
            DB::rollback();
            return ['success' => false, 'message' => '500 '.$error->getMessage()];
        }

    }

    public function delete(Request $request){
        $user = $request->user();
        if($data = json_decode($request->data)) {
            $query = Auth\User::where('fieldtech_id', $data)->first();
            Mod::whereIn('id', $data)->update(['deleted_at' => date('Y-m-d H:i:s'), 'deleted_by' => $user->id]);
            Auth\User::whereIn('id', $query)->update(['deleted_at' => date('Y-m-d H:i:s'), 'deleted_by' => $user->id]);
            return ['success' => true, 'message' => 'Success!'];
        }
        return ['success' => false, 'message' => 'No Data!'];
    }
}
