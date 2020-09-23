<?php

namespace App\Http\Middleware;

use Closure;

class checkHeader
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
        
        if(!$request->header('PUBLIC-API-HEADER')){ 
            return response()->json(['message' => 'You are not authorized user to access this page.'], 403);
        }  
  
        if($request->header('PUBLIC-API-HEADER') != env('PUBLIC_API_KEY')){  
            return response()->json(['message' => 'You are not authorized user to access this page.'], 403);
        }  
        return $next($request);  
    }
}
