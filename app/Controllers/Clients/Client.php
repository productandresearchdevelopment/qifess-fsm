<?php

namespace App\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Libraries\Query;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\Models\Clients\Client as Mod;
use Illuminate\Http\Request;
use App\Models\Sites\Site;
use App\Models\Vendors\Vendor;
use App\Models\WorkOrders\Masters AS Master;
use Illuminate\Support\Facades\App;


class Client extends Controller
{
    public function index(Request $request){
        $user = $request->user();
        $params = [
            'user' => $user,
            'activities' => Master\Activity::all(),
            'services' => Master\Service::all(),
            'vendors' => Vendor::all(),
            'title' => 'Clients Data'
        ];
        return view('clients.main', $params);
    }

    public function data(Request $request){
        $user = $request->user();
        $search = ['id', 'name', 'address','alias','customer_id'];
        $query = Mod::query();
        $query ->withCount(['workorders']);
        $query ->withCount(['sites']);
        if($user->client_id) $query = $query->where('id', $user->client_id);
        return Query::open($query, $search);
    }

    public function dataPublic(Request $request){
        $search = ['name', 'alias', 'customer_id'];
        $query = Mod::query();
        return Query::open($query, $search, false, 20);
    }

    public function get(Request $request, $id = null){
        return Mod::find($id);
    }

    public function push(Request $request, $id = null){
        DB::beginTransaction();
        try{
            if(!$request->input('customer_id'))  return ['success' => false, 'message' => 'customer_id Is Null'];
            else if(!$request->input('name'))  return ['success' => false, 'message' => 'name Is Null'];
            else if(!$request->input('alias'))  return ['success' => false, 'message' => 'alias Is Null'];

            $input = [
                'customer_id' => $request->input('customer_id'),
                'name' => $request->input('name'),
                'phone' => $request->input('phone'),
                'address' => $request->input('address'),
                'alias' => $request->input('alias'),
                'email' => $request->input('email'),
                'description' => $request->input('description'),
            ];

            if($id) {
                $data = Mod::find($id);
                $data->update($input);
            }
            else $data = Mod::create($input);

            DB::commit();
            return ['success' => true, 'message' => 'Success...', 'data' => $data];
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
