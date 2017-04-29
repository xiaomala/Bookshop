<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {

        $http_referer = $_SERVER['HTTP_REFERER'];

        $member = $request->session()->get('member','');
        if($member == ''){
            return redirect()->guest('/login?return_url='. urlencode( $http_referer) );
        }

//        if (Auth::guard($guard)->guest()) {
//            if ($request->ajax() || $request->wantsJson()) {
//                return response('Unauthorized.', 401);
//            } else {
//                return redirect()->guest('/login?return_url='. urlencode( $http_referer) );
//            }
//        }

        return $next($request);
    }
}
