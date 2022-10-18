<?php
/*
 * https://www.phpencode.org/
 */
namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use App\SystemModels\Auth\User;
use Request;
use Closure;

class AuthPublicApiRoles
{
    public function handle($request, Closure $next)
    {
        $token = $request->header('token') ?: $request->input('token');
        if($token) {
            if($user = User::where('token_api', $token)->first()) {
                Auth::guard()->login($user);
                $user->update(['last_ip' => Request::ip(), 'last_active' => date('Y-m-d H:i:s')]);
                return $next($request);
            }
        }
        return response(['success' => false, 'message' => 'Invalid Token!'], 403);
    }
}
