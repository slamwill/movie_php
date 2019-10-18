<?php

namespace App\Http\Middleware;

use Closure;

class Cors
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
		return $next($request);
			//->header('Access-Control-Allow-Origin', 'http://125.227.59.92')
			//->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
	}
	/*
	public function handle($request, Closure $next)
    {
        return $next($request);
    }*/
}
