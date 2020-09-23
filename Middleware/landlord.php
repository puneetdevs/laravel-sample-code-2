<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
class landlord
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
        if (Auth::check()) {
            $tenantId = Auth::user()->region_id;
            \Landlord::addTenant('region_id', $tenantId); // Different column name, but same concept
        }

        return $next($request);
    }
}
