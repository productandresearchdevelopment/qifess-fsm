<?php

namespace App\Controllers\Parts;

use App\Libraries\ExportExcel;
use App\Http\Controllers\Controller;
use App\Libraries\Query;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\Models\WorkOrders\Part as Mod;
use Illuminate\Http\Request;
use App\Models\Sites\Site;
use App\Models\Clients\Client;
use App\Models\Vendors\Vendor;
use App\Models\WorkOrders\Masters AS Master;
use Illuminate\Support\Facades\App;


class Part extends Controller
{
    public function index(Request $request,$type=null){
        $user = $request->user();

        $params = [
            'user' => $user,
            'type' => $type,
            'sites' => Site::all(),
            'clients' => Client::all(),
            'activities' => Master\Activity::all(),
            'services' => Master\Service::all(),
            'vendors' => Vendor::all(),
            'title' => 'Part Data'
        ];
        return view('parts.main', $params);
    }

    public function data(Request $request, $type=null){
        $user = $request->user();
        $search = ['code', 'name','serial'];
        $query = Mod::with(['wo','files'])->where('type',$type);
        if($user->client_id)
        $query->whereHas('wo',function ($q) use ($user) {
             $q->where('client_id',$user->client_id);
        });
        if($filter = $request->input('filter-client'))
        $query->whereHas('wo',function ($q) use ($filter) {
             $q->where('client_id',$filter);
        });
        if($filter = $request->input('filter-site'))
        $query->whereHas('wo',function ($q) use ($filter) {
             $q->where('site_id',$filter);
        });
        return Query::open($query, $search);
    }


    public function push(Request $request, $id = null){
        DB::beginTransaction();
        try{
            $input = [
                'name' => $request->input('name'),
                'code' => $request->input('code'),
                'serial' => $request->input('serial'),
                'model' => $request->input('model'),
                'type' => $request->input('type'),
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

    public function exportExcel(Request $request){
        $user = $request->user();
        $search = $request->input("query");
        $type = $request->input("type");
        $title = "DATA ".strtoupper($type);
        $filter = "";
        if($f=$request->input("filter-client")) $filter.="AND B.client_id = '$f'";
        if($f=$request->input("filter-site")) $filter.="AND B.site_id = '$f'";
        $sql    = "SELECT A.*,CONCAT('(',D.name,') ',C.name) install_at
                   FROM po_wo_part A JOIN po_wo B ON A.wo_id = B.id JOIN po_m_site C
                   ON B.site_id = C.id JOIN po_m_client D ON C.client_id = D.id
                   WHERE A.type = '$type' AND (A.name LIKE '%$search%' OR A.code LIKE '%$search%' OR A.serial LIKE '%$search%')
                         $filter";
       $query = DB::select(DB::raw($sql));

        $columns = '[

                    {"text": "TYPE", "dataIndex": "type", "width": 80,"align" : "center"},
                    {"text": "PART NO", "dataIndex": "code", "width": 120,"align" : "center"},
                    {"text": "NAME", "dataIndex": "name", "width": 200},
                    {"text": "MODEL", "dataIndex": "model", "width": 100},
                    {"text": "SERIAL NO", "dataIndex": "serial", "width": 200},
                    {"text": "INSTALL AT", "dataIndex": "install_at", "width": 300},
                    {"text": "DESCRIPTION", "dataIndex": "description", "width": 250}

        ]'

        ;
        $params = array(
            "title" => [
                [$title, "h2"],
                ["Petro One Indonesia", "h3"],
            ],
            "columns" => json_decode($columns),
            "filename" => $title."-".date("Ymd"),
            "data" => $query,
            "footer" => array("Petro One", "Downloaded at (".date("d F Y H:i:s").")"),
        );

        $excel = new ExportExcel($params);
        $excel->run($params);
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
