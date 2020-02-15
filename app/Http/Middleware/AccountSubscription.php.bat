<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Support\Carbon;
Use Flashy;
use Auth;

class AccountSubscription
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = $request->user();
        if (!$user) {
            abort(503);
        }
        switch ($user->role_id) {
            case 1 :
                if (isset($user->account) && $user->account->due_date <= Carbon::today()->format('Y-m-d') && $user->account->plan_id != Null) {
                    Flashy::error('You have to renew your subscription');
                    Auth::logout();
                    return redirect('/suspended');
                } elseif (isset($user->account) && $user->account->due_date <= Carbon::today()->format('Y-m-d') && $user->account->plan_id == Null) {
                    return redirect('plans');
                }
                return $next($request);
            case 2 :
                if (isset($user->account) && $user->account->due_date <= Carbon::today()->format('Y-m-d') && $user->account->plan_id != Null) {
                    Flashy::error('You have to renew your subscription');
                    Auth::logout();
                    return redirect('/suspended');
                } elseif (isset($user->account) && $user->account->due_date <= Carbon::today()->format('Y-m-d') && $user->account->plan_id == Null) {
                    return redirect('plans');
                }
                return $next($request);
            case 3 :
                Flashy::error('UnAuthorized');
                Auth::logout();
                break;
            case 4 :
                return $next($request);
                break;

            default :
                return $next($request);
        }
    }
}
