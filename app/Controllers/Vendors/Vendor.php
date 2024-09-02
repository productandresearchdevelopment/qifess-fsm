<?php

namespace App\Controllers\Vendors;

use App\Http\Controllers\Controller;
use App\Libraries\FileUpload;
use App\Libraries\Query;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\Models\Vendors\Vendor as Mod;
use Illuminate\Http\Request;
use App\Models AS Model;
use App\Models\WorkOrders\Masters AS Master;
use App\Models\Sites\Site;
use App\Models\Clients\Client;
use Illuminate\Support\Facades\App;


class Vendor extends Controller
{
    public function index(Request $request){
        $user = $request->user();

        $params = [
            'user' => $user,
            'title' => 'Area'
        ];
        return view('vendors.main', $params);
    }

    public function data(Request $request){
        $search = ['name', 'address'];
        $query = Mod::with(['files']);
        $query ->withCount(['workorders']);
        $query ->withCount(['fieldteches']);
        return Query::open($query, $search);
    }

    public function push(Request $request, $id = null){
        DB::beginTransaction();
        try{
            $input = [
                'name' => $request->input('name'),
                'alias' => $request->input('alias'),
                'phone' => $request->input('phone'),
                'address' => $request->input('address'),
                'email' => $request->input('email'),
                'description' => $request->input('description'),
            ];

            if($id) {
                $data = Mod::find($id);
                $data->update($input);
            }
            else {
                $data = Mod::create($input);
                $id = $data->id;
            }

            if($files = $request->input('attachment')){
                $data->files()->detach();
                    $files = json_decode($files);
                    foreach ($files AS $file) {
                        if (is_object($file)) $data->files()->attach($file->id);
                        else if($fid = FileUpload::push($file, 'vendor-attachment')){
                            $data->files()->attach($fid);
                        }
                    }

            }

            DB::commit();
            return ['success' => true, 'message' => 'Success...'];
        }
        catch(QueryException $error){
            DB::rollback();
            return ['success' => false, 'message' => '500 '.$error->getMessage()];
        }

    }


    public function delete(Request $request){
        $user = $request->user();
        if($data = json_decode($request->data)) {
            Mod::whereIn('id', $data)->update(['deleted_at' => date('Y-m-d H:i:s'), 'deleted_by' => $user->id]);
            return ['success' => true, 'message' => 'Success!'];
        }
        return ['success' => false, 'message' => 'No Data!'];
    }
}
