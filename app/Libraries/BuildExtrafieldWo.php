<?php

namespace App\Libraries;

use App\Models\WorkOrders\ActionDetail;
use App\Models\WorkOrders\WorkOrder;
use Hamcrest\Type\IsObject;

class BuildExtrafieldWo
{
    public static function build($id = null)
    {
        $extraWo = new BuildExtrafieldWo();
        if ($id) $extraWo->exec($id);
        else {
            $data = WorkOrder::all();
            foreach ($data as $wo) {
                $extraWo->exec($wo);
            }
        }
    }

    public function exec($wo)
    {

        if (!is_object($wo)) {
            $wo = WorkOrder::find($wo);
        }

        $this->buildContact($wo);
    }

    private function buildContact($wo)
    {
        if ($action = $wo->actions()->where('status_id', 1330)->orderBy('created_at', 'desc')->first()) {
            $details = ActionDetail::where('action_id', $action->id)->whereIn('detail_id', [281880, 281892, 281893, 281894, 281895])->get();
            foreach ($details as $detail) {
                if ($detail->detail_id == '281880') $this->pushExtrafield($wo, 'contact_address', $detail->value);
                if ($detail->detail_id == '281892') $this->pushExtrafield($wo, 'contact_rt', $detail->value);
                if ($detail->detail_id == '281893') $this->pushExtrafield($wo, 'contact_rw', $detail->value);
                if ($detail->detail_id == '281894') $this->pushExtrafield($wo, 'contact_no', $detail->value);
                if ($detail->detail_id == '281895') $this->pushExtrafield($wo, 'contact_phone', $detail->value);
            }
        }
        if ($action = $wo->actions()->where('status_id', 1110)->orderBy('created_at', 'desc')->first()) {
            $details = ActionDetail::where('action_id', $action->id)->whereIn('detail_id', [281827])->get();
            foreach ($details as $detail) {
                if ($detail->detail_id == '281827') $this->pushExtrafield($wo, 'total_stb', $detail->value);
            }
        }
        if ($action = $wo->actions()->where('status_id', 1420)->orderBy('created_at', 'desc')->first()) {
            $details = ActionDetail::where('action_id', $action->id)->whereIn('detail_id', [281805])->get();
            foreach ($details as $detail) {
                if ($detail->detail_id == '281805') $this->pushExtrafield($wo, 'sn_ont_activation', $detail->value);
            }
        }
        if ($action = $wo->actions()->where('status_id', 1430)->orderBy('created_at', 'desc')->first()) {
            $details = ActionDetail::where('action_id', $action->id)->whereIn('detail_id', [281891])->get();
            foreach ($details as $detail) {
                if ($detail->detail_id == '281891') $this->pushExtrafield($wo, 'sn_ont_testing', $detail->value);
            }
        }
        if ($action = $wo->actions()->where('status_id', 1410)->orderBy('created_at', 'desc')->first()) {
            $details = ActionDetail::where('action_id', $action->id)->whereIn('detail_id', [281890])->get();
            foreach ($details as $detail) {
                if ($detail->detail_id == '281890') $this->pushExtrafield($wo, 'input_kabel_kode', $detail->value);
            }
        }

        if ($action = $wo->actions()->whereIn('status_id', [3610, 1610, 2610, 4610, 5610, 6610, 8610])->orderBy('created_at', 'desc')->first()) {
            $details = ActionDetail::where('action_id', $action->id)
                ->whereIn('detail_id', [281783, 281785, 281787, 281789, 281791, 281793, 281908])
                ->get();
            foreach ($details as $detail) {
                $techName = $detail->technicianVendor ? $detail->technicianVendor->name : $detail->value;
                $this->pushExtrafield($wo, 'technician_name', $techName);
            }
        }

        if ($action = $wo->actions()->whereIn('status_id', [1310, 2310, 3310, 4310, 7310])->orderBy('created_at', 'desc')->first()) {
            $details = ActionDetail::where('action_id', $action->id)
                ->whereIn('detail_id', [281011, 281168, 281169, 281273, 281462])
                ->get();
            foreach ($details as $detail) {
                $this->pushExtrafield($wo, 'ont_serial', $detail->value);
            }
        }

        if ($action = $wo->actions()->whereIn('status_id', [1330, 2330, 3330, 4330, 5330, 6330, 7330])->orderBy('created_at', 'desc')->first()) {
            $arrivedDate = date('Y-m-d', strtotime($action->created_at));
            $arrivedTime = date('H:i:s', strtotime($action->created_at));

            $this->pushExtrafield($wo, 'arrived_date', $arrivedDate);
            $this->pushExtrafield($wo, 'arrived_time', $arrivedTime);
        }
    }

    private function pushExtrafield($wo, $index, $value)
    {
        $extrafield = (array) $wo->extrafield ?: [];
        $extrafield[$index] = $value;
        $wo->update(['extrafield' => $extrafield]);
    }
}
