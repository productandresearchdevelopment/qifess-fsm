<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use App\SystemModels\Auth\User;
use Auth;

class LoginController extends Controller {
    use RedirectsUsers;
    use ThrottlesLogins;

    protected $redirectTo = '/';

    public function __construct()    {
        $this->middleware('guest')->except('logout');
    }

    public function username(){
        return 'username';
    }

    public function showLoginForm(){
        return view('auth.login');
    }

    public function login(Request $request){
        $username = $request->username;
        $password = $request->password;
        $user = User::where(['username' => $username])->first();
        if($user){
            if(Hash::check($password, $user->password)){
                Auth::guard()->login($user);
                return redirect()->intended($this->redirectPath());
            }
            return $this->sendFailedLoginResponse($request, 'password');
        }
        return $this->sendFailedLoginResponse($request, 'username');
    }

    protected function sendLoginResponse(Request $request){
        $request->session()->regenerate();
        $this->clearLoginAttempts($request);
        return $this->authenticated($request, $this->guard()->user()) ?: redirect()->intended($this->redirectPath());
    }

    protected function sendFailedLoginResponse(Request $request, $type){
        throw ValidationException::withMessages(['username'=> [trans('auth.failed')]]);
    }

    protected function authenticated(Request $request, $user){
        // authenticated
    }

    public function logout(Request $request){
        $user = $request->user();
        $user->update(['last_module' => null, 'last_url' => null]);
        $this->guard()->logout();
        $request->session()->invalidate();
        return redirect('/');
    }

    protected function guard(){
        return Auth::guard();
    }

    public function mobileLogin(Request $request){
        $username = $request->username;
        $password = $request->password;
        $user = User::with('role')->where(['username' => $username])->first();
        if($user){
            if(Hash::check($password, $user->password)){
                Auth::guard()->login($user);
                $token = $user->token;
                if(!$token){
                    $token = (string) Str::uuid();
                    $user->update(['token' => $token]);
                }
                return [
                    'success' => true,
                    'message' => 'success',
                    'token' => $user->token,
                    'user' => $user,
                ];
            }
            return ['success' => false, 'message' => 'Wrong Password'];
        }
        return ['success' => false, 'message' => 'Username Undefined!'];
    }

    public static function routeMobile(){
        Route::post('/login', '\App\Http\Controllers\Auth\LoginController@mobileLogin');
    }

}
