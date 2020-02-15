<?php

use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $plan = new Plan();
        $plan->en_name = 'Plan 1';
        $plan->ar_name = 'الخطة الاولى';
        $plan->price_of_day = 50;
        $plan->en_desc = "This is first desc";
        $plan->ar_desc = "هذا الوصف الاول";
        $plan->no_of_clinics = 3;
        $plan->save();

        $plan = new Plan();
        $plan->en_name = 'Plan 2';
        $plan->ar_name = 'الخطة الثانية';
        $plan->price_of_day = 100;
        $plan->en_desc = "This is Second desc";
        $plan->ar_desc = "هذا الوصف الثانى";
        $plan->no_of_clinics = 0;
        $plan->save();

    }
}