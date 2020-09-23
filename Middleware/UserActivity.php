<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use Illuminate\Support\Facades\Auth;
class UserActivity
{
    /**
     * Check User last activity 
     * if less then 20min then request send to next and update last activity
     * else token expired.
     * handle
     *
     * @param  mixed $request
     * @param  mixed $next
     *
     * @return void
     */
    public function handle($request, Closure $next)
    {
        if(Auth::user()) {
            $user = User::select('last_activity')->where('id',Auth::user()->id)->first();
            if($user && !empty($user->last_activity)){
                $mintues = (env('SESSION_EXPIRE_TIME')) ? env('SESSION_EXPIRE_TIME') : '20';
                $expire_time = date('Y-m-d H:i:s' , strtotime($user->last_activity.' + '.$mintues.' minute'));
                if(date('Y-m-d H:i:s') <= $expire_time){
                    User::where('id', Auth::user()->id)->update(['last_activity' => date('Y-m-d H:i:s')]);
                    return $next($request);
                }else{
                    return response()->json(['message' => 'Unauthenticated.'], 500);
                }
            }else{
                return response()->json(['message' => 'Unauthenticated.'], 500);
            }
        }else{
            return response()->json(['message' => 'Unauthenticated'], 500);
        }
    }
}
