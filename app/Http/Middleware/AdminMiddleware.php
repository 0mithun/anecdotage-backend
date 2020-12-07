<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if($request->user()->is_admin){
            return $next($request);
        }
        return \response([
            'errors'=>[
                'message'   => 'You are not authorized to access this resource'
            ]
        ], Response::HTTP_UNAUTHORIZED);
    }
}
