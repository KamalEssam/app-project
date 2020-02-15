<?php

namespace App\Http\Middleware;

use Closure;
use MercurySeries\Flashy\Flashy;

class Role
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $actions = $request->route()->getAction();
        // get roles to check if authorized or not
        $roles = isset($actions['roles']) ? $actions['roles'] : (isset($request->data['roles']) ? $request->data['roles'] : NULL);
        if ($request->user()->hasAnyRoles($roles) || !$roles) {

            if (auth()->user()->role_id == 1) {
                // check for not allowed sections like assistant and queue in poly-clinic
                if (auth()->user()->account->type == 1 && (\Request::is('assistants*') || \Request::is('queue/doctor*'))) {
                    Flashy::error('Not Allowed');
                    return redirect()->back();
                }
            }
            return $next($request);
        }

        auth()->logout();
        session()->flash('error', 'unauthorized ..');
        return redirect()->back();
    }
}
