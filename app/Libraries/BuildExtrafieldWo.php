<?php
namespace App\Libraries;

use App\Models\WorkOrders\ActionDetail;
use App\Models\WorkOrders\WorkOrder;

class BuildExtrafieldWo {
    public static function build($id=null){
        $extraWo = new BuildExtrafieldWo();
        if($id) $extraWo->exec($id);
        else{
            $data = WorkOrder::all();
            foreach($data as $wo){
                $extraWo->exec($wo);
            }
        }
    }

    public function exec($wo){
        if(is_string($wo)) $wo = WorkOrder::find($wo);
        if($wo){
            $this->buildContact($wo);
        }
    }

    private function buildContact($wo){
        if($action = $wo->actions()->where('status_id', 1330)->orderBy('created_at', 'desc')->first()) {
            $details = ActionDetail::where('action_id', $action->id)->whereIn('detail_id', [281880, 281892, 281893, 281894, 281895])->get();
            foreach ($details as $detail) {
                if ($detail->detail_id == '281880') $this->pushExtrafield($wo, 'contact_address', $detail->value);
                if ($detail->detail_id == '281892') $this->pushExtrafield($wo, 'contact_rt', $detail->value);
                if ($detail->detail_id == '281893') $this->pushExtrafield($wo, 'contact_rw', $detail->value);
                if ($detail->detail_id == '281894') $this->pushExtrafield($wo, 'contact_no', $detail->value);
                if ($detail->detail_id == '281895') $this->pushExtrafield($wo, 'contact_phone', $detail->value);
            }
        }
    }

    private function pushExtrafield($wo, $index, $value) {
        $extrafield = (array) $wo->extrafield ?: [];
        $exsist = false;
        foreach ($extrafield AS $key => $value) {
            if($key == $index){
                $extra[$key] = $value;
                $exsist = true;
                break;
            }
        }
        if(!$exsist) $extrafield[$index] = $value;
        $wo->update(['extrafield' => $extrafield]);
    }
}

