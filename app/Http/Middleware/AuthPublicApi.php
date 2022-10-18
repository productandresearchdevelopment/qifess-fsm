<?php
/*
 * https://www.phpencode.org/
 */
namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use App\SystemModels\Auth\User;
use Request;
use Closure;

class AuthPublicApi
{
    public function handle($request, Closure $next)
    {
        $key = $request->header('key') ?: $request->input('key');
        if($key == 'vxdWj33ubnHtlwj3iTbGheASMiN5yZLhbbUvC5Umy872huCNcAtOC'){
            return $next($request);
        }
        return response(['success' => false, 'message' => 'Invalid Key!'], 403);
    }
}
