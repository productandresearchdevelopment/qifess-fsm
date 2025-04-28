<?php

namespace App\Controllers\Maps;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libraries\ExportExcel;
use App\Models\Clients\Client;
use App\Models\Exports\ExportRawDataSummary;
use App\Models\Exports\ExportSummaryRow;
use App\Models\Fieldteches\Fieldtech;
use App\Models\Services\Service;
use App\Models\Sites\Site;
use App\Models\Vendors\Vendor;
use App\Models\WorkOrders\Action;
use App\Models\WorkOrders\Masters\Activity;
use App\Models\WorkOrders\Masters\Slot;
use App\Models\WorkOrders\Masters\Status;
use App\Models\WorkOrders\WorkOrder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Map extends Controller
{
    public function index(Request $request)
    {
        $vendors = Vendor::select('id', 'name')->get();
        $activities = Activity::select('id', 'name', 'alias')->get();
        return view('map.main', [
            'vendors' => $vendors,
            'activities' => $activities,
        ]);
    }

    public function getSites(Request $request)
    {
        $query = Site::with('vendor');

        if ($request->has(['min_lat', 'max_lat', 'min_lng', 'max_lng'])) {
            $query->whereBetween('lat', [$request->min_lat, $request->max_lat])
                ->whereBetween('long', [$request->min_lng, $request->max_lng]);
        }

        if ($request->filled('vendor_id') && $request->vendor_id !== 'all') {
            $query->where('vendor_id', $request->vendor_id);
        }

        if ($request->filled('site_id') && $request->site_id !== 'all') {
            $query->where('id', $request->site_id);
        }

        if ($request->filled('fieldtech_id') && $request->fieldtech_id !== 'all') {
            $fieldtechId = $request->fieldtech_id;
            $query->whereHas('workorders', function ($query) use ($fieldtechId) {
                $query->where('fieldtech_id', $fieldtechId);
            });
        }

        if ($request->filled('activity_id') && $request->activity_id !== 'all') {
            $activityId = $request->activity_id;
            $query->whereHas('workorders', function ($query) use ($activityId) {
                $query->where('activity_id', $activityId);
            });
        }

        if ($request->filled('vendor_name')) {
            $vendorName = $request->vendor_name;
            $query->whereHas('workorders.fieldtech', function ($query) use ($vendorName) {
                $query->where('vendor_name', 'LIKE', "%{$vendorName}%");
            });
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = date('Y-m-d 00:00:00', strtotime($request->start_date));
            $endDate = date('Y-m-d 23:59:59', strtotime($request->end_date));
            $query->whereHas('workorders', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            });
        }

        $sites = $query->with([
            'vendor',
            'workorders.fieldtech',
            'workorders.lastAction.status',
            'workorders.activity'
        ])
            ->orderBy('updated_at', 'desc')
            ->limit(2000)
            ->get()
            ->map(function ($site) {

                $latestWO = $site->workorders->sortByDesc('updated_at')->first();
                $fieldtechName = $latestWO->fieldtech->name ?? 'N/A';
                $vendorName = $latestWO->fieldtech->vendor_name ?? 'N/A';
                $colorStatus = $latestWO->lastAction->status->color ?? "#000000";
                $activityName = $latestWO->activity->name ?? 'N/A';
                $activityId = $latestWO->activity_id ?? null;

                return [
                    'id' => $site->id,
                    'name' => $site->name ?? 'N/A',
                    'vendor' => $site->vendor->name ?? 'N/A',
                    'vendor_id' => $site->vendor_id ?? 'N/A',
                    'latitude' => $site->lat ?? null,
                    'longitude' => $site->long ?? null,
                    'fieldtech_name' => $fieldtechName,
                    'vendor_name' => $vendorName,
                    'color_marker' => $colorStatus,
                    'fieldtech_id' => $latestWO->fieldtech_id ?? null,
                    'activity_name' => $activityName,
                    'activity_id' => $activityId ?? null
                ];
            });

        return response()->json($sites);
    }


    // public function countSites(Request $request)
    // {
    //     $query = Vendor::select('id', 'name');

    //     if ($request->has('vendor_id') && $request->vendor_id !== 'all') {
    //         $query->where('id', $request->vendor_id);
    //     }

    //     $vendors = $query->get();

    //     $counts = $vendors->map(function ($vendor) use ($request) {
    //         $siteQuery = Site::where('vendor_id', $vendor->id);

    //         if ($request->has('site_id') && $request->site_id !== 'all') {
    //             $siteQuery->where('id', $request->site_id);
    //         }

    //         $totalSite = $siteQuery->count();

    //         return [
    //             'id' => $vendor->id,
    //             'name' => $vendor->name,
    //             'total_site' => $totalSite
    //         ];
    //     });

    //     return response()->json($counts);
    // }

    public function summary(Request $request)
    {
        $cacheKey = 'summary_data_' . md5(serialize($request->all()));
        $cacheDuration = 60 * 24; // menit (1 hari)

        $data = Cache::remember(
            $cacheKey,
            $cacheDuration,
            function () use ($request) {
                $query = WorkOrder::query()
                    ->selectRaw("
                    GROUP_CONCAT(po_wo.id) as ids,
                    po_wo.fieldtech_id,
                    po_wo.vendor_id,
                    po_wo.activity_id,
                    COUNT(DISTINCT po_wo.id) as total_ticket,
                    COUNT(DISTINCT CASE WHEN a.status_id IN (1432, 4432,5610, 6432) THEN po_wo.id END) as closed_ticket
                ")
                    ->leftJoin('po_wo_action as a', 'po_wo.id', '=', 'a.wo_id')
                    ->groupBy('po_wo.fieldtech_id', 'po_wo.vendor_id', 'po_wo.activity_id');

                if ($request->filled('vendor_id') && $request->vendor_id !== 'all') {
                    $query->where('vendor_id', $request->vendor_id);
                }

                if ($request->filled('site_id') && $request->site_id !== 'all') {
                    $query->where('site_id', $request->site_id);
                }

                if ($request->filled('fieldtech_id') && $request->fieldtech_id !== 'all') {
                    $query->where('fieldtech_id', $request->fieldtech_id);
                }

                if ($request->filled('activity_id') && $request->activity_id !== 'all') {
                    $query->where('activity_id', $request->activity_id);
                }

                if ($request->filled('vendor_name')) {
                    $vendorName = $request->vendor_name;
                    $query->whereHas('fieldtech', function ($q) use ($vendorName) {
                        $q->where('vendor_name', 'LIKE', "%{$vendorName}%");
                    });
                }

                if ($request->filled('start_date') && $request->filled('end_date')) {
                    $startDate = date('Y-m-d 00:00:00', strtotime($request->start_date));
                    $endDate = date('Y-m-d 23:59:59', strtotime($request->end_date));
                    $query->whereBetween('po_wo.created_at', [$startDate, $endDate]);
                }

                $summary = $query->get();

                $fieldtechs = Fieldtech::whereIn('id', $summary->pluck('fieldtech_id'))->get()->keyBy('id');
                $vendors = Vendor::whereIn('id', $summary->pluck('vendor_id'))->get()->keyBy('id');
                $activities = Activity::whereIn('id', $summary->pluck('activity_id'))->get()->keyBy('id');

                $statusMap = [
                    1 => ['start' => 1310, 'end' => 1432],
                    4 => ['start' => 4310, 'end' => 4432],
                    5 => ['start' => 5420, 'end' => 5610],
                    6 => ['start' => 6320, 'end' => 6432],
                ];

                $woIds = $summary->flatMap(function ($row) {
                    return explode(',', $row->ids);
                })->unique()->values()->all();

                $actions = Action::whereIn('wo_id', $woIds)
                    ->whereIn('status_id', collect($statusMap)->flatMap(fn($s) => [$s['start'], $s['end']])->unique())
                    ->orderBy('created_at')
                    ->get()
                    ->groupBy('wo_id');

                $actions = $actions->mapWithKeys(function ($items, $key) {
                    return [strval($key) => $items];
                });

                $summary = $summary->map(function ($item) {
                    if (!isset($item->id) && isset($item->wo_id)) {
                        $item->id = $item->wo_id;
                    }
                    return $item;
                });

                $data = $summary->map(function ($row) use ($fieldtechs, $vendors, $activities, $actions, $statusMap) {
                    $fieldtech = $fieldtechs[$row->fieldtech_id] ?? null;
                    $vendor = $vendors[$row->vendor_id] ?? null;
                    $activity = $activities[$row->activity_id] ?? null;

                    $closedPercent = ($row->total_ticket > 0)
                        ? round(($row->closed_ticket / $row->total_ticket) * 100, 1)
                        : 0;

                    $ids = is_string($row->ids) ? array_map('strval', explode(',', $row->ids)) : [$row->id];

                    $woActions = collect();
                    foreach ($ids as $id) {
                        if ($actions->has($id)) {
                            $woActions = $woActions->merge($actions->get($id));
                        }
                    }

                    $totalDurationMinutes = 0;
                    $ticketsWithDuration = 0;

                    $actionsByWorkOrder = $woActions->groupBy('wo_id');

                    foreach ($actionsByWorkOrder as $workOrderId => $workOrderActions) {
                        foreach ($statusMap as $activityId => $statusRange) {
                            if ($row->activity_id == $activityId) {
                                $startAction = $workOrderActions->where('status_id', $statusRange['start'])->sortBy('created_at')->first();
                                $endAction = $workOrderActions->where('status_id', $statusRange['end'])->sortBy('created_at')->first();

                                if ($startAction && $endAction) {
                                    $durationForTicket = \Carbon\Carbon::parse($endAction->created_at)
                                        ->diffInMinutes(\Carbon\Carbon::parse($startAction->created_at));

                                    $totalDurationMinutes += $durationForTicket;
                                    $ticketsWithDuration++;
                                }
                                break;
                            }
                        }
                    }

                    $averageDurationMinutes = ($ticketsWithDuration > 0)
                        ? round($totalDurationMinutes / $ticketsWithDuration, 1)
                        : 0;

                    return [
                        'name' => $fieldtech->name ?? 'N/A',
                        'vendor' => $vendor->name ?? 'N/A',
                        'vendor_name' => $fieldtech->vendor_name ?? 'N/A',
                        'activity' => $activity->alias ?? 'N/A',
                        'total_ticket' => $row->total_ticket,
                        'closed_ticket' => $row->closed_ticket,
                        'closed_percent' => $closedPercent,
                        'duration_minutes' => $averageDurationMinutes,
                    ];
                })
                    ->sortBy(fn($item) => $item['vendor'] . '_' . $item['name'] . '_' . $item['activity'])
                    ->values()
                    ->all();

                return $data;
            }
        );

        return response()->json($data);
    }

    public function getWorkOrders($site_id, Request $request)
    {
        $query = WorkOrder::with([
            'fieldtech',
            'lastAction',
            'lastAction.status',
            'site',
            'activity',
            'client',
            'actions',
        ])
            ->where('site_id', $site_id);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = date('Y-m-d 00:00:00', strtotime($request->start_date));
            $endDate = date('Y-m-d 23:59:59', strtotime($request->end_date));
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        if ($request->filled('activity_id') && $request->activity_id !== 'all') {
            $query->where('activity_id', $request->activity_id);
        }

        $workorders = $query->orderBy('updated_at', 'asc')->get();

        $specialStatusIds = [
            1910,
            1912,
            1914,
            2910,
            2912,
            2914,
            3910,
            3912,
            3914,
            4910,
            4912,
            4914,
            5910,
            5912,
            5914,
            6910,
            6912,
            6914,
            7910,
            7912,
            7914
        ];

        $workorders = $workorders->sortBy(function ($wo) use ($specialStatusIds) {
            $statusId = optional($wo->lastAction)->status_id;
            return in_array($statusId, $specialStatusIds) ? 1 : 0;
        })->values();

        $statusMap = [
            1 => ['start' => 1310, 'end' => 1432],
            4 => ['start' => 4310, 'end' => 4432],
            5 => ['start' => 5420, 'end' => 5610],
            6 => ['start' => 6320, 'end' => 6432],
        ];

        $workorders->map(function ($wo) use ($statusMap) {
            $wo->duration_minutes = null;

            if (isset($statusMap[$wo->activity_id])) {
                $startStatus = $statusMap[$wo->activity_id]['start'];
                $endStatus = $statusMap[$wo->activity_id]['end'];

                $start = $wo->actions->firstWhere('status_id', $startStatus);
                $end = $wo->actions->firstWhere('status_id', $endStatus);

                if ($start && $end) {
                    $wo->duration_minutes = \Carbon\Carbon::parse($end->created_at)
                        ->diffInMinutes(\Carbon\Carbon::parse($start->created_at));
                }
            }

            return $wo;
        });

        return response()->json($workorders);
    }

    public function getActivities()
    {
        $activities = Activity::select('id', 'name')->orderBy('name')->get();
        return response()->json($activities);
    }

    public function exportExcel(Request $request)
    {
        // $title = [["SUMMARY MAP", 'h2'], ["Asianet", 'h3']];

        if ($request->filled('activity_id') && $request->activity_id !== 'all') {
            $activity = Activity::find($request->activity_id);
            $title[] = ["Activity: " . $activity->name, 'h4'];
        }

        if ($request->filled('vendor_id') && $request->vendor_id !== 'all') {
            $vendor = Vendor::find($request->vendor_id);
            $title[] = ["Vendor: " . $vendor->name, 'h4'];
        }

        if ($request->filled('site_id') && $request->site_id !== 'all') {
            $site = Site::find($request->site_id);
            $title[] = ["Site: " . $site->name, 'h4'];
        }

        if ($request->filled('fieldtech_id') && $request->fieldtech_id !== 'all') {
            $fieldtech = Fieldtech::find($request->fieldtech_id);
            $title[] = ["Fieldtech: " . $fieldtech->name, 'h4'];
        }

        if ($request->filled("vendor_name")) {
            $vendor = Fieldtech::where('vendor_name', $request->filled("vendor_name"))->first();
            $title[] = ["Vendor: " . $vendor->name, 'h4'];
        }

        $response = $this->summary($request);
        $data = $response instanceof \Illuminate\Http\JsonResponse ? $response->getData(true) : $response;

        $dataModels = collect($data)->map(function ($item) {
            return new ExportSummaryRow($item);
        });

        $eloquentData = new EloquentCollection($dataModels);

        $columns = [
            [
                'text' => 'TEAM',
                'dataIndex' => 'name',
                'width' => 150,
                'align' => 'center'
            ],
            ['text' => 'VENDOR', 'dataIndex' => 'vendor_name', 'width' => 150, 'align' => 'center'],
            ['text' => 'AREA', 'dataIndex' => 'vendor', 'width' => 200],
            ['text' => 'ACTIVITY TICKET', 'dataIndex' => 'activity', 'width' => 200],
            [
                'text' => 'TOTAL TICKET',
                'dataIndex' => 'total_ticket',
                'width' => 150,
            ],
            [
                'text' => 'CLOSE TICKET',
                'dataIndex' => 'closed_ticket',
                'width' => 150,
            ],
            [
                'text' => 'COMPLETE RATE (%)',
                'dataIndex' => 'closed_percent',
                'width' => 150,
            ],
            [
                'text' => 'DURATION (MIN)',
                'dataIndex' => 'duration_minutes',
                'width' => 150,
            ],
        ];

        $params = array(
            // 'title' => $title,
            'columns' => $columns,
            'data' => $eloquentData,
            'filename' => 'Summary Map' . '-' . date('YmdHi'),
            'footer' => [config('app.name') . ' (' . date('d F Y H:i:s') . ')'],
        );

        return ExportExcel::export($params);
    }

    public function exportRawExcel(Request $request)
    {

        $cacheKey = 'raw_export_data_' . md5(serialize($request->all()));

        $processedData = Cache::remember(
            $cacheKey,
            60 * 24,
            function () use ($request) {

                $query = WorkOrder::query()->with(['actions', 'actions.status']);

                if ($request->filled('vendor_id') && $request->vendor_id !== 'all') {
                    $query->where('vendor_id', $request->vendor_id);
                }

                if ($request->filled('site_id') && $request->site_id !== 'all') {
                    $query->where('site_id', $request->site_id);
                }

                if ($request->filled('fieldtech_id') && $request->fieldtech_id !== 'all') {
                    $query->where('fieldtech_id', $request->fieldtech_id);
                }

                if ($request->filled('activity_id') && $request->activity_id !== 'all') {
                    $query->where('activity_id', $request->activity_id);
                }

                if ($request->filled('vendor_name')) {
                    $vendorName = $request->vendor_name;
                    $query->whereHas('fieldtech', function ($q) use ($vendorName) {
                        $q->where('vendor_name', 'LIKE', "%{$vendorName}%");
                    });
                }

                if ($request->filled('start_date') && $request->filled('end_date')) {
                    $startDate = date('Y-m-d 00:00:00', strtotime($request->start_date));
                    $endDate = date('Y-m-d 23:59:59', strtotime($request->end_date));
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }

                $workOrders = $query->get();
                $woIds = $workOrders->pluck('id')->toArray();

                $fieldtechs = Fieldtech::whereIn('id', $workOrders->pluck('fieldtech_id'))->get()->keyBy('id');
                $vendors = Vendor::whereIn('id', $workOrders->pluck('vendor_id'))->get()->keyBy('id');
                $activities = Activity::whereIn('id', $workOrders->pluck('activity_id'))->get()->keyBy('id');
                $sites = Site::whereIn('id', $workOrders->pluck('site_id'))->get()->keyBy('id');
                $clients = Client::whereIn('id', $workOrders->pluck('client_id'))->get()->keyBy('id');
                $services = Service::whereIn('id', $workOrders->pluck('service_id'))->get()->keyBy('id');
                $slots = Slot::whereIn('id', $workOrders->pluck('slot_id'))->get()->keyBy('id');
                $last_action = Action::whereIn('id', $workOrders->pluck('last_action'))->get()->keyBy('id');
                $status_ids = $last_action->pluck('status_id')->toArray();
                $status = Status::whereIn('id', $status_ids)->get()->keyBy('id');

                $statusMap = [
                    1 => ['start' => 1310, 'end' => 1432], // PREPARATION & ADM
                    4 => ['start' => 4310, 'end' => 4432],
                    5 => ['start' => 5420, 'end' => 5610], // DE-ACTIVATION & POST ACTIVATION
                    6 => ['start' => 6320, 'end' => 6432],
                ];

                $actions = Action::whereIn('wo_id', $woIds)
                    ->whereIn('status_id', collect($statusMap)->flatMap(fn($s) => [$s['start'], $s['end']])->unique())
                    ->orderBy('created_at')
                    ->get()
                    ->groupBy('wo_id');

                $ontSerialNumberMapping = [
                    1310 => 281011,
                    2310 => 281168,
                    3310 => 281169,
                    4310 => 281273,
                    7310 => 281462
                ];
                $totalStbDetailId = 281827;
                $alamatInstalasiDetailId = 281880;
                $snOntDetailId = 281805;

                $allActionIds = [];
                $ontActionIds = [];
                $statusToActionMap = [];

                foreach ($workOrders as $wo) {
                    $totalStbAction = $wo->actions->where('status_id', 1110)->first();
                    if ($totalStbAction) {
                        $allActionIds[] = $totalStbAction->id;
                    }

                    $alamatInstalasiAction = $wo->actions->where('status_id', 1330)->first();
                    if ($alamatInstalasiAction) {
                        $allActionIds[] = $alamatInstalasiAction->id;
                    }

                    $snOntAction = $wo->actions->where('status_id', 1420)->first();
                    if ($snOntAction) {
                        $allActionIds[] = $snOntAction->id;
                    }

                    foreach ($ontSerialNumberMapping as $statusId => $detailId) {
                        $ontAction = $wo->actions->where('status_id', $statusId)->first();
                        if ($ontAction) {
                            $allActionIds[] = $ontAction->id;
                            $ontActionIds[] = $ontAction->id;
                            $statusToActionMap[$ontAction->id] = $statusId;
                        }
                    }
                }
                // Gabungkan semua action IDs
                $allDetailIds = array_merge(
                    [$totalStbDetailId, $alamatInstalasiDetailId, $snOntDetailId],
                    array_values($ontSerialNumberMapping)
                );

                $actionDetails = DB::table('po_wo_action_detail')
                    ->whereIn('action_id', $allActionIds)
                    ->whereIn('detail_id', $allDetailIds)
                    ->get();

                $actionDetailValues = [];
                foreach ($actionDetails as $detail) {
                    $actionDetailValues[$detail->action_id][$detail->detail_id] = $detail->value;
                }

                $data = [];

                foreach ($workOrders as $wo) {

                    $fieldtech = $fieldtechs[$wo->fieldtech_id] ?? null;
                    $vendor = $vendors[$wo->vendor_id] ?? null;
                    $activity = $activities[$wo->activity_id] ?? null;
                    $site = $sites[$wo->site_id] ?? null;
                    $client = $clients[$wo->client_id] ?? null;
                    $service = $services[$wo->service_id] ?? null;
                    $slot = $slots[$wo->slot_id] ?? null;
                    $action = $last_action[$wo->last_action] ?? null;

                    // DETAIL
                    $totalStbAction = $wo->actions->where('status_id', 1110)->first();
                    $alamatInstalasiAction = $wo->actions->where('status_id', 1330)->first();
                    $snOntAction = $wo->actions->where('status_id', 1420)->first();

                    $totalStbValue = isset($totalStbAction) && isset($actionDetailValues[$totalStbAction->id][$totalStbDetailId])
                        ? $actionDetailValues[$totalStbAction->id][$totalStbDetailId]
                        : null;

                    $alamatInstalasiValue = isset($alamatInstalasiAction) && isset($actionDetailValues[$alamatInstalasiAction->id][$alamatInstalasiDetailId])
                        ? $actionDetailValues[$alamatInstalasiAction->id][$alamatInstalasiDetailId]
                        : null;

                    $snOntValue = isset($snOntAction) && isset($actionDetailValues[$snOntAction->id][$snOntDetailId])
                        ? $actionDetailValues[$snOntAction->id][$snOntDetailId]
                        : null;

                    $ontSerialNumbers = [];
                    foreach ($ontSerialNumberMapping as $statusId => $detailId) {
                        $ontAction = $wo->actions->where('status_id', $statusId)->first();
                        if ($ontAction && isset($actionDetailValues[$ontAction->id][$detailId])) {
                            $serialValue = $actionDetailValues[$ontAction->id][$detailId];
                            if (!empty($serialValue)) {
                                $ontSerialNumbers[] = $serialValue;
                            }
                        }
                    }

                    $combinedOntSerialNumbers = !empty($ontSerialNumbers) ? implode(', ', $ontSerialNumbers) : null;

                    $woActions = $actions->get(strval($wo->id), collect());

                    $inprogressTime = null;
                    $prepTime = null;
                    $admTime = null;
                    $installDurationFormatted = '';
                    $deactivationTime = null;
                    $postActivationTime = null;
                    $terminationDurationFormatted = '';

                    if (in_array($wo->activity_id, [1, 4])) {
                        $statusStart = $statusMap[$wo->activity_id]['start'];
                        $statusEnd = $statusMap[$wo->activity_id]['end'];

                        $prepAction = $woActions->where('status_id', $statusStart)->sortBy('created_at')->first();

                        $admAction = $woActions->where('status_id', $statusEnd)->sortBy('created_at')->first();

                        if ($prepAction) {
                            $prepTime = \Carbon\Carbon::parse($prepAction->created_at)->format('Y-m-d H:i:s');
                        }

                        if ($admAction) {
                            $admTime = \Carbon\Carbon::parse($admAction->created_at)->format('Y-m-d H:i:s');
                        }

                        if (!empty($prepTime) && !empty($admTime)) {
                            $installDuration = \Carbon\Carbon::parse($admTime)
                                ->diffInMinutes(\Carbon\Carbon::parse($prepTime));
                            $installDurationFormatted = $installDuration . 'm';
                        }
                    } elseif ($wo->activity_id == 5) {
                        $deactivateAction = $woActions->where('status_id', $statusMap[5]['start'])->sortBy('created_at')->first();
                        $postActivateAction = $woActions->where('status_id', $statusMap[5]['end'])->sortBy('created_at')->first();

                        if ($deactivateAction) {
                            $deactivationTime = \Carbon\Carbon::parse($deactivateAction->created_at)->format('Y-m-d H:i:s');
                        }

                        if ($postActivateAction) {
                            $postActivationTime = \Carbon\Carbon::parse($postActivateAction->created_at)->format('Y-m-d H:i:s');
                        }

                        if ($deactivationTime && $postActivationTime) {
                            $terminationDuration = \Carbon\Carbon::parse($postActivationTime)
                                ->diffInMinutes(\Carbon\Carbon::parse($deactivationTime));
                            $terminationDurationFormatted = isset($terminationDuration) ? $terminationDuration . 'm' : null;
                        }
                    } elseif ($wo->activity_id == 6) {
                        $inprogressAction = $woActions->where('status_id', $statusMap[6]['start'])->sortBy('created_at')->first();
                        $admAction = $woActions->where('status_id', $statusMap[6]['end'])->sortBy('created_at')->first();

                        if ($inprogressAction) {
                            $inprogressTime = \Carbon\Carbon::parse($inprogressAction->created_at)->format('Y-m-d H:i:s');
                        }

                        if ($admAction) {
                            $admTime = \Carbon\Carbon::parse($admAction->created_at)->format('Y-m-d H:i:s');
                        }

                        if ($inprogressTime && $admTime) {
                            $installDuration = \Carbon\Carbon::parse($admTime)
                                ->diffInMinutes(\Carbon\Carbon::parse($inprogressTime));
                            $installDurationFormatted = isset($installDuration) ? $installDuration . 'm' : null;
                        }
                    }

                    $data[] = [
                        'id' => $wo->id,
                        'ticket_id' => $wo->no_wo,
                        'service' => $service->name ?? 'N/A',
                        'activity' => $activity->alias ?? 'N/A',
                        'client' => $client->name ?? 'N/A',
                        'site_name' => $site->name ?? 'N/A',
                        'site_address' => $site->address ?? 'N/A',
                        'site_phone' => $site->pic_phone ?? 'N/A',
                        'area' => $vendor->name ?? 'N/A',
                        'duration' => $wo->start_date ? \Carbon\Carbon::parse($wo->start_date)->diffInDays(now()) : 'N/A',
                        'booking_date' => $wo->start_date ?? 'N/A',
                        'booking_slot' => $slot->name ?? 'N/A',
                        'created_by' => $wo->createdBy->name ?? 'N/A',
                        'created_date' => $wo->created_at ?? 'N/A',
                        'last_action_status' => isset($last_action[$wo->last_action]) && isset($status[$last_action[$wo->last_action]->status_id])
                            ? $status[$last_action[$wo->last_action]->status_id]->name
                            : 'N/A',
                        'last_action_date' => $action ? $action->created_at->format('Y-m-d') : 'N/A',
                        'total_stb_value' => $totalStbValue,
                        'alamat_instalasi_value' => $alamatInstalasiValue,
                        'sn_ont_act_value' => $snOntValue,
                        'ont_serial_numbers' => $combinedOntSerialNumbers ?? '',
                        'description' => $wo->description ?? 'N/A',
                        'is_hold' => $wo->is_hold ? 'HOLD' : '-',
                        'fieldtech_name' => $fieldtech->name ?? 'N/A',
                        'vendor_name' =>  $fieldtech->vendor_name ?? 'N/A',
                        'datetime_inprogress' => $inprogressTime ?? '',
                        'datetime_preparation' => $prepTime ?? '',
                        'datetime_adm' => $admTime ?? '',
                        'duration_installation' => $installDurationFormatted,
                        'datetime_deactivation' => $deactivationTime ?? '',
                        'datetime_post_activation' => $postActivationTime ?? '',
                        'duration_termination' => $terminationDurationFormatted,
                    ];
                }

                $data = collect($data)
                    ->sortBy(function ($item) {
                        return $item['vendor_name'] . '_' . $item['fieldtech_name'] . '_' . $item['activity'];
                    })
                    ->values()
                    ->all();

                return $data;
            }
        );


        $dataModels = collect($processedData)->map(function ($item) {
            return new ExportRawDataSummary($item);
        });

        $eloquentData = new EloquentCollection($dataModels);

        $columns = [
            ['text' => 'ID', 'dataIndex' => 'id', 'width' => 150, 'align' => 'center'],
            ['text' => 'TICKET ID', 'dataIndex' => 'ticket_id', 'width' => 200, 'align' => 'center'],
            ['text' => 'SERVICE', 'dataIndex' => 'service', 'width' => 150, 'align' => 'center'],
            ['text' => 'ACTIVITY', 'dataIndex' => 'activity', 'width' => 150, 'align' => 'center'],
            ['text' => 'CLIENT', 'dataIndex' => 'client', 'width' => 150],
            ['text' => 'SITE', 'dataIndex' => 'site_name', 'width' => 250],
            ['text' => 'SITE ADDRESS', 'dataIndex' => 'site_address', 'width' => 300],
            ['text' => 'SITE PHONE', 'dataIndex' => 'site_phone', 'width' => 150, 'align' => 'center'],
            ['text' => 'AREA', 'dataIndex' => 'area', 'width' => 150],
            ['text' => 'DURATION (DAYS)', 'dataIndex' => 'duration', 'width' => 100, 'align' => 'center'],
            ['text' => 'BOOKING DATE', 'dataIndex' => 'booking_date', 'width' => 150, 'align' => 'center'],
            ['text' => 'BOOKING SLOT', 'dataIndex' => 'booking_slot', 'width' => 200, 'align' => 'center'],
            ['text' => 'CREATED BY', 'dataIndex' => 'created_by', 'width' => 150, 'align' => 'center'],
            ['text' => 'CREATED DATE', 'dataIndex' => 'created_date', 'width' => 150, 'align' => 'center'],
            ['text' => 'LAST ACTION STATUS', 'dataIndex' => 'last_action_status', 'width' => 150, 'align' => 'center'],
            ['text' => 'LAST ACTION DATE', 'dataIndex' => 'last_action_date', 'width' => 150, 'align' => 'center'],
            ['text' => 'TOTAL STB', 'dataIndex' => 'total_stb_value', 'width' => 150, 'align' => 'center'],
            ['text' => 'ONT SERIAL NUMBER', 'dataIndex' => 'ont_serial_numbers', 'width' => 150, 'align' => 'center'],
            ['text' => 'DESCRIPTION', 'dataIndex' => 'description', 'width' => 500],
            ['text' => 'ALAMAT INSTALASI', 'dataIndex' => 'alamat_instalasi_value', 'width' => 340, 'align' => 'center'],
            ['text' => 'SN ACT', 'dataIndex' => 'sn_ont_act_value', 'width' => 150, 'align' => 'center'],
            ['text' => 'HOLD', 'dataIndex' => 'is_hold', 'width' => 80, 'align' => 'center'],
            ['text' => 'TEAM', 'dataIndex' => 'fieldtech_name', 'width' => 150, 'align' => 'center'],
            ['text' => 'VENDOR', 'dataIndex' => 'vendor_name', 'width' => 150, 'align' => 'center'],
            ['text' => 'DATE TIME (IN PROGRESS)', 'dataIndex' => 'datetime_inprogress', 'width' => 150],
            ['text' => 'DATE TIME (PREPARATION)', 'dataIndex' => 'datetime_preparation', 'width' => 150],
            ['text' => 'DATE TIME (ADM)', 'dataIndex' => 'datetime_adm', 'width' => 150],
            ['text' => 'DURATION COMPLETION (INSTALLATION / RELOCATE / MOVE)', 'dataIndex' => 'duration_installation', 'width' => 150],
            ['text' => 'DATE TIME (DE-ACTIVATION)', 'dataIndex' => 'datetime_deactivation', 'width' => 150],
            ['text' => 'DATE TIME (POST ACTIVATION)', 'dataIndex' => 'datetime_post_activation', 'width' => 150],
            ['text' => 'DURATION COMPLETION (TERMINATION)', 'dataIndex' => 'duration_termination', 'width' => 150],
        ];

        $params = array(
            'columns' => $columns,
            'data' => $eloquentData,
            'filename' => 'Raw Data Export' . '-' . date('YmdHi'),
            'footer' => [config('app.name') . ' (' . date('d F Y H:i:s') . ')'],
        );

        return ExportExcel::export($params);
    }
}
