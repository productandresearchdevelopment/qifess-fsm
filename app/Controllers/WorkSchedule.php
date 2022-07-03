<?php

namespace App\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Fieldteches\Fieldtech as Mod;
use App\Models\Vendors\Vendor;
use App\Models\WorkOrders\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class WorkSchedule extends Controller
{
    public function index(Request $request){
        $user = $request->user();
        $date = $request->input('date');
        $date1 = $date ? date('Y-m-01', strtotime("$date 00:00:00")) : date('Y-m-01');
        $date2 = date('Y-m-t', strtotime("$date1 00:00:00"));

        $params = [
            'date1' => $date1,
            'date2' => $date2,
            'user' => $user,
            'vendors' => ($user->vendor_id) ? [] : Vendor::all(),
        ];
        return view('work_schedule.main', $params);
    }

    public function data(Request $request){
        $user = $request->user();

        $result = [];
        $date1 = $request->input('date1');
        $date2 = $request->input('date2');

        $data = Mod::orderBy('name');

        if($filter = $request->input('filter-vendor')) $data->where('vendor_id', $filter);
        if($user->vendor_id) $data->where('vendor_id', $user->vendor_id);


        foreach ($data->get() as $row) {
            $wo = WorkOrder::select('start_date', DB::raw('count(*) as count'))
                ->where('fieldtech_id', $row->id)
                ->whereBetween('start_date', [$date1, $date2])
                ->groupBy('start_date')
                ->get();

            $result[] = [
                'id' => $row->id,
                'name' => $row->name,
                'vendor_id' => $row->vendor_id,
                'vendor_name' => $row->vendor ? $row->vendor->name : null,
                'data' => $wo
            ];
        }
        return $result;
    }

}
