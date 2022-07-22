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


