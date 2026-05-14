<?php

namespace App\Controllers\WorkOrders;

use App\SystemModels\Globals\Upload;
use Cache;
use Curl;
use Illuminate\Database\QueryException;
use PDF;
use App\Libraries\ExportExcel;
use App\Libraries\FileUpload;
use App\Models\Fieldteches\Fieldtech;
use App\Models\Owners\Owner;
use App\Models\WorkOrders\Part;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libraries\BuildExtrafieldWo;
use App\Libraries\Query;
use App\Models\WorkOrders\WorkOrder as Wo;
use App\Models\WorkOrders\WorkOrderOngoing as WoOngoing;
use App\Models\WorkOrders\Action;
use App\Models\WorkOrders\ActionDetail;
use App\Models\WorkOrders\Masters\StatusDetailOption;
use App\Models\Sites\Site;
use App\Models\Clients\Client;
use App\Models\Vendors\Vendor;
use App\Models\WorkOrders\Masters as Master;
use App\Models\WorkOrders\Masters\StatusDetail;
use Illuminate\Support\Facades\Log;

class WorkOrder extends Controller
{
    public function __construct()
    {
        set_time_limit(120); // Set time limit to 2 minutes
    }

    public function index(Request $request, $archive = false)
    {
        $user   = $request->user();
        $view   = isMobile() ? 'workorders.mobile.main' : 'workorders.main';
        $params = $this->getParams($request, ['archive' => $archive]);
        return view($view, $params);
    }

    public function form(Request $request, $id = null)
    {
        $user = $request->user();
        $view   = 'workorders.form';
        $params = $this->getParams($request);;
        return view($view, $params);
    }

    public function detail(Request $request, $id = null)
    {
        $params = $this->getParams($request, ['data' => $this->get($request, $id)]);
        return view('workorders.detail', $params);
    }

    private function getParams($request, $params = null)
    {
        $user = $request->user()->load('fieldtech', 'fieldtech.workorders', 'fieldtech.workorders.client', 'fieldtech.workorders.activity');
        // $user = $request->user();

        if ($user->vendors && count($user->vendors)) $vendors = $user->vendors;
        else $vendors = Vendor::orderBy('name')->get();

        $result = [
            'user' => $user,
            'activities' => ($ftr = $user->activities) ? Master\Activity::whereIn('id', $ftr)->get() : Master\Activity::all(),
            'clients' => Client::orderBy('name', 'asc')->get(),
            'owners' => ($ftr = $user->owners) ? Owner::whereIn('id', $ftr)->get() : Owner::all(),
            'vendors' => $vendors,
            'services' => Master\Service::all(),
            'slots' => Master\Slot::all(),
            'status' => Master\Status::with('details.options')->get(),
        ];
        if ($params) $result = array_merge($result, $params);
        return $result;
    }

    public function archive(Request $request)
    {
        return $this->index($request, true);
    }

    public function data(Request $request, $archive = false)
    {
        $user = $request->user();

        $query = $archive ? Wo::query() : WoOngoing::query();

        $query->with([
            'site.service',
            'client',
            'removeSite',
            'fieldtech.users',
            'lastAction.details.files',
            'lastAction.createdBy',
            'lastAction.updatedBy',
            'lastAction.deletedBy',
            'actions',
            'actions.details',
            'actions.details.detail',
            'actions.details.files',
            'actions.details.fieldtech.users',
            'actions.createdBy',
            'actions.updatedBy',
            'actions.deletedBy',
            'parts.files',
            'createdBy',
            'updatedBy',
            'deletedBy'
        ]);

        $search = $request->input('query');

        if ($archive) {
            $query->whereNotNull('close_date');
        } else {
            if ($request->input('activedOnly') == 1) {
                $query->whereNull('close_date');
            } else if ($request->input('activedOnly') == 2) {
                $query->where('close_date', '>', date('Y-m-d', strtotime('-1 days')));
            } else {
                $query->where(function ($query) {
                    $query->whereNull('close_date')
                        ->orWhere('close_date', '>', date('Y-m-d', strtotime('-1 days')));
                });
            }
        }

        // FILTER BY USER AUTH
        if ($ftr = $user->owners) $query->whereIn('owner_id', $ftr);
        if ($ftr = $user->activities) $query->whereIn('activity_id', $ftr);
        if ($ftr = $user->client_id) $query->where('client_id', $ftr);
        if ($ftr = $user->vendor_id) $query->where('vendor_id', $ftr);
        if ($ftr = $user->fieldtech_id) $query->where('fieldtech_id', $ftr);
        if (count($user->vendors)) {
            $query->whereIn('vendor_id', $user->vendors->pluck('id')->toArray());
        }

        if (!empty($user->listvendor_id)) {
            $query->whereHas('fieldtech', function ($q) use ($user) {
                $q->where('listvendor_id', $user->listvendor_id);
            });
        }


        // FILTER
        if ($ftr = $request->input('filter-status')) {
            $query->whereHas('lastAction', function ($query) use ($ftr) {
                $query->where('status_id', $ftr);
            });
        }
        if ($ftr = $request->input('filter-activity')) $query->where('activity_id', $ftr);
        if ($ftr = $request->input('filter-service')) $query->where('service_id', $ftr);
        if ($ftr = $request->input('filter-client')) $query->where('client_id', $ftr);
        if ($ftr = $request->input('filter-owner')) $query->where('owner_id', $ftr);
        if ($ftr = $request->input('filter-vendor')) $query->where('vendor_id', $ftr);
        if (($ftr = $request->input('filterDate')) && !$search) {
            $month = date('Y-m', strtotime("$ftr 00:00:00")) . '%';
            $query->where('start_date', 'LIKE', $month);
        }
        if ($ftr = $request->input('filter-hold')) {
            if ($ftr == 1) {
                $query->where('is_hold', 0);
            } else if ($ftr == 2) {
                $query->where('is_hold', 1);
            } else {
                $query->whereNull('is_hold');
            }
        }

        // SEARCH
        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->orWhereHas('site', function ($query) use ($search) {
                    $query->where('name', 'LIKE', "%$search%");
                });
                $query->orWhereHas('fieldtech', function ($query) use ($search) {
                    $query->where('name', 'LIKE', "%$search%");
                });
                $query->orWhere('id', 'LIKE', "%$search");
                $query->orWhere('no_wo', 'LIKE', "%$search%");
                $query->orWhere('description', 'LIKE', "%$search%");
            });
        }

        // DEFAULT SORT
        if (!$request->input('sort')) $query->orderBy('updated_at', 'DESC');

        return Query::open($query);
    }

    public function get(Request $request, $id = null)
    {
        $data = Wo::with([
            'site.service',
            'removeSite',
            'fieldtech.users',
            'lastAction.status',
            'lastAction.details.files',
            'lastAction.createdBy',
            'lastAction.updatedBy',
            'lastAction.deletedBy',
            'actions.details.files',
            'actions.details.fieldtech.users', // => function($query){ $query->where('id', 1); },
            'actions.createdBy',
            'actions.updatedBy',
            'actions.deletedBy',
            'parts.files',
            'createdBy',
            'updatedBy',
            'deletedBy'
        ])->find($id);
    }

    public function getPublic(Request $request, $id = null)
    {
        return Wo::find($id);
    }

    public function dataArchive(Request $request)
    {
        return $this->data($request, true);
    }

    public function dataSite(Request $request)
    {
        $query = Site::query();
        return Query::open($query, ['id', 'name', 'link_id'], false);
    }

    public function dataFieldtech(Request $request)
    {
        $user = $request->user();
        $startDate = $request->input('start_date');
        $slot = $request->input('slot');
        $query = Fieldtech::where('vendor_id', $request->vendor);
        if (count($user->vendors)) {
            $query->whereIn('vendor_id', $user->vendors->pluck('id')->toArray());
        }
        $query->withCount(['workorders' => function ($query) use ($startDate, $slot) {
            $query->where('start_date', $startDate);
            $query->where('slot_id', $slot);
        }]);
        return Query::open($query, null, false);
    }

    public function reloadTicket($woId)
    {
        DB::beginTransaction();
        try {
            // Cari Work Order di kedua tabel
            $wo = Wo::find($woId);
            $woOngoing = WoOngoing::where('wo_id', $wo->id)->first();

            if (!$wo) {
                return response()->json(['success' => false, 'message' => 'Work Order not found']);
            }

            if (!$wo->is_hold) {
                $lastStatus = strtoupper(optional($wo->lastAction->status)->name);

                if ($lastStatus == 'ACTIVATION') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Ticket is Ready. Please continue ONT ACTIVATION.'
                    ]);
                } elseif ($lastStatus == 'CHECK MAC ADDRESS HSI') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Ticket is Ready. Please continue TESTING.'
                    ]);
                }
            }

            // Ambil last_action dari WO
            $action = Action::where('id', $wo->last_action)->first();

            if (!$action) {
                return response()->json(['success' => false, 'message' => 'No action found for this Work Order']);
            }

            // Siapkan detail untuk API
            $details = $this->getActionDetails($action);
            $apiResult = $this->hitExternalApi($wo, $action, $details);

            // Handle response API
            if ($apiResult->success) {
                if ($apiResult->status == 200) {
                    $wo->update(['is_hold' => 0]);
                    if ($woOngoing) {
                        $woOngoing->update(['is_hold' => 0]);
                    }

                    DB::commit();
                    return response()->json(['success' => true, 'message' => 'Ticket successfully continued', 'status' => 200]);
                } elseif ($apiResult->status == 206) {
                    DB::commit();

                    if (strtoupper($action->status->name) == 'ACTIVATION') {
                        $message = 'Hold, waiting for partner acknowledgment';
                    } elseif (strtoupper($action->status->name) == 'CHECK MAC ADDRESS HSI') {
                        $message = 'Hold, waiting for MAC address verification';
                    } elseif (strtoupper($action->status->name) == 'IPTV CUSTOMER') {
                        $detailsArray = is_string($details) ? json_decode($details, true) : $details;

                        $iptvHold = false;
                        foreach ($detailsArray as $d) {
                            if (
                                isset($d['name'], $d['value']) &&
                                strtolower($d['name']) == 'pelanggan iptv' &&
                                $d['value'] == 430
                            ) {
                                $iptvHold = true;
                                break;
                            }
                        }

                        if ($iptvHold) {
                            $message = 'Hold, waiting for IPTV customer provisioning';
                        }
                    }

                    return response()->json([
                        'success' => true,
                        'message' => $message,
                        'status' => 206
                    ]);
                } else {
                    return response()->json(['success' => false, 'message' => 'Unknown status', 'status' => $apiResult->status]);
                }
            } else {
                DB::rollback();
                return response()->json(['success' => false, 'message' => 'API Error: ' . $apiResult->message]);
            }
        } catch (QueryException $error) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => '500 (Retry Hold Ticket) ' . $error->getMessage()]);
        }
    }

    private function getActionDetails($action)
    {
        $details = [];

        foreach ($action->details as $extra) {
            $details[] = [
                'name' => strtolower($extra->detail->name),
                'value' => $extra->value,
            ];
        }

        return $details;
    }

    private function hitExternalApi($wo, $action, $details)
    {
        $result = $this->pushApi($wo, $action, $details);

        Log::info('RELOAD - Response dari API:', $result->data ?? []);

        return $result;
    }


    public function push(Request $request, $id = null)
    {
        DB::beginTransaction();
        try {
            $startDate = $request->input('start_date');
            $fieldtechId = $request->input('fieldtech_id');
            $slotId = $request->input('slot_id');
            $activityId = $request->input('activity_id');
            $noWo = $request->input('no_wo');

            // Validasi input yang diperlukan
            if (!$request->input('remove_site_id') && !$request->input('site_id'))
                return ['success' => false, 'message' => 'site_id OR remove_site_id Is Null'];
            if (!$request->input('activity_id')) return ['success' => false, 'message' => 'activity_id Is Null'];
            if (!$request->input('client_id')) return ['success' => false, 'message' => 'client_id Is Null'];
            if (!$request->input('description')) return ['success' => false, 'message' => 'description Is Null'];

            if ($noWo) {
                $existingWoQuery = Wo::where('no_wo', $noWo);
                if ($id) {
                    $existingWoQuery->where('id', '!=', $id);
                }
                $existingWo = $existingWoQuery->first();

                if ($existingWo) {
                    return ['success' => false, 'message' => 'Ticket Number already exists'];
                }

                $existingWoOngoingQuery = WoOngoing::where('no_wo', $noWo);
                if ($id) {
                    $existingWoOngoingQuery->where('wo_id', '!=', $id);
                }
                $existingWoOngoing = $existingWoOngoingQuery->first();

                if ($existingWoOngoing) {
                    return ['success' => false, 'message' => 'Ticket Number already exists'];
                }
            }

            if ($startDate && $fieldtechId && $slotId) {
                if ($err = $this->fieldtechCheck($fieldtechId, $startDate, $slotId)) {
                    return ['success' => false, 'message' => 'Team already have installation ticket', 'data' => $err];
                }
            }

            if ($startDate && strtotime($startDate) < strtotime('1970-01-01')) {
                $startDate = null;
            }

            $input = [
                'site_id' => $request->input('site_id'),
                'remove_site_id' => $request->input('remove_site_id'),
                'activity_id' => $activityId,
                'vendor_id' => $request->input('vendor_id'),
                'client_id' => $request->input('client_id'),
                'service_id' => $request->input('service_id'),
                'slot_id' => $slotId,
                'owner_id' => $request->input('owner_id'),
                'description' => $request->input('description'),
                'no_wo' => $noWo,
                'start_date' => $startDate,
                'fieldtech_id' => $fieldtechId,
                'expire_date' => $request->input('expire_date'),
                'is_hold' => 0,
                'last_action' => null,
                'created_by' => auth()->user()->id,
                'updated_by' => auth()->user()->id
            ];

            Log::info('Data Create from Asianet : ', $input);

            if ($id) {
                if ($wo = Wo::find($id)) {
                    $input['is_hold'] = $wo->is_hold;

                    $input['close_date'] = $wo->close_date;

                    if (!isset($input['last_action'])) {
                        $input['last_action'] = $wo->last_action;
                    }

                    $wo->update($input);
                    $actionId = optional($wo->actions()->first())->id;
                    $lastAction = $wo->last_action;

                    WoOngoing::where('wo_id', $wo->id)
                        ->update(array_merge($input, [
                            'last_action' => $lastAction,
                            'id' => $wo->id
                        ]));
                } else {
                    return ['success' => false, 'message' => "Undefined WorkOrder"];
                }
            } else {
                $input['close_date'] = null;
                $wo = Wo::create($input);
                $actionId = null;

                WoOngoing::firstOrCreate(
                    ['wo_id' => $wo->id],
                    array_merge($input, [
                        'last_action' => null,
                        'id' => $wo->id
                    ])
                );
            }

            $status = Master\Status::getStatusOpen($input['activity_id']);
            $inputAction = [
                "status_id" => $status->id,
                "note" => $input['description'],
                "lat" => $request->input('lat'),
                "long" => $request->input('long')
            ];

            if (!$details = $request->input('details')) {
                $details = [];
                if ($statusDetails = Master\StatusDetail::where('status_id', $status->id)->get()) {
                    foreach ($statusDetails as $detail) {
                        if ($detail->name == "Team") $details[] = (object) ['id' => $detail->id, 'value' => $fieldtechId];
                        else if ($detail->name == "Date") $details[] = (object) ['id' => $detail->id, 'value' => $startDate];
                        else if ($detail->name == "Slot") $details[] = (object) ['id' => $detail->id, 'value' => $slotId];
                        else if ($detail->name == "ONT Type") $details[] = (object) ['id' => $detail->id, 'value' => $request->input('ontType')];
                        else if ($detail->name == "Total STB") $details[] = (object) ['id' => $detail->id, 'value' => $request->input('totalSTB')];
                        else if ($detail->name == "Tipe STB") $details[] = (object) ['id' => $detail->id, 'value' => $request->input('deviceDetailType')];
                    }
                }
            }

            $action = $this->actionPush($wo, $inputAction, $details, $actionId);

            if (!is_array($action)) return ['success' => false, 'message' => $action];
            elseif (!$action['success']) return ['success' => false, 'message' => $action];

            $wo->refresh();

            WoOngoing::where('wo_id', $wo->id)->update(['last_action' => $wo->last_action]);

            DB::commit();

            //convert to string untuk id
            $wo->id = (string) $wo->id;

            return ['success' => true, 'message' => 'Success...', 'data' => $wo];
        } catch (QueryException $error) {
            DB::rollback();
            return ['success' => false, 'message' => '500 (Create WO) ' . $error->getMessage()];
        }
    }

    private function fieldtechCheck($fieldtech, $date, $slot, $id = null)
    {
        if ($date && $fieldtech && $slot) {
            $rec = Wo::where('fieldtech_id', $fieldtech)
                ->where('start_date', $date)
                ->where('activity_id', 0)
                ->where('slot_id', $slot);
            if ($id) $rec->where('id', '<>', $id);
            return $rec->first();
        }
        return null;
    }

    public function pushAction(Request $request, $wo = null, $status = null)
    {
        DB::beginTransaction();
        try {
            $user = $request->user();
            $wo = Wo::find($wo);
            $status = Master\Status::find($status);

            if (!$wo || !$status) {
                return [
                    'success' => false,
                    'message' => 'Work Order or Status not found'
                ];
            }

            if ($wo->lastAction->status->name == 'ACTIVATION' && $wo->is_hold == 1) {
                return [
                    'success' => false,
                    'message' => "Hold, waiting from partner acknowledgements"
                ];
            }

            if ($error = $this->actionValid($wo, $status, $user)) {
                return ['success' => false, 'message' => $error];
            }

            $input = [
                "status_id" => $status->id,
                "note" => $request->input('note'),
                "lat" => $request->input('lat'),
                "long" => $request->input('long')
            ];
            $details = $request->input('details');

            $actionResult = $this->actionPush($wo, $input, $details);

            if (!is_array($actionResult) || !$actionResult['success']) {
                DB::rollback();
                return $actionResult;
            }

            WoOngoing::where('wo_id', $wo->id)->update([
                'last_action' => $wo->last_action,
                'is_hold' => $wo->is_hold,
                'close_date' => $wo->close_date
            ]);

            BuildExtrafieldWo::build($wo->id);

            DB::commit();
            return ['success' => true, 'message' => 'Action updated successfully'];
        } catch (QueryException $error) {
            DB::rollback();
            return ['success' => false, 'message' => '500 (Push Action) ' . $error->getMessage()];
        }
    }

    private function actionPush($wo, $input, $details, $id = null)
    {
        if (!$wo) return 'Undefined WorkOrder';
        else {
            DB::beginTransaction();
            try {
                $input['wo_id'] = $wo->id;
                if ($id) {
                    if ($action = Action::find($id)) {
                        $action->update($input);
                    } else return "Undefined Action Id";
                } else {
                    $action = Action::create($input);
                    $wo->update(['last_action' => $action->id]);
                }

                if ($pushdetail = $this->actionDetailPush($wo, $action, $details)) {
                    DB::rollback();
                    return $pushdetail;
                }

                if ($wo->is_hold != 1) {
                    $wo->update(['is_hold' => 0]);
                }


                // SET CLOSING WO -------------------------------------------
                if ($action->status->type > 1) {
                    $wo->update(['close_date' => $action->created_at]);
                }

                if (strtoupper(substr($wo->no_wo, 0, 2)) == 'OH') {
                    if (in_array($wo->activity->name, ['INSTALLATION', 'SERVICE UPDATE', 'RELOCATION', 'DEVICE MOVING', 'TERMINATION', 'TROUBLESHOOT', 'CPE REPLACEMENT'])) {
                        if (in_array($action->status->name, ['PREPARATION', 'IN PROGRESS', 'ARRIVED', 'INSTALLATION', 'ACTIVATION', 'POST ACTIVATION', 'TESTING', 'ADDITIONAL MATERIAL', 'TROUBLESHOOT ACTION', 'SOLVING', 'PENDING', 'TESTING & CLOSE', 'CHANGE CPE', 'IPTV CUSTOMER', 'CHECK MAC ADDRESS HSI', 'CLOSED'])) {
                            if ($pushapi = $this->pushApi($wo, $action, $details)) {
                                if ($pushapi->success && ($pushapi->status == 200 || $pushapi->status == 206)) {

                                    $detailsArray = is_string($details) ? json_decode($details, true) : $details;

                                    if ($pushapi->status == 206 && in_array($action->status->name, ['ACTIVATION', 'CHECK MAC ADDRESS HSI'])) {
                                        $wo->update(['is_hold' => 1]);
                                    }

                                    if ($pushapi->status == 206 && $action->status->name === 'IPTV CUSTOMER') {
                                        foreach ($detailsArray as $d) {
                                            if (isset($d['id'], $d['value']) && $d['id'] == 281909 && $d['value'] == 430) {
                                                $wo->update(['is_hold' => 1]);
                                                break;
                                            }
                                        }
                                    }
                                    if ($pushapi->status == 200) {
                                        $wo->update(['is_hold' => 0]);
                                    }

                                    DB::commit();
                                    return (array) $pushapi;
                                }
                            }
                            DB::rollback();
                            return ['success' => false, 'message' => 'API Error: ' . $pushapi->message];
                        }
                    }
                }

                DB::commit();
                return ['success' => true, 'message' => 'Success'];

                // SEND EMAIL ----------------------------------------------------------------
                // dispatch(new NotifJob($wo->id));

            } catch (QueryException $error) {
                DB::rollback();
                return ['success' => false, 'message' => '500 (Action WO) ' . $error->getMessage()];
            }
        }
    }

    public function testApi(Request $request)
    {
        $result = (object) ['success' => false];

        $baseUrl = 'http://103.66.38.238'; //config('site.asianet_api_url');
        $email = 'QA.Asianet+C52@gmail.com'; //config('site.asianet_api_user');
        $password = 'odm'; //config('site.asianet_api_password');

        $urlLogin = $baseUrl . '/amt/1.1/atm/generate-token';
        //$urlPush = $baseUrl.'/amt/1.0/wfm/engineerstatus';
        $urlPush = $baseUrl . '/amt/1.1/apm/engineerstatus';


        if (Cache::has('token')) $token = Cache::get('woaccesstoken');
        else {
            $login = Curl::to($urlLogin)
                ->withData(['email' => $email, 'password' => $password])
                ->withTimeout(120)
                ->asJson()
                ->returnResponseObject()
                ->post();

            if (isset($login->content) && isset($login->content->body->accessToken)) {
                $token = $login->content->body->accessToken;
                Cache::put('woaccesstoken', $token, 10);
            } else {
                $result->message = "ERROR API LOGIN (" . $login->status . ") " . ($login->content ? json_encode($login->content) : '');
                return (array) $result;
            }
        }

        // GET ACTION --------------------------------------------------------------------------------------------------

        $data = [
            'activityName' => $request->input('activityName') ?: 'INSTALLATION',
            'orderNumber' => 'OH1093851648341319601', //$request->input('orderNumber') ?: '0',
            'workFlowNumber' => '202408000041', //$request->input('workFlowNumber') ?: '0',
            'orderStatus' => $request->input('orderStatus') ?: 'PREPARED',
            'teamID' => 42, //$request->input('teamId') ?: 0,
            'serialNumber' => $request->input('serialNumber') ?: null,
            'longitude' => $request->input('longitude') ?: 0,
            'latitude' => $request->input('latitude') ?: 0,
            'fatLongitude' => $request->input('longitude') ?: 0,
            'fatLatitude' => $request->input('latitude') ?: 0,
            'additionalUTP' => $request->input('additionalUTP') ?: 0,
            'additionalDropCable' => $request->input('additionalDropCable') ?: 0,
            'bastURL' => route('wo.export.balap', 'OH1093851648341319601'),
            'cpe' => [
                [
                    "type" => 'ont',
                    "serialNumber" => "AMTB2400001",
                    "macaddressont" => '12345'
                ],
                [
                    "type" => 'stb',
                    "stbType" => "stbType1",
                    "serialNumber" => "AMTB2400001",
                    "macAddressstb" => '12345'
                ],
                [
                    "type" => 'stb',
                    "stbType" => "stbType2",
                    "serialNumber" => "AMTB2400001",
                    "macAddressstb" => '12345'
                ],
                [
                    "type" => 'stb',
                    "stbType" => "stbType3",
                    "serialNumber" => "AMTB2400001",
                    "macAddressstb" => '12345'
                ],
            ]
        ];

        // PUSH API ----------------------------------------------------------------------------------------------------

        $response = Curl::to($urlPush)
            ->withData($data)
            ->withTimeout(120)
            ->withBearer($token)
            ->asJson()
            ->returnResponseObject()
            ->post();
        if ($response->status == 200 || $response->status == 400) {
            if ($content = $response->content) {
                if (isset($content->statusCode)) {
                    if (!$content->statusCode) {
                        $result->success = true;
                        $result->message = "Success";
                    } else {
                        $result->message = "Error API engineer status response failed";
                        $result->result = json_encode($content);
                    }

                    $result->result = [
                        'url' => $urlPush,
                        'dataPush' => $data,
                        'response' => (array) $content,
                    ];
                } else $result->message = "Error API engineer status statusCode Not Found";
            } else $result->message = "Error API engineer status (response is null)";
        } else {
            $result->message = "ERROR API ENGINEERSTATUS ($response->status)";
            $result->result = $response->content ? json_encode($response->content) : null;
        }

        return (array) $result;
    }

    private function pushApi($wo, $action, $details)
    {
        $result = (object) ['success' => false];

        $baseUrl = config('site.asianet_api_url');
        $email = config('site.asianet_api_user');
        $password = config('site.asianet_api_password');

        $urlLogin = $baseUrl . '/amt/1.1/atm/generateToken';
        $urlPush = $baseUrl . '/amt/1.1/eda/engineerStatus';

        if (Cache::has('token')) $token = Cache::get('woaccesstoken');
        else {
            $login = Curl::to($urlLogin)
                ->withData(['email' => $email, 'password' => $password])
                ->withTimeout(120)
                ->asJson()
                ->returnResponseObject()
                ->post();

            if (isset($login->content) && isset($login->content->body->accessToken)) {
                $token = $login->content->body->accessToken;
                Cache::put('woaccesstoken', $token, 10);
            } else {
                $result->message = "ERROR API LOGIN (" . $login->status . ") " . ($login->content ? json_encode($login->content) : '');
                return $result;
            }
        }

        // GET ACTION --------------------------------------------------------------------------------------------------
        $serialNumber = null;
        $additionalUTP = null;
        $additionalDropCable = null;
        $fatPort = "";
        $bastURL = null;
        $evidenceURL = null;
        // $fatSignalMeasurement = null;
        // $signalLevelRX = null;
        $iptvCustomer = null;

        $ontSerialFromActivation = null;
        $ontSerialFromTesting = null;
        $ontSerialFromSolving = null;
        $ontSerialFromPreparation = null;

        $ont  = ["type" => 'ont', "serialNumber" => "", "macaddressont" => ""];
        $stb1 = ["type" => 'stb', "stbType" => "", "serialNumber" => "", "macAddressstb" => ""];
        $stb2 = ["type" => 'stb', "stbType" => "", "serialNumber" => "", "macAddressstb" => ""];
        $stb3 = ["type" => 'stb', "stbType" => "", "serialNumber" => "", "macAddressstb" => ""];

        if ($action->status->name == 'POST ACTIVATION') {
            $bastURL = route('wo.export.balap', $wo->id);
            $evidenceURL = route('wo.export.pdf', $wo->id);
        }

        foreach ($wo->actions as $act) {
            foreach ($act->details as $extra) {
                if (strtoupper($act->status->name) == 'ACTIVATION') {
                    if (strtolower($extra->detail->name) == 'sn ont') {
                        $ont['serialNumber'] = $extra->value;
                        $ontSerialFromActivation = $extra->value;
                    } else if (strtolower($extra->detail->name) == 'mac address ont') $ont['macaddressont'] = $extra->value;
                    else if (strtolower($extra->detail->name) == 'tipe stb 1') $stb1['stbType'] = ($opt = StatusDetailOption::find($extra->value)) ? $opt->option : '';
                    else if (strtolower($extra->detail->name) == 'sn stb 1') $stb1['serialNumber'] = $extra->value;
                    else if (strtolower($extra->detail->name) == 'mac address stb 1') $stb1['macAddressstb'] = $extra->value;
                    else if (strtolower($extra->detail->name) == 'tipe stb 2') $stb2['stbType'] = ($opt = StatusDetailOption::find($extra->value)) ? $opt->option : '';
                    else if (strtolower($extra->detail->name) == 'sn stb 2') $stb2['serialNumber'] = $extra->value;
                    else if (strtolower($extra->detail->name) == 'mac address stb 2') $stb2['macAddressstb'] = $extra->value;
                    else if (strtolower($extra->detail->name) == 'tipe stb 3') $stb3['stbType'] = ($opt = StatusDetailOption::find($extra->value)) ? $opt->option : '';
                    else if (strtolower($extra->detail->name) == 'sn stb 3') $stb3['serialNumber'] = $extra->value;
                    else if (strtolower($extra->detail->name) == 'mac address stb 3') $stb3['macAddressstb'] = $extra->value;
                    else if (strtolower($extra->detail->name) == 'serial number registration') $serialNumber = $extra->value;
                } else if (strtoupper($act->status->name) == 'PREPARATION') {
                    if ((strtolower($extra->detail->name) == 'ont serial number') && ($extra->detail->type == "text")) {
                        $ontSerialFromPreparation = $extra->value;
                    }
                } else if (strtoupper($act->status->name) == 'INSTALLATION') {
                    if (strtolower($extra->detail->name) == 'fat port') {
                        $fatPort = ($opt = StatusDetailOption::find($extra->value)) ? $opt->option : '';
                    }
                    //else if ((strtolower($extra->detail->name) == 'fat signal measurement (dbm)') && ($extra->detail->type == "number")) {
                    //     $fatSignalMeasurement = $extra->value;
                    // }
                } else if (strtoupper($act->status->name) == 'SOLVING') {
                    if ((strtolower($extra->detail->name) == 'new ont serial number') && ($extra->detail->type == "text")) {
                        $ontSerialFromSolving = $extra->value;
                        $ont['serialNumber'] = $extra->value;
                    }
                } else if (strtoupper($act->status->name) == 'DE-ACTIVATION') {
                    if (strtolower($extra->detail->name) == 'serial number unregistration') $serialNumber = $extra->value;
                } else if (strtoupper($act->status->name) == 'ADDITIONAL MATERIAL') {
                    if (strtolower($extra->detail->name) == 'kelebihan kabel dw') $additionalDropCable = $extra->value;
                    else if (strtolower($extra->detail->name) == 'kelebihan kabel utp') $additionalUTP = $extra->value;
                } else if (strtoupper($act->status->name) == 'TESTING') {
                    if (strtolower($extra->detail->name) == 'sn ont') {
                        $ontSerialFromTesting = $extra->value;
                    }
                    //else if ((strtolower($extra->detail->name) == 'signal level rx (dbm)') && ($extra->detail->type == "number")) {
                    //     $signalLevelRX = $extra->value;
                    // }
                } else if (strtoupper($act->status->name) == 'CHANGE CPE') {
                    if (strtolower($extra->detail->name) == 'serial number ont baru') {
                        $serialNumber = $extra->value;
                        $ont['serialNumber'] = $extra->value;
                    } else if (strtolower($extra->detail->name) == 'pelanggan iptv') {
                        $option = StatusDetailOption::find($extra->value);
                        if ($option) {
                            $iptvCustomer = (strtolower(trim($option->option)) == 'ya, stb (set top box)');
                        }
                    }
                }
                // else if (strtoupper($act->status->name) == 'POST ACTIVATION') {
                //     if (strtolower($extra->detail->name) == 'kelebihan kabel dw') $additionalDropCable = $extra->value;
                //     else if (strtolower($extra->detail->name) == 'kelebihan kabel utp') $additionalUTP = $extra->value;
                // }
            }
        }

        if (!empty($ontSerialFromSolving)) {
            $serialNumber = $ontSerialFromSolving;
            $ont['serialNumber'] = $ontSerialFromSolving;
        } else if (!empty($ontSerialFromTesting)) {
            $serialNumber = $ontSerialFromTesting;
            $ont['serialNumber'] = $ontSerialFromTesting;
        } else if (!empty($ontSerialFromActivation)) {
            $serialNumber = $ontSerialFromActivation;
            $ont['serialNumber'] = $ontSerialFromActivation;
        } else if (!empty($ontSerialFromPreparation)) {
            $serialNumber = $ontSerialFromPreparation;
            $ont['serialNumber'] = $ontSerialFromPreparation;
        }

        // if (!empty($ontSerialFromTesting)) {
        //     $ont['serialNumber'] = $ontSerialFromTesting;
        //     $serialNumber = $ontSerialFromTesting;
        // } else if (!empty($ontSerialFromActivation)) {
        //     $ont['serialNumber'] = $ontSerialFromActivation;
        //     $serialNumber = $ontSerialFromActivation;
        // }

        $cpe = [$ont, $stb1, $stb2, $stb3];

        $data = [
            'activityName' => (string) $action->wo->activity->name,
            'orderNumber' => (string) $action->wo->no_wo,
            'workFlowNumber' => (string) $action->wo->id,
            'orderStatus' => $action->status->name,
            'teamID' =>  $action->wo->fieldtech_id * 1,
            'serialNumber' => (string) $serialNumber,
            'longitude' => (float) $action->long,
            'latitude' => (float) $action->lat,
            'fatLongitude' => (float) $action->long,
            'fatLatitude' => (float) $action->lat,
            'additionalUTP' => (float) $additionalUTP,
            'additionalDropCable' => (float) $additionalDropCable,
            'bastURL' => $bastURL,
            'evidenceURL' => $evidenceURL,
            'fatport' => (string) $fatPort,
            'isIPTV' => (bool) $iptvCustomer,
            // 'fatSignalMeasurement(dBm)' => (string) $fatSignalMeasurement,
            // 'signalLevelRX(dBm)' => (string) $signalLevelRX,
            'cpe' => $cpe
        ];

        // PUSH API ----------------------------------------------------------------------------------------------------

        $response = Curl::to($urlPush)
            ->withData($data)
            ->withTimeout(120)
            ->withBearer($token)
            ->asJson()
            ->returnResponseObject()
            ->post();


        if ($response->status === 0) {
            $result->message = "Connection to the API timed out. Please try again";
            $result->status = 500;
            Log::info('No response from API', ['data' => $data]);
        } elseif ($response->status >= 200 && $response->status <= 490) {
            if ($content = $response->content) {
                if (isset($content->statusCode)) {
                    if ($response->status == 206 && $action->wo->activity->name == "TROUBLESHOOT") {
                        $result->message = 'Error response code 206';
                        $result->status = 206;
                        Log::info("TROUBLESHOOT activity received 206 response - treated as error: " . $result->message);
                    } else if ($response->status == 206 && $action->status->name == "ACTIVATION") {
                        $result->success = true;
                        $result->status = 206;
                        $result->message = "Hold, waiting from partner acknowledgements";
                    } else if ($response->status == 206 && $action->status->name == "CHECK MAC ADDRESS HSI") {
                        $result->success = true;
                        $result->status = 206;
                        $result->message = "Hold, waiting for MAC address verification";
                    } else if ($response->status == 206 && $action->status->name == "IPTV CUSTOMER") {
                        $result->success = true;
                        $result->status = 206;
                        $result->message = "Hold, waiting for IPTV acknowledgement";
                    } else if ($response->status == 200 || ($response->status == 206 && !in_array($action->status->name, ["ACTIVATION", "CHECK MAC ADDRESS HSI"]))) {
                        $result->success = true;
                        $result->status = 200;
                        $result->message = "Success";

                        if (isset($content->bodyExtended) && isset($content->bodyExtended->macAddressHsi)) {
                            $macAddressHsi = $content->bodyExtended->macAddressHsi;

                            try {
                                $detailId = StatusDetail::where('name', 'MAC ADDRESS HSI')->value('id');
                                if ($detailId) {
                                    ActionDetail::updateOrCreate(
                                        [
                                            'action_id' => $action->id,
                                            'detail_id' => $detailId,
                                        ],
                                        [
                                            'value' => $macAddressHsi,
                                        ]
                                    );
                                    Log::info("MAC ADDRESS HSI berhasil disimpan", [
                                        'wo_id' => $wo->id,
                                        'action_id' => $action->id,
                                        'value' => $macAddressHsi
                                    ]);
                                } else {
                                    Log::warning("StatusDetail 'MAC ADDRESS HSI' belum ada di master table");
                                }
                            } catch (\Exception $e) {
                                Log::error("Gagal simpan MAC ADDRESS HSI", ['error' => $e->getMessage()]);
                            }
                        } else {
                            Log::warning("MAC ADDRESS HSI tidak ditemukan di response body");
                        }
                    } else {
                        // Extract only the returnMessage for error cases
                        $responseContent =
                            json_encode($response);
                        $responseArray = json_decode($responseContent, true);

                        // $returnMessage
                        //     = $responseArray['content']['returnMessage'] . ", Status Code: " . $responseArray['content']['statusCode'] ?? 'Unknown error';

                        $returnMessage = 'Unknown error';
                        if ($responseArray && is_array($responseArray)) {
                            if (isset($responseArray['content']['returnMessage']) && isset($responseArray['content']['statusCode'])) {
                                $returnMessage = $responseArray['content']['returnMessage'] . ", Status Code: " . $responseArray['content']['statusCode'] ?? 'Unknown error';
                            } else {
                                Log::info("Cek Response Status Return Message FC PushApi: " .
                                    (isset($responseArray['content']['returnMessage'])
                                        ? $responseArray['content']['returnMessage']
                                        : 'returnMessage not found'));
                                Log::info("Cek Response Status Status Code FC PushApi: " .
                                    (isset($responseArray['content']['statusCode'])
                                        ? $responseArray['content']['statusCode']
                                        : 'statusCode not found'));
                            }
                        } else {
                            Log::info("Cek Response FC PushApi tidak ada respon array" . json_encode($responseArray));
                        }

                        $result->message = 'Error API engineer status response failed: ' . $returnMessage;


                        $result->status = $response->status ?? 500;
                        $result->data = [
                            'url' => $urlPush,
                            'dataPush' => $data,
                            'response' => (array) $content,
                        ];

                        Log::info("Cek result Message: " . $result->message . ", Status: " . ($result->status ?? 'Unknown'));
                        Log::info("Cek Response Array : " . json_encode($responseArray));
                    }
                } else {
                    $result->message = "Error: statusCode not found in response";
                    $result->status = $response->status ?? 500;
                }
            } else {
                $result->message = "Error: API response is null";
                $result->status = $response->status ?? 500;
            }
        } else {
            // Only return the error message if available
            $responseContent = json_encode($response);
            $responseArray = json_decode($responseContent, true);

            $returnMessage = 'Unknown error';
            if ($responseArray && is_array($responseArray)) {
                if (isset($responseArray['content']['returnMessage']) && isset($responseArray['content']['statusCode'])) {
                    $returnMessage = $responseArray['content']['returnMessage'] . ", Status Code: " . $responseArray['content']['statusCode'] ?? 'Unknown error';
                } else {
                    Log::info("Cek Response Status Return Message: " .
                        (isset($responseArray['content']['returnMessage'])
                            ? $responseArray['content']['returnMessage']
                            : 'returnMessage not found'));
                    Log::info("Cek Response Status Status Code: " .
                        (isset($responseArray['content']['statusCode'])
                            ? $responseArray['content']['statusCode']
                            : 'statusCode not found'));
                }
            } else {
                Log::info("Cek Response" . json_encode($responseArray));
            }
            $result->message = $returnMessage;
            $result->status = $response->status ?? 500;
        }

        $result->data = [
            'url' => $urlPush,
            'dataPush' => $data,
            'statusCode' => ($response && isset($response->status)) ? $response->status : 'null',
            'response' => isset($content) ? json_decode(json_encode($content), true) : null,
        ];

        Log::info('Response dari API:', $result->data);

        return $result;
    }

    private function pushApi2($action, $details)
    {
        $result = (object) ['success' => false];

        $baseUrl = config('site.asianet_api_url');
        $email = config('site.asianet_api_user');
        $password = config('site.asianet_api_password');

        $urlLogin = $baseUrl . '/amt/1.0/security/login';
        $urlPush = $baseUrl . '/amt/1.0/wfm/engineerstatus';


        if (Cache::has('token')) $token = Cache::get('woaccesstoken');
        else {
            $login = Curl::to($urlLogin)
                ->withData(['email' => $email, 'password' => $password])
                ->withTimeout(120)
                ->asJson()
                ->returnResponseObject()
                ->post();
            if (isset($login->content) && isset($login->content->accessToken)) {
                $token = $login->content->accessToken;
                Cache::put('woaccesstoken', $token, 10);
            } else {
                $result->message = "ERROR API LOGIN (" . $login->status . ") " . ($login->content ? json_encode($login->content) : '');
                return $result;
            }
        }


        // GET ACTION --------------------------------------------------------------------------------------------------
        $serialNumber = null;
        $additionalUTP = null;
        $additionalDropCable = null;

        /*
            Excess Material - Drop Wire
            Excess Material - UTP
         */

        foreach (json_decode($details) as $extra) {
            if ($extra && isset($extra->id)) {
                if ($detail = Master\StatusDetail::find($extra->id)) {
                    if ($detail->type != 'file') {
                        if (strtolower($detail->name) == 'ont serial number') $serialNumber = $extra->value;
                        else if (strtolower($detail->name) == 'serial number registration') $serialNumber = $extra->value;
                        else if (strtolower($detail->name) == 'serial number unregistration') $serialNumber = $extra->value;
                        else if (strtolower($detail->name) == 'excess material - drop wire') $additionalDropCable = $extra->value;
                        else if (strtolower($detail->name) == 'excess material - utp') $additionalUTP = $extra->value;
                    }
                }
            }
        }

        if ($action->status->name == "PREPARATION") $status = 'PREPARED';
        else if ($action->status->name == "IN PROGRESS") $status = 'ONGOING';
        else if ($action->status->name == "ARRIVED") $status = 'ARRIVED';
        else if ($action->status->name == "INSTALLATION") $status = 'TAGGED';
        else if ($action->status->name == "DE-INSTALLATION") $status = 'TAGGED';
        else if ($action->status->name == "ACTIVATION") $status = 'ACTIVATED';
        else if ($action->status->name == "DE-ACTIVATION") $status = 'ACTIVATED';
        else if ($action->status->name == "POST ACTIVATION") $status = 'COMPLETED';

        $data = [
            'activityName' => (string) $action->wo->activity->name,
            'orderNumber' => (string) $action->wo->no_wo,
            'workFlowNumber' => (string) $action->wo->id,
            'orderStatus' => $status,
            'teamID' => (string) $action->wo->fieldtech_id,
            'serialNumber' => (string) $serialNumber,
            'longitude' => (float) $action->long,
            'latitude' => (float) $action->lat,
            'fatLongitude' => (float) $action->long,
            'fatLatitude' => (float) $action->lat,
            'additionalUTP' => (float) $additionalUTP,
            'additionalDropCable' => (float) $additionalDropCable
        ];

        // PUSH API ----------------------------------------------------------------------------------------------------

        $response = Curl::to($urlPush)
            ->withData($data)
            ->withTimeout(120)
            ->withBearer($token)
            ->asJson()
            ->returnResponseObject()
            ->post();
        if ($response->status == 200 || $response->status == 400) {
            if ($content = $response->content) {
                if (isset($content->statusCode)) {
                    if (!$content->statusCode) {
                        $result->success = true;
                        $result->message = "Success";
                    } else $result->message = "Error API engineer status response failed (" . json_encode($content) . ")";

                    $result->data = [
                        'url' => $urlPush,
                        'dataPush' => $data,
                        'response' => (array) $content,
                    ];
                } else $result->message = "Error API engineer status statusCode Not Found";
            } else $result->message = "Error API engineer status (response is null)";
        } else {
            $result->message = "ERROR API ENGINEERSTATUS (" . $response->status . ") " . ($response->content ? json_encode($response->content) : '');
        }

        return $result;
    }

    private function actionValid($wo, $status, $user)
    {
        if (!$wo) return "WorkOrder Not Found!";
        if (!$status) return "Status Not Found!";
        if (!in_array($user->role_id, $status->roles) && $user->role_id != 20) return "Update Status ($status->name) Denied!";
        if (!in_array($wo->activity_id, $status->activities)) return $wo->activity->name . " ($status->name) Not Found!";
        if (!$status->show_on || !in_array($wo->lastAction->status_id, $status->show_on)) return "Not Show On $status->name";

        return null;
    }

    private function actionDetailPush($wo, $action, $details)
    {
        $details = is_string($details) ? json_decode($details) : $details;
        if (is_array($details)) {
            DB::beginTransaction();
            try {
                $fieldtechId = null;
                $startDate = null;
                $slotId = null;

                ActionDetail::where('action_id', $action->id)->delete();
                foreach ($details as $detail) {
                    $statusDetail = Master\StatusDetail::where('status_id', $action->status_id)->where('id', $detail->id)->first();
                    if ($statusDetail) {
                        if ($statusDetail->type == 'file') {
                            if ($detail->value && count($detail->value)) {
                                $actionDetail = ActionDetail::create(['action_id' => $action->id, 'detail_id' => $statusDetail->id]);
                                $watermark  = "QIFESS (WO: $action->wo_id) - (" . strtoupper(date('d M Y H:i')) . ")";
                                $watermark .= "\n" . $action->status->name;
                                $watermark .= "\n" . $statusDetail->name;
                                if ($action->lat && $action->long) $watermark .= "\nCoordinate ($action->lat, $action->long)";
                                foreach ($detail->value as $file) {
                                    if (is_object($file)) $actionDetail->files()->attach($file->id);
                                    else if ($fileid = FileUpload::push($file, 'action-detail-file', $watermark)) {
                                        $actionDetail->files()->attach($fileid);
                                    }
                                }
                            }
                        } else if ($statusDetail->type == 'signature') {
                            if ($detail->value) {
                                $fileid = FileUpload::push($detail->value, 'action-detail-signature');
                                ActionDetail::create([
                                    'action_id' => $action->id,
                                    'detail_id' => $statusDetail->id,
                                    'value' => $fileid,
                                ]);
                            }
                        } else {
                            $value = ActionDetail::create([
                                'action_id' => $action->id,
                                'detail_id' => $statusDetail->id,
                                'value' => isset($detail->value) ? $detail->value : null,
                            ]);
                        }

                        if ($statusDetail->triger) {
                            $value = isset($detail->value) ? $detail->value : null;
                            switch ($statusDetail->triger) {
                                case 'wo.fieldtech':
                                    $fieldtechId = $value;
                                    break;
                                case 'wo.startdate':
                                    $startDate = $value;
                                    break;
                                case 'wo.slot':
                                    $slotId = $value;
                                    break;
                                case 'wo.unbook':
                                    $wo->update(['fieldtech_id' => null, 'start_date' => null, 'slot_id' => null]);
                                    break;
                            }
                        }
                    }
                }

                if ($err = $this->fieldtechCheck($fieldtechId, $startDate, $slotId, $wo->id)) {
                    DB::rollback();
                    return ['success' => false, 'message' => "Team already have installation ticket", 'data' => $err];
                }

                if ($fieldtechId) $wo->update(['fieldtech_id' => $fieldtechId]);
                if ($startDate) $wo->update(['start_date' => $startDate]);
                if ($slotId) $wo->update(['slot_id' => $slotId]);

                DB::commit();
                return false;
            } catch (QueryException $error) {
                DB::rollback();
                return '500 (WO Action Detail) ' . $error->getMessage();
            }
        }

        return 'Error Action Details';
    }

    public function pushPart(Request $request, $id = null)
    {
        DB::beginTransaction();
        try {
            $input = [
                'wo_id' => $request->input('wo_id'),
                'type' => $request->input('type'),
                'code' => $request->input('code'),
                'name' => $request->input('name'),
                'serial' => $request->input('serial'),
                'model' => $request->input('model'),
                'description' => $request->input('description'),
            ];
            if ($id) {
                if ($data = Part::find($id)) {
                    $data->update($input);
                } else return ['success' => false, 'message' => 'Update Part Notfound...'];
            } else $data = Part::create($input);

            $data->files()->detach();
            if ($data && $files = $request->input('files')) {
                $files = json_decode($files);
                foreach ($files as $file) {
                    if (is_object($file)) $data->files()->attach($file->id);
                    else if ($fid = FileUpload::push($file, 'part')) {
                        $data->files()->attach($fid);
                    }
                }
            }

            DB::commit();
            return ['success' => true, 'message' => 'Success...'];
        } catch (QueryException $error) {
            DB::rollback();
            return ['success' => false, 'message' => '500 (Create WO)' . $error->getMessage()];
        }
    }

    public function rebooking(Request $request, $id = null)
    {
        if ($wo = Wo::find($id)) {
            $laststs = $wo->lastAction->status_id;
            $status = Master\Status::whereIn('id', [1214, 2214, 3214, 4214, 5214, 6214, 7214])->get();

            foreach ($status as $sts) {
                foreach ($sts->show_on as $sid) {
                    if ($sid == $laststs) {
                        if (!$date = $request->input('date')) return ['success' => false, 'message' => 'date is empty'];
                        if (!$slot = $request->input('slot_id')) return ['success' => false, 'message' => 'slot_id is empty'];
                        if (!$fieldtech = $request->input('fieldtech_id')) return ['success' => false, 'message' => 'fieldtech_id is empty'];
                        if (!$notes = $request->input('notes')) return ['success' => false, 'message' => 'notes is empty'];

                        if ($err = $this->fieldtechCheck($fieldtech, $date, $slot, $wo->id)) {
                            return ['success' => false, 'message' => 'Team already have installation ticket', 'data' => $err];
                        }

                        if (!Master\Slot::find($slot)) return ['success' => false, 'message' => 'slot_id not found'];
                        if (!Fieldtech::find($fieldtech)) return ['success' => false, 'message' => 'fieldtech_id not found'];

                        DB::beginTransaction();
                        try {
                            $action = Action::create([
                                'wo_id' => $wo->id,
                                'status_id' => $sts->id,
                                'note' => $notes,
                            ]);

                            // dd($sts->details);

                            foreach ($sts->details as $detail) {
                                $value = null;
                                if ($detail->property == 'fieldtech') $value = $fieldtech;
                                else if ($detail->property == 'startdate') $value = $date;
                                else if ($detail->property == 'slot') $value = $slot;

                                ActionDetail::create([
                                    'action_id' => $action->id,
                                    'detail_id' => $detail->id,
                                    'value' => $value,
                                ]);
                            }


                            // Update WO
                            $wo->update([
                                'last_action' => $action->id,
                                'start_date' => $date,
                                'slot_id' => $slot,
                                'fieldtech_id' => $fieldtech,
                            ]);

                            // Update atau buat data di WO Ongoing
                            $woOngoing = WoOngoing::where('wo_id', $wo->id)->first();

                            if ($woOngoing) {
                                $woOngoing->update([
                                    'last_action' => $action->id,
                                    'start_date' => $date,
                                    'slot_id' => $slot,
                                    'fieldtech_id' => $fieldtech,
                                ]);
                            }

                            DB::commit();
                            return ['success' => true, 'message' => 'Success!'];
                        } catch (Exception $e) {
                            DB::rollback();
                            return ['success' => false, 'message' => '500 ' . $e->getMessage()];
                        }
                    }
                }
            }

            return ['success' => false, 'message' => 'Rebooking not permitted!'];
        }
        return ['success' => false, 'message' => 'Undefined WO ID!'];
    }

    public function cancel(Request $request, $id = null)
    {
        $user = auth()->user();

        if ($wo = Wo::find($id)) {
            $laststs = $wo->lastAction->status_id;

            $closeStatus = [1610, 2610, 3610, 4610, 5610, 6610, 7430];
            if (in_array($laststs, $closeStatus)) {
                return ['success' => false, 'message' => 'Cancel not allowed for this status'];
            }

            if ($user->role_id == 20) {
                $allowedStatus = Master\Status::whereIn('id', [1914, 2910, 3910, 4910, 5910, 6910, 7910])->get();
            } elseif ($user->role_id == 10) {
                $allowedStatus = Master\Status::whereIn('id', [1914, 2914, 3914, 4914, 5914, 6914, 7914])->get();
            } else {
                return ['success' => false, 'message' => 'Unauthorized role'];
            }

            foreach ($allowedStatus as $sts) {
                if (in_array($laststs, $sts->show_on)) {
                    if (!$notes = $request->input('notes')) {
                        return ['success' => false, 'message' => 'Notes is empty'];
                    }

                    DB::beginTransaction();
                    try {
                        $action = Action::create([
                            'wo_id' => $wo->id,
                            'status_id' => $sts->id,
                            'note' => $notes,
                        ]);

                        $updateData = [
                            'last_action' => $action->id,
                        ];

                        if (!is_null($wo->close_date)) {
                            $updateData['close_date'] = null;
                        }

                        $wo->update($updateData);

                        // WoOngoing::updateOrCreate(
                        //     ['wo_id' => $wo->id],
                        //     ['last_action' => $action->id, 'close_date' => $updateData['close_date'] ?? $wo->close_date]
                        // );

                        $ongoing = WoOngoing::where('wo_id', $wo->id)->first();
                        if ($ongoing) {
                            $ongoing->update([
                                'last_action' => $action->id,
                                'close_date' => $updateData['close_date'] ?? $wo->close_date,
                            ]);
                        }

                        DB::commit();
                        return ['success' => true, 'message' => 'Success!'];
                    } catch (Exception $e) {
                        DB::rollback();
                        return ['success' => false, 'message' => '500 ' . $e->getMessage()];
                    }
                }
            }

            return ['success' => false, 'message' => 'Cancel not permitted!'];
        }

        return ['success' => false, 'message' => 'Undefined WO ID!'];
    }

    public function delete(Request $request)
    {
        if ($data = json_decode($request->data)) {
            $user = $request->user();
            $timestamp = date('Y-m-d H:i:s');

            DB::beginTransaction();
            try {
                // Update di tabel utama (po_wo)
                Wo::whereIn('id', $data)->update([
                    'deleted_by' => $user->id,
                    'deleted_at' => $timestamp
                ]);

                // Update di tabel mirror (wo_ongoing) berdasarkan wo_id
                WoOngoing::whereIn('wo_id', $data)->update([
                    'deleted_by' => $user->id,
                    'deleted_at' => $timestamp
                ]);

                DB::commit();
                return ['success' => true, 'message' => 'Success!'];
            } catch (Exception $e) {
                DB::rollback();
                return ['success' => false, 'message' => '500 ' . $e->getMessage()];
            }
        }

        return ['success' => false, 'message' => 'No Data!'];
    }

    public function deleteAction(Request $request, $id)
    {
        $action = Action::find($id);

        if ($action) {
            DB::beginTransaction();

            try {
                $workOrder = Wo::find($action->wo_id);
                $woOngoing = WoOngoing::where('wo_id', $action->wo_id)->first();

                if ($workOrder && $workOrder->is_hold == 1) {
                    $workOrder->update(['is_hold' => 0]);
                    if ($woOngoing) {
                        $woOngoing->update(['is_hold' => 0]);
                    }
                }

                if ($workOrder && $workOrder->close_date) {
                    $workOrder->update(['close_date' => null]);
                }

                if ($woOngoing && $woOngoing->close_date) {
                    $woOngoing->update(['close_date' => null]);
                }

                Action::where('wo_id', $action->wo_id)
                    ->where('created_at', '>=', $action->created_at)
                    ->orderBy('created_at')
                    ->delete();

                if ($workOrder) {
                    $workOrder->updateLastAction();
                    if ($woOngoing) {
                        $woOngoing->update(['last_action' => $workOrder->last_action]);
                    }
                }

                DB::commit();
                return ['success' => true, 'message' => 'Success!'];
            } catch (Exception $error) {
                DB::rollback();
                return ['success' => false, 'message' => '500 ' . $error->getMessage()];
            }
        }

        return ['success' => false, 'message' => 'Undefined Action ID'];
    }

    public function deactivation(Request $request, $id = null)
    {
        if ($wo = Wo::find($id)) {
            if ($wo->activity_id !== 5) {
                return response()->json([
                    'success' => false,
                    'message' => 'Deactivation not permitted! Invalid termination activity.'
                ], 403);
            }

            $laststs = $wo->lastAction->status_id ?? null;

            if ($laststs !== 5110) {
                return response()->json([
                    'success' => false,
                    'message' => "Deactivation not permitted! Invalid booking status phase."
                ], 403);
            }

            $status = Master\Status::where('id', 5420)->first();

            if ($status && is_array($status->show_on)) {
                foreach ($status->show_on as $sid) {
                    if ($sid == $laststs) {
                        if (!$port_deactivation = $request->input('port_deactivation_in_olt')) return ['success' => false, 'message' => 'Port Deactivation In OLT is empty'];
                        if (!$include_stb = $request->input('include_stb')) return ['success' => false, 'message' => 'Include STB is empty'];
                        if (!$ont_type = $request->input('ont_type')) return ['success' => false, 'message' => 'ONT Type is empty'];
                        if (!$sn_ont = $request->input('sn_ont')) return ['success' => false, 'message' => 'SN ONT is empty'];

                        DB::beginTransaction();
                        try {
                            $action = Action::create([
                                'wo_id' => $wo->id,
                                'status_id' => $status->id,
                            ]);

                            foreach ($status->details as $detail) {
                                $detailId = $detail->getAttribute('id');
                                $value = null;

                                if ($detailId == "281399") $value = $port_deactivation;
                                else if ($detailId == "281815") $value = $include_stb;
                                else if ($detailId == "281816") $value = $ont_type;
                                else if ($detailId == "281817") $value = $sn_ont;


                                ActionDetail::create([
                                    'action_id' => $action->id,
                                    'detail_id' => $detail->id,
                                    'value' => $value,
                                ]);
                            }

                            $wo->update([
                                'last_action' => $action->id,
                            ]);

                            // WoOngoing::updateOrCreate(
                            //     ['wo_id' => $wo->id],
                            //     [
                            //         'last_action' => $action->id,
                            //     ]
                            // );

                            $woOngoing = WoOngoing::where('wo_id', $wo->id)->first();

                            if ($woOngoing) {
                                $woOngoing->update([
                                    'last_action' => $action->id,
                                ]);
                            } else {
                                DB::rollback();
                                return ['success' => false, 'message' => 'WO Ongoing not found for update'];
                            }

                            DB::commit();
                            return ['success' => true, 'message' => 'Success!'];
                        } catch (Exception $e) {
                            DB::rollback();
                            return ['success' => false, 'message' => '500 ' . $e->getMessage()];
                        }
                    }
                }
            }

            return ['success' => false, 'message' => 'De-Activation not permitted!'];
        }
        return ['success' => false, 'message' => 'Undefined WO ID!'];
    }

    public function getOntTypeOptions()
    {
        $options = StatusDetailOption::where('detail_id', 281816)->get();

        if ($options->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No data available for ONT Type.'
            ], 404);
        }

        $data = $options->map(function ($option) {
            return [
                'id' => $option->id,
                'name' => $option->option
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
    public function getIncludeStbOptions()
    {
        $options = StatusDetailOption::where('detail_id', 281815)->get();

        if ($options->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No data available for Include STB.'
            ], 404);
        }

        $data = $options->map(function ($option) {
            return [
                'id' => $option->id,
                'name' => $option->option
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function deletePart(Request $request)
    {
        if ($id = $request->input('id')) {
            Part::find($id)->delete();
            return ['success' => true, 'message' => 'Success!'];
        }
        return ['success' => false, 'message' => 'No Data!'];
    }

    // tes pakai temp table ======================================
    public function exportExcel(Request $request)
    {
        $user = $request->user();

        $titles = [["WORKORDER", 'h2'], ["Asianet", 'h3']];

        $query = [];

        if ($search = $request->input('query')) {
            $query[] = "(A.id LIKE '%$search%' OR A.no_wo LIKE '%$search%' OR A.description LIKE '%$search%' OR G1.name LIKE '%$search%' OR G2.name LIKE '%$search%' OR I.name LIKE '%$search%')";
        }

        if ($request->input('archive')) {
            $query[] = "(A.close_date IS NOT NULL)";
            array_push($titles, ['Archive Data', 'h4']);
        } elseif ($request->input('filter-hold') === "2") {
            array_push($titles, ['Data Is Hold', 'h4']);
        } else {
            $mindate = date('Y-m-d', strtotime('-0 days'));
            $query[] = "(A.close_date IS NULL OR A.close_date >= '$mindate')";
            array_push($titles, ['Data On Going', 'h4']);
        }

        // FILTER ------------------------------------------------------------------------------------------------------
        if (!$search && ($filter = $request->input('filterDate'))) {
            $m = date('Y-m', strtotime("$filter 00:00:00")) . '%';
            $query[] = "(A.start_date LIKE '$m')";

            $month = date('F Y', strtotime("$filter 00:00:00"));
            array_push($titles, [$month, 'h4']);
        }
        if ($filter = $request->input('filter-status')) if ($filter != 'null') $query[] = "(B.status_id = '$filter')";
        if ($filter = $request->input('filter-activity')) $query[] = "(A.activity_id = '$filter')";
        if ($filter = $request->input('filter-service')) $query[] = "(A.service_id = '$filter')";
        if ($filter = $request->input('filter-vendor')) $query[] = "(A.vendor_id = '$filter')";
        if ($filter = $request->input('filter-client')) $query[] = "(A.client_id = '$filter')";
        if ($filter = $request->input('filter-owner')) $query[] = "(A.owner_id = '$filter')";
        if ($filter = $request->input('filter-hold')) {
            if ($filter === "2") {
                $query[] = "(A.is_hold = '1')";
            } elseif ($filter === "1") {
                $query[] = "(A.is_hold = '0')";
            }
        }

        // FILTER BY USER AUTH -----------------------------------------------------------------------------------------
        if ($ftr = $user->owners) $query[] = "(A.owner_id = '$ftr') ";
        if ($ftr = $user->activities) $query[] = "(A.activity_id = '$ftr') ";
        if ($ftr = $user->client_id) $query[] = "(A.client_id = '$ftr') ";
        if ($ftr = $user->vendor_id) $query[] = "(A.vendor_id = '$ftr') ";
        if ($ftr = $user->fieldtech_id) $query[] = "(A.fieldtech_id = '$ftr') ";

        $where = '';
        if (count($query)) {
            $query = implode(" AND ", $query);
            $where = " AND $query";
        }

        // Query utama
        $sql = "SELECT A.*,
                    B.created_at AS lastupdate_at,
                    B1.`name` AS status_name,
                    C.`name` AS activity_name,
                    D.`name` AS service_name,
                    E.`name` AS owner_name,
                    F.`name` AS client_name,
                    G1.`name` AS site_name,
                    G1.`lat` AS site_lat,
                    G1.`long` AS site_long,
                    G1.`address` AS site_address,
                    G1.`pic_phone` AS site_phone,
                    G2.`name` AS remove_site_name,
                    H.`name` AS vendor_name,
                    I.`name` AS fieldtech_name,
                    J.`name` AS created_by_name,
                    K.`name` AS slot,
                    DATEDIFF(DATE(NOW()), A.start_date) AS duration
                FROM po_wo A
                    LEFT JOIN po_wo_action B ON A.last_action = B.id AND B.deleted_at IS NULL
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
                    LEFT JOIN po_wo_m_slot K ON A.slot_id = K.id
                WHERE A.deleted_at IS NULL
                $where";


        $data = DB::select(DB::raw($sql));
        $columns = [
            ["text" => "ID", "dataIndex" => "id", "width" => 115],
            ["text" => "TICKET ID", "dataIndex" => "no_wo", "width" => 115],
            ["text" => "SERVICE", "dataIndex" => "service_name", "width" => 100, "align" => "center"],
            ["text" => "ACTIVITY", "dataIndex" => "activity_name", "width" => 150, "align" => "center"],
            ["text" => "CLIENT", "dataIndex" => "client_name", "width" => 150],
            ["text" => "SITE", "dataIndex" => "site_name", "width" => 200],
            ["text" => "LATITUDE", "dataIndex" => "site_lat", "width" => 150],
            ["text" => "LONGITUDE", "dataIndex" => "site_long", "width" => 150],
            ["text" => "SITE ADDRESS", "dataIndex" => "site_address", "width" => 250],
            ["text" => "SITE PHONE", "dataIndex" => "site_phone", "width" => 150],
            ["text" => "AREA", "dataIndex" => "vendor_name", "width" => 200],
            ["text" => "TEAM", "dataIndex" => "fieldtech_name", "width" => 250],
            ["text" => "DURATION (DAY)", "dataIndex" => "duration", "align" => "center", "width" => 100, 'type' => 'int'],
            ["text" => "BOOKING DATE", "dataIndex" => "start_date", "type" => "date", "align" => "center", "width" => 100],
            ["text" => "BOOKING SLOT", "dataIndex" => "slot", "align" => "center", "width" => 150],
            ["text" => "CREATED BY", "dataIndex" => "created_by_name", "width" => 200],
            ["text" => "CREATED DATE", "dataIndex" => "created_at", "type" => "date", "align" => "center", "width" => 100],
            ["text" => "LAST STATUS", "dataIndex" => "status_name", "width" => 200],
            ["text" => "LAST STATUS DATE", "dataIndex" => "lastupdate_at", "type" => "date", "align" => "center", "width" => 100],
            // [
            //     "text" => "TOTAL STB",
            //     "dataIndex" => "extrafield",
            //     "width" => 100,
            //     "align" => "center",
            //     "renderer" => function ($value) {

            //         $data = json_decode($value);

            //         if (isset($data->total_stb)) {
            //             return $data->total_stb;
            //         }

            //         return ($value === null || $value == 0) ? '-' : $value;
            //     }
            // ],
            // [
            //     "text" => "ONT SERIALNUMBER",
            //     "dataIndex" => "extrafield",
            //     "width" => 200,
            //     "renderer" => function ($value) {
            //         $data = json_decode($value);

            //         if (isset($data->ont_serial)) {
            //             return $data->ont_serial;
            //         }

            //         return "";
            //     }
            // ],
            ["text" => "DESCRIPTION", "dataIndex" => "description", "width" => 500],
            // [
            //     "text" => "NAMA JALAN",
            //     "dataIndex" => "extrafield",
            //     "width" => 400,
            //     "renderer" => function ($value) {
            //         $data = json_decode($value);

            //         if (isset($data->contact_address)) {
            //             return $data->contact_address;
            //         }

            //         return "";
            //     }
            // ],
            // [
            //     "text" => "RW",
            //     "dataIndex" => "extrafield",
            //     "width" => 100,
            //     "renderer" => function ($value) {
            //         $data = json_decode($value);

            //         if (isset($data->contact_rw)) {
            //             return $data->contact_rw;
            //         }

            //         return "";
            //     }
            // ],
            // [
            //     "text" => "RT",
            //     "dataIndex" => "extrafield",
            //     "width" => 100,
            //     "renderer" => function ($value) {
            //         $data = json_decode($value);

            //         if (isset($data->contact_rt)) {
            //             return $data->contact_rt;
            //         }

            //         return "";
            //     }
            // ],
            // [
            //     "text" => "NOMOR RUMAH",
            //     "dataIndex" => "extrafield",
            //     "width" => 100,
            //     "renderer" => function ($value) {
            //         $data = json_decode($value);

            //         if (isset($data->contact_no)) {
            //             return $data->contact_no;
            //         }

            //         return "";
            //     }
            // ],
            // [
            //     "text" => "NOMOR KONTAK PELANGGAN",
            //     "dataIndex" => "extrafield",
            //     "width" => 200,
            //     "renderer" => function ($value) {
            //         $data = json_decode($value);

            //         if (isset($data->contact_phone)) {
            //             return $data->contact_phone;
            //         }

            //         return "";
            //     }
            // ],
            // ["text" => "HOLD", "dataIndex" => "is_hold", "width" => 100, "align" => "center", "renderer" => function ($value) {
            //     return $value == 1 ? 'HOLD' : '-';
            // }],
            // [
            //     "text" => "SN ACT",
            //     "dataIndex" => "extrafield",
            //     "width" => 200,
            //     "renderer" => function ($value) {
            //         $data = json_decode($value);

            //         if (isset($data->sn_ont_activation)) {
            //             return $data->sn_ont_activation;
            //         }

            //         return "";
            //     }
            // ],
            // [
            //     "text" => "SN TESTING",
            //     "dataIndex" => "extrafield",
            //     "width" => 200,
            //     "renderer" => function ($value) {
            //         $data = json_decode($value);

            //         if (isset($data->sn_ont_testing)) {
            //             return $data->sn_ont_testing;
            //         }

            //         return "";
            //     }
            // ],
            // [
            //     "text" => "BARCODE KABEL KODE",
            //     "dataIndex" => "extrafield",
            //     "width" => 300,
            //     "renderer" => function ($value) {
            //         $data = json_decode($value);

            //         if (isset($data->input_kabel_kode)) {
            //             return $data->input_kabel_kode;
            //         }

            //         return "";
            //     }
            // ],
            // [
            //     "text" => "TECHNICIAN NAME",
            //     "dataIndex" => "extrafield",
            //     "width" => 300,
            //     "renderer" => function ($value) {
            //         $data = json_decode($value);

            //         if (isset($data->technician_name)) {
            //             return $data->technician_name;
            //         }

            //         return "";
            //     }
            // ],
        ];

        $footers = ['Total Count: ' . count($data) . ' Row', ' ', 'Field Service Management', 'Downloaded (QIFESS)` (' . date('d F Y H:i:s') . ')'];
        $params = array(
            // 'title' => $titles,
            'columns' => $columns,
            'filename' => 'WO ' . date("YmdHis"),
            'data' => $data,
            'footer' => $footers,
        );

        $excel = new ExportExcel($params);
        $excel->run($params);
    }

    public function exportPdf(Request $request, $id = null)
    {
        $user = $request->user();
        $view = 'reports.wo_pdf';
        $data = Wo::find($id);

        $params = ['user' => $user, 'data' => $data];
        $html = view($view, $params);
        $pdf = PDF::loadHtml($html);
        return $pdf->stream("WO ($data->id).pdf");
    }

    public function exportBalapPdf(Request $request, $id = null)
    {
        $user = $request->user();
        if (!$data = Wo::where('no_wo', $id)->first()) {
            $data = Wo::find($id);
        }

        if ($data) {

            $params = [
                'user' => $user,
                'data' => $data,
            ];

            $params['time_start'] = null;
            $params['time_finish'] = null;
            $params['internet'] = null;
            $params['totalStb'] = null;
            $params['emUtp'] = 0;
            $params['emWire'] = 0;
            $params['ontType'] = null;
            $params['ontSN'] = null;
            $params['ontMac'] = null;
            $params['stbType1'] = null;
            $params['stbType2'] = null;
            $params['stbType3'] = null;
            $params['stbSN1'] = null;
            $params['stbSN2'] = null;
            $params['stbSN3'] = null;
            $params['ttdFieldtech'] = null;
            $params['ttdCustomer'] = null;
            $params['ttdFieldtechName'] = null;
            $params['ttdCustomerName'] = null;
            $params['lastNote'] = null;
            $params['ispCustomerId'] = null;

            if ($data) {
                foreach ($data->actions as $action) {
                    if (str_contains(strtoupper($action->status->name), 'INSTALLATION')) {
                        foreach ($action->details as $detail) {
                            if (strtoupper($detail->detail->name) == 'ONT TYPE') {
                                $params['ontType'] = $detail->valueOption->option;
                            }
                        }
                        $params['time_start'] = $action->created_at;
                    } else if (str_contains(strtoupper($action->status->name), 'ADDITIONAL MATERIAL')) {
                        foreach ($action->details as $detail) {
                            if (strtoupper($detail->detail->name) == 'KELEBIHAN KABEL DW') {
                                $params['emWire'] = $detail->value;
                            } else if (strtoupper($detail->detail->name) == 'KELEBIHAN KABEL UTP') {
                                $params['emUtp'] = $detail->value;
                            }
                        }
                    } else if (str_contains(strtoupper($action->status->name), 'POST ACTIVATION')) {
                        $params['lastNote'] = $action->note;
                        $params['time_finish'] = $action->created_at;
                        foreach ($action->details as $detail) {
                            if (strtoupper($detail->detail->name) == 'KELEBIHAN KABEL DW') {
                                $params['emWire'] = $detail->value;
                            } else if (strtoupper($detail->detail->name) == 'KELEBIHAN KABEL UTP') {
                                $params['emUtp'] = $detail->value;
                            } else if (strtoupper($detail->detail->name) == 'SIGNATURE INSTALLER') {
                                $params['ttdFieldtech'] = $detail->value;
                            } else if (strtoupper($detail->detail->name) == 'SIGNATURE PIC') {
                                $params['ttdCustomer'] = $detail->value;
                            } else if (strtoupper($detail->detail->name) == 'CUSTOMER NAME') {
                                $params['ttdCustomerName'] = $detail->value;
                            } else if (strtoupper($detail->detail->name) == 'TECHNICIAN NAME') {
                                $params['ttdFieldtechName'] = $detail->value;
                            } else if (strtoupper($detail->detail->name) == 'ISP CUSTOMER ID') {
                                $params['ispCustomerId'] = $detail->value;
                            }
                        }
                    } else if (str_contains(strtoupper($action->status->name), 'DE-ACTIVATION')) {
                        foreach ($action->details as $detail) {

                            if (strtoupper($detail->detail->name) == 'ONT TYPE') {
                                $params['ontType'] = $detail->valueOption->option;
                            } else if (strtoupper($detail->detail->name) == 'SN ONT') {
                                $params['ontSN'] = $detail->value;
                            } else if (strtoupper($detail->detail->name) == 'SN STB 1') {
                                $params['stbSN1'] = $detail->value;
                            } else if (strtoupper($detail->detail->name) == 'TIPE STB 1') {
                                $params['stbType1'] = $detail->valueOption ? $detail->valueOption->option : null;
                            } else if (strtoupper($detail->detail->name) == 'SN STB 2') {
                                $params['stbSN2'] = $detail->value;
                            } else if (strtoupper($detail->detail->name) == 'TIPE STB 2') {
                                $params['stbType2'] = $detail->valueOption ? $detail->valueOption->option : null;
                            } else if (strtoupper($detail->detail->name) == 'SN STB 3') {
                                $params['stbSN3'] = $detail->value;
                            } else if (strtoupper($detail->detail->name) == 'TIPE STB 3') {
                                $params['stbType3'] = $detail->valueOption ? $detail->valueOption->option : null;
                            }
                        }
                    } else if (str_contains(strtoupper($action->status->name), 'ACTIVATION')) {
                        foreach ($action->details as $detail) {

                            if (strtoupper($detail->detail->name) == 'QOS REGISTRATION') {
                                $params['internet'] = $detail->valueOption ? $detail->valueOption->option : null;
                            } else if (strtoupper($detail->detail->name) == 'TOTAL STB') {
                                $params['totalStb'] = $detail->value;
                            } else if (strtoupper($detail->detail->name) == 'MAC ADDRESS ONT') {
                                $params['ontMac'] = $detail->value;
                            } else if (strtoupper($detail->detail->name) == 'SN STB 1') {
                                $params['stbSN1'] = $detail->value;
                            } else if (strtoupper($detail->detail->name) == 'TIPE STB 1') {
                                $params['stbType1'] = $detail->valueOption ? $detail->valueOption->option : null;
                            } else if (strtoupper($detail->detail->name) == 'TIPE STB 2') {
                                $params['stbType2'] = $detail->valueOption ? $detail->valueOption->option : null;
                            } else if (strtoupper($detail->detail->name) == 'SN STB 2') {
                                $params['stbSN2'] = $detail->value;
                            } else if (strtoupper($detail->detail->name) == 'TIPE STB 3') {
                                $params['stbType3'] = $detail->valueOption ? $detail->valueOption->option : null;
                            } else if (strtoupper($detail->detail->name) == 'SN STB 3') {
                                $params['stbSN3'] = $detail->value;
                            } else if (strtoupper($detail->detail->name) == 'SN ONT') {
                                $params['ontSN'] = $detail->value;
                            }
                        }
                    } else if (str_contains(strtoupper($action->status->name), 'PREPARATION')) {
                        foreach ($action->details as $detail) {
                            if ($detail->detail->type != 'file' && (strtoupper($detail->detail->name) == 'ONT TYPE')) {
                                $params['*ontType'] = $detail->valueOption ? $detail->valueOption->option : null;
                            } else if ($detail->detail->type != 'file' && (strtoupper($detail->detail->name) == 'ONT SERIAL NUMBER')) {
                                $params['ontSN'] = $detail->value;
                            } else if (strtoupper($detail->detail->name) == 'TYPE STB 1') {
                                $params['*stbType1'] = $detail->valueOption ? $detail->valueOption->option : null;
                            } else if (strtoupper($detail->detail->name) == 'TYPE STB 2') {
                                $params['stbType2'] = $detail->valueOption ? $detail->valueOption->option : null;
                            } else if (strtoupper($detail->detail->name) == 'SN STB 2') {
                                $params['stbSN2'] = $detail->value;
                            } else if (strtoupper($detail->detail->name) == 'TYPE STB 3') {
                                $params['stbType3'] = $detail->valueOption ? $detail->valueOption->option : null;
                            } else if (strtoupper($detail->detail->name) == 'SN STB 3') {
                                $params['stbSN3'] = $detail->value;
                            }
                        }
                    } else if (str_contains(strtoupper($action->status->name), 'CLOSED')) {
                        $params['lastNote'] = $action->note;
                        $params['time_finish'] = $action->created_at;
                        foreach ($action->details as $detail) {
                            if (strtoupper($detail->detail->name) == 'SIGNATURE INSTALLER') {
                                $params['ttdFieldtech'] = $detail->value;
                            } else if (strtoupper($detail->detail->name) == 'SIGNATURE PIC') {
                                $params['ttdCustomer'] = $detail->value;
                            } else if (strtoupper($detail->detail->name) == 'CUSTOMER NAME') {
                                $params['ttdCustomerName'] = $detail->value;
                            } else if (strtoupper($detail->detail->name) == 'TECHNICIAN NAME') {
                                $params['ttdFieldtechName'] = $detail->value;
                            }
                        }
                    }
                }
            }

            if (in_array($data->client_id, [3, 7])) $view = 'reports.wo_balap_hifi_pdf';
            else if (in_array($data->client_id, [4])) $view = 'reports.wo_balap_taranet_pdf';
            else if (in_array($data->client_id, [5])) $view = 'reports.wo_balap_relab_pdf';
            else if (in_array($data->client_id, [6])) $view = 'reports.wo_balap_dankom_pdf';
            else if (in_array($data->client_id, [8])) $view = 'reports.wo_balap_viberlink_pdf';
            else if (in_array($data->client_id, [9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 21, 22, 23, 24, 25, 26, 28, 29, 30, 31, 32])) $view = 'reports.wo_balap_all_pdf';


            $html = view($view, $params);
            $pdf = PDF::loadHtml($html);
            return $pdf->stream("BALAP ($data->id).pdf");
        }
        return abort(404);
    }

    public function postActivationTerminate(Request $request)
    {
        $ids = $request->input('ids', []);
        $notes = $request->input('notes');

        if (!is_array($ids) || empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'WO IDs is required'
            ], 422);
        }

        if (!$notes) {
            return response()->json([
                'success' => false,
                'message' => 'Notes is empty'
            ], 422);
        }

        $status = Master\Status::find(5610);

        if (!$status) {
            return response()->json([
                'success' => false,
                'message' => 'Status 5610 not found'
            ], 404);
        }

        DB::beginTransaction();
        try {
            foreach ($ids as $id) {

                $wo = Wo::find($id);

                if (!$wo) {
                    throw new \Exception("WO ID {$id} not found");
                }

                if ($wo->activity_id !== 5) {
                    throw new \Exception("WO ID {$id} invalid activity");
                }

                $action = Action::create([
                    'wo_id'     => $wo->id,
                    'status_id' => $status->id,
                    'note'      => $notes,
                ]);

                $wo->update([
                    'last_action' => $action->id,
                    'close_date'  => $action->created_at
                ]);

                WoOngoing::updateOrCreate(
                    ['wo_id' => $wo->id],
                    [
                        'last_action' => $action->id,
                        'close_date'  => $action->created_at
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Post Activation Terminate success'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
