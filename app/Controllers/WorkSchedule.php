<?php

namespace App\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Fieldteches\Fieldtech as Mod;
use App\Models\Vendors\Vendor;
use App\Models\WorkOrders\Masters\Activity;
use App\Models\WorkOrders\Masters\Slot;
use Illuminate\Http\Request;


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
            'activities' => Activity::all(),
            'slots' => Slot::all(),
        ];
        return view('work_schedule.main', $params);
    }

    public function data(Request $request){
        $user = $request->user();

        $date1 = $request->input('date1');
        $date2 = $request->input('date2');

        $data = Mod::with([
            'workorders' => function($query) use ($date1, $date2){
                $query->whereBetween('start_date', [$date1, $date2]);
            }
        ])->orderBy('name');

        if($filter = $request->input('filter-vendor')) $data->where('vendor_id', $filter);
        if($user->vendor_id) $data->where('vendor_id', $user->vendor_id);

        return $data->get();
    }

}
