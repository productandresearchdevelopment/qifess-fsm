<?php

namespace App\Controllers\Samples;

use Illuminate\Database\QueryException;
use PDF;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libraries\Query;
use App\Models\Samples\Sample AS Mod;


class Sample extends Controller
{
    public function index(Request $request){
        $user = $request->user();
        $view   = isMobile() ? 'samples.mobile.main' : 'samples.main';
        $params = ['user' => $user, 'title' => 'Sample'];
        return view($view, $params);
    }

    public function data(Request $request){
        $search = ['name', 'phone'];
        $query  = Mod::query();
        return Query::open($query, $search);
    }

    public function export(Request $request){
        $html = view("samples.pdf");
        $pdf = PDF::loadHTML($html);
        return $pdf->stream();

    }

    public function push(Request $request, $id = null){
        DB::beginTransaction();
        try{
            $input = [
                'name' => $request->input('name'),
                'phone' => $request->input('phone'),
                'address' => $request->input('address'),
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
        if($data = json_decode($request->data)) {
            Mod::whereIn('id', $data)->delete();
            return ['success' => true, 'message' => 'Success!'];
        }
        return ['success' => false, 'message' => 'No Data!'];
    }

}
