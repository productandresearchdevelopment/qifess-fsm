<?php
namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\SystemModels\Auth\User;
use Request;
use Closure;

class AuthMobileRoles
{
    public function handle($request, Closure $next)
    {
        $token = $request->header('token');
        $token = $token ? $token : $request->input('token');
        if($user = User::where('token', $token)->first()) {
            $route = Route::getCurrentRoute()->getName();
            if ($user->hasRoute($route)) {
                Auth::guard()->login($user);
                $user->update(['last_ip' => Request::ip(), 'last_active' => date('Y-m-d H:i:s')]);
                return $next($request);
            }
        }
        return abort(403);
    }
}
