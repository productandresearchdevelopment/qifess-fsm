<?php

namespace App\Controllers\Dashboards;

use App\Models\Fieldteches\Fieldtech;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\WorkOrders\WorkOrder AS Wo;
use App\Models\Sites\Site;
use App\Models\Clients\Client;
use App\Models\Vendors\Vendor;

class Dashboard extends Controller
{
    public function index(Request $request){
        $user   = $request->user();
        $view   = 'dashboards.main';

        // TOTAL TICKET -------------------------------------------------------------------------------------------------
        $sql = "SELECT A.*, SUM(IF(B.id, 1, 0)) count FROM po_wo_m_activity A LEFT JOIN po_wo B ON A.id = B.activity_id GROUP BY A.id";
        $allTicket = DB::select(DB::raw($sql));
        $totalAllTicket = 0;
        foreach ($allTicket AS $r){ $totalAllTicket += $r->count; }

        // ON GOING TICKET -------------------------------------------------------------------------------------------------
        $sql = "SELECT A.*, SUM(IF(B.id, 1, 0)) count FROM po_wo_m_activity A LEFT JOIN po_wo B ON (A.id = B.activity_id AND B.close_date IS NULL) GROUP BY A.id";
        $ongoingTicket = DB::select(DB::raw($sql));
        $totalOngoingTicket = 0;
        foreach($ongoingTicket AS $r){ $totalOngoingTicket += $r->count; }


        // QUERY SUMMARY PART ------------------------------------------------------------------------------------------
        $sql = "SELECT SUM(IF(B.activity_id = 1, 1, 0)) install,
                       SUM(IF(B.activity_id = 5, 1, 0)) remove
                FROM (SELECT serial, MAX(wo_id) wo FROM po_wo_part WHERE type = 'SPAREPART' AND serial IS NOT NULL AND deleted_at IS NULL GROUP BY serial) A
                     LEFT JOIN po_wo B ON A.wo = B.id";
        $part = DB::select(DB::raw($sql));
        if(count($part)) $part = $part[0];
        else $part = (object) ['install' => 0, 'remove' => 0];

        // QUERY SUMMARY VENDOR ----------------------------------------------------------------------------------------
        $sql = "SELECT A.id, A.`name`, A.alias,
                       SUM(IF(B.id, 1, 0)) total,
                       SUM(IF(expire_date > IF(close_date, close_date, DATE(NOW())), 1, 0)) ontarget
                FROM po_m_vendor A
                     LEFT JOIN po_wo B ON A.id = B.vendor_id
                WHERE (A.deleted_at IS NULL AND B.deleted_at IS NULL)
                GROUP BY A.id";
        $vendors = DB::select(DB::raw($sql));

        $params = [
            'user' => $user,
            'allTicket' => $allTicket,
            'totalAllTicket' => $totalAllTicket,
            'ongoingTicket' => $ongoingTicket,
            'totalOngoingTicket' => $totalOngoingTicket,
            'totalSite' => Site::count(),
            'totalSiteActive' => Site::where('is_active', 1)->count(),
            'totalClient' => Client::all()->count(),
            'totalVendor' => Vendor::all()->count(),
            'totalFieldtech' => Fieldtech::all()->count(),
            'wo' => WO::orderBy('updated_at', 'DESC')->limit(5)->get(),
            'part' => $part,
            'vendors' => $vendors,
        ];
        return view($view, $params);
    }
}
