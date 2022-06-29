<?php

namespace App\Controllers\WorkOrders;

use App\Libraries\ExportExcel;
use App\Libraries\FileUpload;
use App\Mail\UpdateWoMail;
use App\Models\Fieldteches\Fieldtech;
use App\Models\Owners\Owner;
use App\Models\WorkOrders\Part;
use App\SystemModels\Auth\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libraries\Query;
use App\Models\WorkOrders\WorkOrder AS Wo;
use App\Models\WorkOrders\Action;
use App\Models\WorkOrders\ActionDetail;
use App\Models\Sites\Site;
use App\Models\Clients\Client;
use App\Models\Vendors\Vendor;
use App\Models\WorkOrders\Masters AS Master;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Ixudra\Curl\Facades\Curl;

class WorkOrder extends Controller
{
    public function index(Request $request, $archive = false){
        $user   = $request->user();
        $view   = isMobile() ? 'workorders.mobile.main' : 'workorders.main';
        $params = $this->getParams($request, ['archive' => $archive]);
        return view($view, $params);
    }

    public function form(Request $request, $id=null){
        $user = $request->user();
        $view   = 'workorders.form';
        $params = $this->getParams($request);;
        return view($view, $params);
    }

    public function detail(Request $request, $id=null){
        $params = $this->getParams($request, ['data' => $this->get($request, $id)]);
        return view('workorders.detail', $params);
    }

    private function getParams($request, $params = null){
        $user = $request->user();
        $result = [
            'user' => $user,
            'activities' => ($ftr = $user->activities) ? Master\Activity::whereIn('id',$ftr)->get() : Master\Activity::all(),
            'clients' => Client::all(),
            'owners' => ($ftr = $user->owners) ? Owner::whereIn('id',$ftr)->get() : Owner::all(),
            'vendors' => Vendor::all(),
            'services' => Master\Service::all(),
            'status' => Master\Status::with('details.options')->get(),
        ];
        if($params) $result = array_merge($result, $params);
        return $result;
    }

    public function archive(Request $request){
        return $this->index($request, true);
    }

    public function data(Request $request, $archive=false){
        $user = $request->user();
        $query = Wo::with([
            'site',
            'client',
            'removeSite',
            'fieldtech',
            'lastAction.details.files',
            'lastAction.createdBy',
            'lastAction.updatedBy',
            'lastAction.deletedBy',
            'actions.details.files',
            'actions.details.fieldtech',
            'actions.createdBy',
            'actions.updatedBy',
            'actions.deletedBy',
            'parts.files',
            'createdBy',
            'updatedBy',
            'deletedBy'
        ]);

        // FILTER ON GOING ---------------------------------------------------------------------------------------------
        if($archive) $query->whereNotNull('close_date');
        else {
            $query->where(function ($query) {
                $query->whereNull('close_date');
                $query->orWhere('close_date', '>=', date('Y-m-d', strtotime('-7 days')));
            });
        }

        // FILTER BY USER AUTH -----------------------------------------------------------------------------------------
        if($ftr = $user->owners) $query->whereIn('owner_id', $ftr);
        if($ftr = $user->activities) $query->whereIn('activity_id', $ftr);
        if($ftr = $user->client_id) $query->where('client_id', $ftr);
        if($ftr = $user->vendor_id) $query->where('vendor_id', $ftr);
        if($ftr = $user->fieldtech_id) $query->where('fieldtech_id', $ftr);

        // FILTER ------------------------------------------------------------------------------------------------------
        if($ftr = $request->input('filter-status')){
            $query->whereHas('lastAction', function($query) use ($ftr){
                $query->where('status_id', $ftr);
            });
        }
        if($ftr = $request->input('filter-activity')) $query->where('activity_id', $ftr);
        if($ftr = $request->input('filter-service')) $query->where('service_id', $ftr);
        if($ftr = $request->input('filter-client')) $query->where('client_id', $ftr);
        if($ftr = $request->input('filter-owner')) $query->where('owner_id', $ftr);
        if($ftr = $request->input('filter-vendor')) $query->where('vendor_id', $ftr);

        // SEARCH ------------------------------------------------------------------------------------------------------
        $search = $request->input('query');
        if($search) {
            $query->where(function ($query) use ($search) {
                $query->orWhereHas('site', function($query) use ($search){
                    $query->where('name', 'LIKE', "%$search%");
                });
                $query->orWhereHas('fieldtech', function($query) use ($search){
                    $query->where('name', 'LIKE', "%$search%");
                });
                $query->orWhere('id', 'LIKE', "%$search");
                $query->orWhere('no_wo', 'LIKE', "%$search");
                $query->orWhere('description', 'LIKE', "%$search%");
            });
        }

        // DEFAULT SORT ------------------------------------------------------------------------------------------------
        if(!$request->input('sort')) $query->orderBy('updated_at','DESC');

        return Query::open($query);
    }

    public function get(Request $request, $id=null){
        return Wo::with([
            'site',
            'removeSite',
            'fieldtech',
            'lastAction.details.files',
            'lastAction.createdBy',
            'lastAction.updatedBy',
            'lastAction.deletedBy',
            'actions.details.files',
            'actions.details.fieldtech',// => function($query){ $query->where('id', 1); },
            'actions.createdBy',
            'actions.updatedBy',
            'actions.deletedBy',
            'parts.files',
            'createdBy',
            'updatedBy',
            'deletedBy'
        ])->find($id);
    }

    public function getPublic(Request $request, $id=null){
        return Wo::find($id);
    }

    public function dataArchive(Request $request){
        return $this->data($request, true);
    }

    public function dataSite(Request $request){
        $query = Site::query();
        return Query::open($query, ['id','name','link_id'], false);
    }

    public function dataFieldtech(Request $request){
        $query = Fieldtech::where('vendor_id', $request->vendor);
        return Query::open($query, ['id','name'], false);
    }

    public function push(Request $request, $id = null){
        DB::beginTransaction();
        try{
            if(!$request->input('remove_site_id') && !$request->input('site_id')) return ['success' => false, 'message' => 'site_id OR remove_site_id Is Null'];
            if(!$request->input('activity_id')) return ['success' => false, 'message' => 'activity_id Is Null'];
            if(!$request->input('client_id')) return ['success' => false, 'message' => 'client_id Is Null'];
            //if(!$request->input('service_id')) return ['success' => false, 'message' => 'service_id Is Null'];
            //if(!$request->input('owner_id')) return ['success' => false, 'message' => 'owner_id Is Null'];
            if(!$request->input('description')) return ['success' => false, 'message' => 'description Is Null'];
            //if(!$request->input('no_wo')) return ['success' => false, 'message' => 'no_wo Is Null'];

            $input = [
                'site_id' => $request->input('site_id'),
                'remove_site_id' => $request->input('remove_site_id'),
                'activity_id' => $request->input('activity_id'),
                'vendor_id' => $request->input('vendor_id'),
                'client_id' => $request->input('client_id'),
                'service_id' => $request->input('service_id'),
                'owner_id' => $request->input('owner_id'),
                'description' => $request->input('description'),
                'no_wo' => $request->input('no_wo'),
                'start_date' => $request->input('start_date'),
                'expire_date' => $request->input('expire_date'),
            ];

            if($id){
                if($wo = Wo::find($id)) {
                    $wo->update($input);
                    $actionId = $wo->actions()->first()->id;
                }
                else return ['success' => false, 'message' => "Undefined WorkOrder"];
            }
            else {
                $wo = Wo::create($input);
                $actionId = null;
            }

            $status = Master\Status::getStatusOpen($input['activity_id']);
            $inputAction = [
                "status_id" => $status->id,
                "note" => $input['description'],
                "lat" => $request->input('lat'),
                "long" => $request->input('long')
            ];
            $details = $request->input('details');
            $action = $this->actionPush($wo, $inputAction, $details, $actionId);

            if(!is_object($action)) return ['success' => false, 'message' => $action];

            DB::commit();
            return ['success' => true, 'message' => 'Success...', 'data' => $wo];
        }
        catch(Exception $error) {
            DB::rollback();
            return ['success' => false, 'message' => '500 (Create WO)'.$error->getMessage()];
        }

    }

    public function pushAction(Request $request, $wo=null, $status=null){
        $user = $request->user();
        $wo = Wo::find($wo);
        $status = Master\Status::find($status);

        if (!$error = $this->actionValid($wo, $status, $user)) {
            $input = [
                "status_id" => $status->id,
                "note" => $request->input('note'),
                "lat" => $request->input('lat'),
                "long" => $request->input('long')
            ];
            $details = $request->input('details');
            $action = $this->actionPush($wo, $input, $details);
            if (is_object($action)) {
                return ['success' => true, 'message' => 'Success...'];
            }
            else $error = $action;
        }

        return ['success' => false, 'message' => $error];
    }

    private function actionValid($wo, $status, $user){
        if(!$wo) return "WorkOrder Not Found!";
        if(!$status) return "Status Not Found!";
        if(!in_array($user->role_id, $status->roles)) return "Update Status ($status->name) Denied!";
        if(!in_array($wo->activity_id, $status->activities)) return $wo->activity->name." ($status->name) Not Found!";
        if(!$status->show_on || !in_array($wo->lastAction->status_id, $status->show_on)) return "Not Show On $status->name";

        return null;
    }

    private function actionPush($wo, $input, $details, $id=null){
        if(!$wo) return 'Undefined WorkOrder';
        else{
            DB::beginTransaction();
            try{
                $input['wo_id'] = $wo->id;
                if($id){
                    if($action = Action::find($id)){
                        $action->update($input);
                    }
                    else return "Undefined Action Id";
                }
                else {
                    $action = Action::create($input);
                    $wo->update(['last_action' => $action->id]);
                }

                $this->actionDetailPush($wo, $action, $details);

                // SET CLOSING WO -------------------------------------------
                if($action->status->type > 1){
                    $wo->update(['close_date' => $action->created_at]);
                }

                DB::commit();

                // SEND EMAIL ----------------------------------------------------------------
                if($action->status) {
                    $sendEmailRoles = $action->status->send_email_roles;
                    $sendTo = User::where(function ($query) use ($wo, $sendEmailRoles) {
                        $query->whereIn('role_id', $sendEmailRoles);
                        $query->where(function ($query) use ($wo, $sendEmailRoles) {
                            $query->where(function ($query) use ($wo, $sendEmailRoles) {
                                $query->where('role_id', '<', 1000);
                                $query->whereNull('vendor_id');
                                $query->whereNull('client_id');
                                $query->whereNull('fieldtech_id');
                                $query->whereNull('activities');
                                $query->whereNull('owners');
                            });
                            $query->orwhere(function ($query) use ($wo) {
                                $query->where(function ($query) use ($wo) {
                                    $query->where('vendor_id', $wo->vendor_id);
                                    $query->whereNull('fieldtech_id');
                                });
                                $query->orwhere(function ($query) use ($wo) {
                                    $query->where('vendor_id', $wo->vendor_id);
                                    $query->where('fieldtech_id', $wo->fieldtech_id);
                                });
                            });
                            $query->orwhere('client_id', $wo->client_id);
                            $query->orwhere('activities', 'LIKE', '%' . $wo->activity_id . '%');
                            $query->orwhere('owners', 'LIKE', '%' . $wo->activity_id . '%');
                        });

                    })->whereNotNull('email')->where('email', '<>', '')->pluck('email');
                    Mail::to($sendTo)->send(new UpdateWoMail($wo, $action));
                }

                return $action;
            }
            catch(Exception $error){
                DB::rollback();
                return '500 (Action WO) '.$error->getMessage();
            }
        }
    }

    private function actionDetailPush($wo, $action, $details){
        $details = json_decode($details);
        if($details){
            DB::beginTransaction();
            try{
                ActionDetail::where('action_id', $action->id)->delete();
                foreach ($details AS $detail){
                    $statusDetail = Master\StatusDetail::where('status_id', $action->status_id)->where('id', $detail->id)->first();
                    if($statusDetail){
                        if($statusDetail->type == 'file'){
                            if($detail->value && count($detail->value)) {
                                $actionDetail = ActionDetail::create(['action_id' => $action->id, 'detail_id' => $statusDetail->id]);
                                $watermark  = "DEMO (WO: $action->wo_id) - (".strtoupper(date('d M Y H:i')).")";
                                $watermark .= "\n".$action->status->name;
                                $watermark .= "\n".$statusDetail->name;
                                if($action->lat && $action->long) $watermark .= "\nCoordinate ($action->lat, $action->long)";
                                foreach ($detail->value AS $file) {
                                    if (is_object($file)) $actionDetail->files()->attach($file->id);
                                    else if($fileid = FileUpload::push($file, 'action-detail-file', $watermark)){
                                        $actionDetail->files()->attach($fileid);
                                    }
                                }
                            }
                        }
                        else if($statusDetail->type == 'signature'){
                            if($detail->value) {
                                $fileid = FileUpload::push($detail->value, 'action-detail-signature');
                                ActionDetail::create([
                                    'action_id' => $action->id,
                                    'detail_id' => $statusDetail->id,
                                    'value' => $fileid,
                                ]);
                            }
                        }
                        else{
                            $value = ActionDetail::create([
                                'action_id' => $action->id,
                                'detail_id' => $statusDetail->id,
                                'value' => isset($detail->value) ? $detail->value : null,
                            ]);
                        }

                        if($statusDetail->triger){
                            $value = isset($detail->value) ? $detail->value : null;
                            switch ($statusDetail->triger){
                                case 'wo.fieldtech':
                                    $wo->update(['fieldtech_id' => $value]);
                                    break;
                                case 'site.link_active':
                                    $site = Site::find($wo->site_id);
                                    $site->update([
                                        'link_id' => $value,
                                        'active_date' => $action->created_at,
                                        'owner_id' => $wo->owner_id,
                                        'service_id' => $wo->service_id,
                                    ]);
                                    break;
                                case 'site.inactive':
                                    if($site = Site::find($wo->remove_site_id)) {
                                        $site->update([
                                            'inactive_date' => $action->created_at,
                                            'is_active' => 0
                                        ]);
                                    }
                                    break;
                            }
                        }
                    }
                }

                DB::commit();
                return false;
            }
            catch(Exception $error){
                DB::rollback();
                return '500 (WO Action Detail) '.$error->getMessage();
            }
        }

        return 'Error Action Details';
    }

    public function pushPart(Request $request, $id=null){
        DB::beginTransaction();
        try{
            $input = [
                'wo_id' => $request->input('wo_id'),
                'type' => $request->input('type'),
                'code' => $request->input('code'),
                'name' => $request->input('name'),
                'serial' => $request->input('serial'),
                'model' => $request->input('model'),
                'description' => $request->input('description'),
            ];
            if($id){
                if($data = Part::find($id)) {
                    $data->update($input);
                }
                else return ['success' => false, 'message' => 'Update Part Notfound...'];
            }
            else $data = Part::create($input);

            $data->files()->detach();
            if($data && $files = $request->input('files')){
                $files = json_decode($files);
                foreach ($files AS $file) {
                    if (is_object($file)) $data->files()->attach($file->id);
                    else if($fid = FileUpload::push($file, 'part')){
                        $data->files()->attach($fid);
                    }
                }
            }

            DB::commit();
            return ['success' => true, 'message' => 'Success...'];
        }
        catch(Exception $error) {
            DB::rollback();
            return ['success' => false, 'message' => '500 (Create WO)'.$error->getMessage()];
        }
    }

    public function delete(Request $request){
        if($data = json_decode($request->data)) {
            $user = $request->user();
            Wo::whereIn('id', $data)->update(['deleted_by' => $user->id, 'deleted_at' => date('Y-m-d H:i:s')]);
            return ['success' => true, 'message' => 'Success!'];
        }
        return ['success' => false, 'message' => 'No Data!'];
    }

    public function deletePart(Request $request){
        if($id = $request->input('id')) {
            Part::find($id)->delete();
            return ['success' => true, 'message' => 'Success!'];
        }
        return ['success' => false, 'message' => 'No Data!'];
    }

    public function exportExcel(Request $request){
        $user = $request->user();

        $titles = [ ["WORKORDER", 'h2'], ["DEMO", 'h3'] ];

        $query = $request->input('query');
        $query = "(A.id LIKE '%$query%' OR A.no_wo LIKE '%$query%' OR A.description LIKE '%$query%' OR G1.name LIKE '%$query%' OR G2.name LIKE '%$query%' OR I.name LIKE '%$query%')";


        if($request->input('archive')) {
            $query .= "AND (A.close_date IS NOT NULL)";
            array_push($titles, ['Archive Data','h4']);
        }
        else {
            $mindate = date('Y-m-d', strtotime('-7 days'));
            $query .= "AND (A.close_date IS NULL OR A.close_date >= '$mindate')";
            array_push($titles, ['Data On Going','h4']);
        }

        // FILTER ------------------------------------------------------------------------------------------------------
        if($filter = $request->input('filter-status')) $query .= "AND (B.status_id = '$filter')";
        if($filter = $request->input('filter-activity')) $query .= "AND (A.activity_id = '$filter')";
        if($filter = $request->input('filter-service')) $query .= "AND (A.service_id = '$filter')";
        if($filter = $request->input('filter-vendor')) $query .= "AND (A.vendor_id = '$filter')";
        if($filter = $request->input('filter-client')) $query .= "AND (A.client_id = '$filter')";
        if($filter = $request->input('filter-owner')) $query .= "AND (A.owner_id = '$filter')";

        // FILTER BY USER AUTH -----------------------------------------------------------------------------------------
        if($ftr = $user->owners) $query = "AND (A.owner_id = '$ftr') ";
        if($ftr = $user->activities) $query = "AND (A.activity_id = '$ftr') ";
        if($ftr = $user->client_id) $query = "AND (A.client_id = '$ftr') ";
        if($ftr = $user->vendor_id) $query = "AND (A.vendor_id = '$ftr') ";
        if($ftr = $user->fieldtech_id) $query = "AND (A.fieldtech_id = '$ftr') ";

        $sql = "SELECT A.*,
                       B.created_at lastupdate_at,
                       B1.`name` status_name,
                       C.`name` activity_name,
                       D.`name` service_name,
                       E.`name` owner_name,
                       F.`name` client_name,
                       G1.`name` site_name,
                       G2.`name` remove_site_name,
                       H.`name` vendor_name,
                       I.`name` fieldtech_name,
                       J.`name` created_by_name,
                       DATEDIFF(DATE(NOW()), A.start_date) sla,
                       DATEDIFF(A.expire_date, A.start_date) sla_target
                FROM po_wo A
                     LEFT JOIN po_wo_action B ON A.last_action = B.id
                         LEFT JOIN po_wo_m_status B1 ON B.status_id = B1.id
                         LEFT JOIN po_wo_m_activity C ON A.activity_id = C.id
                         LEFT JOIN po_m_owner E ON A.owner_id = E.id
                         LEFT JOIN po_m_client F ON A.client_id = F.id
                         LEFT JOIN po_m_site G1 ON A.site_id = G1.id
                         LEFT JOIN po_m_site G2 ON A.remove_site_id = G2.id
                         LEFT JOIN po_wo_m_service D ON G1.service_id = D.id
                         LEFT JOIN po_m_vendor H ON A.vendor_id = H.id
                         LEFT JOIN po_m_fieldtech I ON A.fieldtech_id = I.id
                         LEFT JOIN auth_user J ON A.created_by = J.id
                WHERE $query";

        $data = DB::select(DB::raw($sql));
        $columns = [
            ["text"=> "ID", "dataIndex"=> "id", "width"=> 115],
            ["text"=> "SERVICE", "dataIndex"=> "service_name", "width"=> 100, "align"=> "center"],
            ["text"=> "ACTIVITY", "dataIndex"=> "activity_name", "width"=> 150, "align"=> "center"],
            ["text"=> "OWNER", "dataIndex"=> "owner_name", "width"=> 180],
            ["text"=> "CLIENT", "dataIndex"=> "client_name", "width"=> 150],
            ["text"=> "SITE", "dataIndex"=> "site_name", "width"=> 200],
            ["text"=> "DISMANTLE SITE", "dataIndex"=> "remove_site_name", "width"=> 200],
            ["text"=> "VENDOR", "dataIndex"=> "vendor_name", "width"=> 200],
            ["text"=> "FIELDTECH", "dataIndex"=> "fieldtech_name", "width"=> 250],
            ["text"=> "SLA", "dataIndex"=> "sla", "align"=> "center", "width"=> 100, 'type' => 'int'],
            [
                "text"=> "CONTRACT",
                "columns"=> [
                    ["text"=> "START", "dataIndex"=> "start_date", "type"=> "date", "align"=> "center", "width"=> 100],
                    ["text"=> "TARGET", "dataIndex"=> "expire_date", "type"=> "date", "align"=> "center", "width"=> 100]
                ]
            ],
            [
                "text"=> "OPEN AT",
                "columns"=> [
                    ["text"=> "OPEN BY", "dataIndex"=> "created_by_name", "width"=> 200],
                    ["text"=> "DATE", "dataIndex"=> "created_at", "type"=> "date", "align"=> "center", "width"=> 100]
                ]
            ],
            [
                "text"=> "LAST STATUS",
                "columns"=> [
                    ["text"=> "STATUS", "dataIndex"=> "status_name", "width"=> 200],
                    ["text"=> "DATE", "dataIndex"=> "lastupdate_at", "type"=> "date", "align"=> "center", "width"=> 100]
                ]
            ],
            ["text"=> "DESCRIPTION", "dataIndex"=> "description", "width"=> 250],
        ];

        $footers = ['Total Count: '.count($data).' Row', ' ', 'DEMO', 'Downloaded at ('.date('d F Y H:i:s').')'];
        $params = array(
            'title' => $titles,
            'columns' => $columns,
            'filename' => 'POST - Workorder '.date("YmdHis"),
            'data' => $data,
            'footer' => $footers,
        );

        $excel = new ExportExcel($params);
        $excel->run($params);
    }
}
