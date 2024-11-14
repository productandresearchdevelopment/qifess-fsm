<?php
use App\Http\Controllers\Main;
use App\Http\Controllers\Auth\LoginController;
use App\SystemModels\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

Main::deployRoutes();
LoginController::routeMobile();

Route::get('/', function (Request $request){
    return 'Server Running...';
});

Route::get('/file/{id?}', '\Main@fileUpload');

Route::post('/mobile/login', function (Request $request){
    $username = $request->username;
    $password = $request->password;
    $user = User::where(['username' => $username])->first();
    if($user){
        if(Hash::check($password, $user->password)){
            Auth::guard()->login($user);
            $user->update(['token_fcm' => $request->firebase_token]);
            $data = [
                'username' => $user->username,
                'name' => $user->name,
                'team' => $user->fieldtech ? $user->fieldtech->name : '',
            ];
            return ['success' => true, 'message' => 'success', 'data' => $data];
        }
        return ['success' => false, 'message'=> 'Your Password Is Wrong'];
    }
    return ['success' => false, 'message'=> 'User Notfound'];
});

Route::group(['middleware' => ['auth.api'] ], function(){

    Route::group(['prefix' => 'public'], function (){

        Route::post('/getToken', function (Request $request){
            $username = $request->input('username');
            $password = $request->input('password');

            if($username && $password) {
                if($user = User::where('username', $username)->whereIn('role_id', [10, 20])->first()) {
                    if(Hash::check($password, $user->password)) {
                        if($user->token_api && ($user->token_api_expired_at > date('Y-m-d H:i:s'))) {
                            $token = $user->token_api;
                        }
                        else $token = \Illuminate\Support\Str::uuid();
                        $expired = 120;
                        $expiredAt = date('Y-m-d H:i:s', strtotime("+$expired minutes"));
                        $user->update([
                            'token_api' => $token,
                            'token_api_expired_at' => $expiredAt
                        ]);
                        return [
                            'success' => true,
                            'message' => 'Success...',
                            'name' => $user->name,
                            'username' => $username,
                            'token' => $token,
                            'expired_token' => $expired,
                            'expired_token_at' => $expiredAt
                        ];
                    }
                    return response()->json(['success' => false, 'message' => 'Invalid Password!']);
                }
                return response()->json(['success' => false, 'message' => 'Invalid Username']);
            }
            return response()->json(['success' => false, 'message' => 'Invalid Username Or Password!']);
        });

        Route::group(['middleware' => ['auth.api.role'] ], function(){
            Route::get('/test', function (Request $request){
                return $request->user();
            });
            Route::get('/area/data', 'Vendors\Vendor@data');
            Route::get('/team/data', 'Fieldtechs\Fieldtech@data');
            Route::get('/service/data', 'Services\Service@data');
            Route::get('/client/data', 'Clients\Client@data');
            Route::get('/site/data', 'Sites\Site@data');
            Route::get('/booking/data', 'WorkSchedule@data');
            Route::get('/slot/data', function (Request $request){
                return \App\Models\WorkOrders\Masters\Slot::all();
            });
            Route::get('/activity/data', function (Request $request){
                $activities = \App\Models\WorkOrders\Masters\Activity::all();
                foreach ($activities AS $activity){
                    $status = \App\Models\WorkOrders\Masters\Status::where('activities', 'LIKE', "%$activity->id%")
                        ->where('type', 0)
                        ->first();

                    if($status) $activity->status = ['id' => $status->id, 'name' => $status->name];
                    else $activity->status = null;
                }
                return $activities;
            });
            Route::get('/wo/data', 'WorkOrders\WorkOrder@data');
            Route::get('/wo/data/archive', 'WorkOrders\WorkOrder@dataArchive');
            Route::get('/wo/check/status/{id?}', function (Request $request, $id=null){
                if($wo = \App\Models\WorkOrders\WorkOrder::find($id)){
                    $status = $wo->lastAction ? $wo->lastAction->status : null;
                    $nextStatus = \App\Models\WorkOrders\Masters\Status::where('show_on', 'LIKE', "%$status->id%")->with('details.options')->get();
                    $wo->nextStatus = $nextStatus;
                    return $wo;
                }
                return abort(404);
            });
            Route::post('/wo/update/status/{wo?}/{status?}', 'WorkOrders\WorkOrder@pushAction');
            Route::post('/wo/rebooking/{wo?}', 'WorkOrders\WorkOrder@rebooking');
            Route::post('/wo/cancel/{wo?}', 'WorkOrders\WorkOrder@cancel');
            Route::post('/wo/delete', 'WorkOrders\WorkOrder@delete');
            Route::post('/wo/push', 'WorkOrders\WorkOrder@push');
            Route::post('/site/push/{id?}', 'Sites\Site@push');
        });

    });

});







