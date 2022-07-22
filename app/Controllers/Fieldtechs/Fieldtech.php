<?php

namespace App\Controllers\Fieldtechs;

use App\Http\Controllers\Controller;
use App\Libraries\FileUpload;
use App\Models\WorkOrders\Masters AS Master;
use App\Libraries\Query;
use Illuminate\Support\Facades\DB;
use App\SystemModels\Auth;
use App\Models\Fieldteches\Fieldtech as Mod;
use App\Models\Vendors\Vendor;
use Illuminate\Http\Request;

class Fieldtech extends Controller
{
    public function index(Request $request){
        $user = $request->user();

        $params = [
            'user' => $user,
            'vendors' => Vendor::all(),
            'activity' => Master\Activity::all(),
            'service' => Master\Service::all(),
            'title' => 'Fieldtech Data'
        ];
        return view('fieldtechs.main', $params);
    }

    public function data(Request $request){
        $user = $request->user();
        $search = ['nik', 'name'];
        $query = Mod::with(['users','files','workorders']);
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

            if($id) {
                $data = Mod::find($id);
                $data->update($input);
            }
            else {
                $data = Mod::create($input);
                $id = $data->id;
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
            Mod::whereIn('id', $data)->update(['deleted_at' => date('Y-m-d H:i:s'), 'deleted_by' => $user->id]);
            return ['success' => true, 'message' => 'Success!'];
        }
        return ['success' => false, 'message' => 'No Data!'];
    }
}
