<?php

namespace App\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Libraries\Query;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\Models\Services\Service as Mod;
use Illuminate\Http\Request;
use App\Models\Sites\Site;
use Illuminate\Support\Facades\App;


class Service extends Controller
{
    public function index(Request $request){
        $user = $request->user();

        $params = [
            'user' => $user,
            'services' => Mod::all(),
            'title' => 'Services Data'
        ];
        return view('services.main', $params);
    }

    public function data(Request $request){
        $user = $request->user();
        $search = ['alias', 'name'];
        $query = Mod::query();
        return Query::open($query, $search);
    }

    public function push(Request $request, $id = null){
        DB::beginTransaction();
        try{
            $input = [
                'name' => $request->input('name'),
                'color' => $request->input('color'),
                'alias' => $request->input('alias'),
                'description' => $request->input('description'),
            ];

            if($id) Mod::find($id)->update($input);
            else Mod::create($input);

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
