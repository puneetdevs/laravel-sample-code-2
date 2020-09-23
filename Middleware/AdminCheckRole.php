<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use App\Role;
class AdminCheckRole
{
    /**
     * Check Role is Admin then access key otherwise return with error
     * handle
     *
     * @param  mixed $request
     * @param  mixed $next
     *
     * @return void
     */
    public function handle($request, Closure $next)
    {
        
        $role = Role::find($request->user()->role_id);
        if($role) {
            switch ($role->slug) {
                case "admin":
                    return $next($request);
                default:
                return response()->json(['message' => 'You are not authorized user to access this page.'], 403);
            }
        }else{
            return response()->json(['message' => 'You are not authorized user to access this page.'], 403);
        }
    }
}
