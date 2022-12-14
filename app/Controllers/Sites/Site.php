<?php

namespace App\Controllers\Sites;

use App\Libraries\ExportExcel;
use App\Http\Controllers\Controller;
use App\Libraries\Query;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\Models\Sites\Site as Mod;
use Illuminate\Http\Request;
use App\Models\Clients\Client;
use App\Models\Vendors\Vendor;
use App\Models\WorkOrders\Masters AS Master;
use App\Models AS Model;
use Illuminate\Support\Facades\App;


class Site extends Controller
{
    public function index(Request $request){
        $user = $request->user();

        $params = [
            "user" => $user,
            "sites" => Mod::all(),
            "activities" => Master\Activity::all(),
            "clients" => Client::all(),
            "vendors" => Vendor::all(),
            "services" => Master\Service::all(),
            "status" => Master\Status::with("details.options")->get(),
            "title" => "Sites Data"
        ];
        return view("sites.main", $params);
    }

    public function data(Request $request){
        $user = $request->user();
        $query = Mod::query();
        if($user->client_id) $query = $query->where("client_id", $user->client_id);
        if($filter = $request->input("filter-client")) $query = $query->where("client_id", $filter);
        if($filter = $request->input("filter-area")) $query = $query->where("vendor_id", $filter);
        if($filter = $request->input("filter-service")) $query = $query->where("service_id", $filter);
        if($filter = $request->input("filter-status")) $query = $query->where("is_active", $filter>1?0:1);
        if($search = $request->input("query")){
            $query->where(function($query) use ($search){
                $query->whereHas("client", function($query) use ($search){
                    $query->where("name", "LIKE", "%$search%");
                });
                $query->orwhere("name", "LIKE", "%$search%");
                $query->orwhere("address", "LIKE", "%$search%");
            });
        }
        $query->with(["workorders"]);
        $query->withCount(["workorders"]);
        return Query::open($query);
    }


    public function dataPublic(Request $request){
        $search = ['name', 'link_id', 'terminal_name'];
        $query = Mod::query();

        return Query::open($query, $search);
    }

    public function get(Request $request, $id = null){
        return Mod::find($id);
    }

    public function push(Request $request, $id = null){
        DB::beginTransaction();
        try{
            if(!$request->input("name")) return ["success" => false, "message" => "name Is Null"];
            if(!$request->input("client_id")) return ["success" => false, "message" => "client_id Is Null"];

            $input = [
                "name" => $request->input("name"),
                "client_id" => $request->input("client_id"),
                "vendor_id" => $request->input("vendor_id"),
                "link_id" => $request->input("link_id"),
                "pic" => $request->input("pic"),
                "pic_phone" => $request->input("pic_phone"),
                "pic_email" => $request->input("pic_email"),
                "address" => $request->input("address"),
                "lat" => $request->input("lat"),
                "long" => $request->input("long"),
                "description" => $request->input("description"),
                "is_active" => $request->input("active"),
                "active_date" => $request->input("active_date"),
                "terminal_name" => $request->input("terminal_name"),
                "beam" => $request->input("beam"),
                "airmac" => $request->input("airmac"),
                "serial_number" => $request->input("serial_number"),
                "service_id" => $request->input("service_id"),

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
            return ["success" => false, "message" => "500 ".$error->getMessage()];
        }

    }

    public function exportExcel(Request $request){
        $user = $request->user();
        $search = $request->input("query");
        $filter = "";
        if($f=$request->input("filter-client")) $filter.="AND B.id = '$f'";
        if($f=$request->input("filter-status")) $filter.="AND A.is_active = '$f'";
        $sql    = "SELECT A.*,IF(A.is_active,'ACTIVE','INACTIVE') AS status, B.name client_name,
                   C.name service_name
                   FROM po_m_site A JOIN po_m_client B ON A.client_id = B.id LEFT JOIN po_wo_m_service C
                   ON A.service_id = C.id
                   WHERE (B.name LIKE '%$search%' OR A.name LIKE '%$search%' OR A.address LIKE '%$search%')
                         $filter";
        $query = DB::select(DB::raw($sql));

        $columns = '[

                    {"text": "STATUS", "dataIndex": "status", "width": 80,"align" : "center"},
                    {"text": "CLIENT", "dataIndex": "client_name", "width": 150},
                    {"text": "LINK ID", "dataIndex": "link_id", "width": 120},
                    {"text": "NAME", "dataIndex": "name", "width": 200},
                    {"text": "SERVICES", "dataIndex": "service_name", "width": 120,"align" : "center"},
                    {"text": "TERMINAL NAME", "dataIndex": "terminal_name", "width": 150},
                    {"text": "BEAM", "dataIndex": "beam", "width": 80},
                    {"text": "AIRMAC", "dataIndex": "airmac", "width": 120},
                    {"text": "SERIAL NUMBER", "dataIndex": "serial_number", "width": 120},
                    {"text": "ADDRESS", "dataIndex": "address", "width": 200},
                    {"text": "ACTIVE DATE", "dataIndex": "active_date","type":"date", "width": 100},
                    {"text": "LATITUDE", "dataIndex": "lat","type":"float", "width": 100},
                    {"text": "LONGITUDE", "dataIndex": "long","type":"float", "width": 100},
                    {"text": "PIC", "dataIndex": "pic", "width": 200},
                    {"text": "PIC PHONE", "dataIndex": "pic_phone", "width": 100},
                    {"text": "PIC EMAIL", "dataIndex": "pic_email", "width": 150},
                    {"text": "DESCRIPTION", "dataIndex": "description", "width": 300}

        ]'

        ;
        $params = array(
            "title" => [
                ["DATA SITE", "h2"],
                ["Petro One Indonesia", "h3"],
            ],
            "columns" => json_decode($columns),
            "filename" => "SITE-".date("Ymd"),
            "data" => $query,
            "footer" => array("Petro One", "Downloaded at (".date("d F Y H:i:s").")"),
        );

        $excel = new ExportExcel($params);
        $excel->run($params);
    }

    public function delete(Request $request){
        $user = $request->user();
        if($data = json_decode($request->data)) {
            Mod::whereIn("id", $data)->update(["deleted_at" => date("Y-m-d H:i:s"), "deleted_by" => $user->id]);
            return ["success" => true, "message" => "Success!"];
        }
        return ["success" => false, "message" => "No Data!"];
    }
}
