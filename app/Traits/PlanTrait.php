<?php

namespace App\Traits;


use App\Http\Controllers\WebController;
use App\Models\Plan;
use DB;

trait PlanTrait
{
    public function checkAvailability()
    {
        $user = auth()->user();
        if ($user->role_id != WebController::ROLE_DOCTOR) {
            abort('404');
        }

        $account = $user->account;
        if (!$account) {
            abort('404');
        }

        $plan = Plan::where('id', $account->plan_id)->first();

        if (!$plan) {
            return false;
        }

        if ($plan->no_of_clinics == 0 ) {
            return true;
        }

        $clinics = DB::table('clinics')
            ->join('users', 'clinics.created_by', 'users.id')
            ->join('accounts', 'users.account_id', '=', 'accounts.id')
            ->where('users.account_id', $user->account_id)
            ->whereIn('users.role_id', [WebController::ROLE_DOCTOR, WebController::ROLE_ASSISTANT])
            ->count();

        // check for the number of clinics in the plan
        if ($plan->no_of_clinics == 3 && $plan->no_of_clinics > $clinics) {
            return true;
        } else {
            return false;
        }
    }
}