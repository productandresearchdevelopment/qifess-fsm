<?php

namespace App\Jobs;

use App\Mail\AlertEmail;
use App\Mail\UpdateWoMail;
use App\Models\PQB\Alerts\Alert;
use App\Models\WorkOrders\WorkOrder;
use App\SystemModels\Auth\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Ixudra\Curl\Facades\Curl;

class NotifJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $wo;

    public function __construct($wo){
        $this->wo = WorkOrder::find($wo);
    }

    public function handle() {
        if($wo = $this->wo) {
            if($receiver = $this->getReceivers($wo)) {
                Log::debug(json_encode($receiver));
                if ($receiver->emails && count($receiver->emails)) {
                    Mail::to($receiver->emails)->send(new UpdateWoMail($wo, $wo->lastAction));
                }
                if ($receiver->tokens && count($receiver->tokens)) {
                    $data = [
                        'registration_ids' => $receiver->tokens,
                        'data' => [
                            'title' => 'Title Nya',
                            'message' => 'Message'
                        ]
                    ];
                    Curl::to('https://fcm.googleapis.com/fcm/send')
                        ->withData($data)
                        ->withHeader('Authorization: key=AAAAtwC-0ec:APA91bHxUN6adM1fT6GvD-NXXd95kiX8qpiOlw5Clc9ks0nTiQfbyGjef-HAZd7ds9xBmzLtNjl5dFbdtZ3dppHNxzMUNtrlrds4vJ-teQZIPRSA_wU2J8RKBW3gRZUMuONRgkIPeD0o')
                        ->asJson()
                        ->post();
                }
            }
        }
    }

    private function getReceivers($wo){
        $result = (object) ['emails' => [], 'tokens' => []];
        $sendEmailRoles = $wo->lastAction->status->send_email_roles;
        $users = User::where(function ($query) use ($wo, $sendEmailRoles) {
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
        })->get();

        foreach($users AS $user){
            if($user->email) $result->emails[] = $user->email;
            if($user->token_fcm) $result->tokens[] = $user->token_fcm;
        }

        return $result;
    }
}
