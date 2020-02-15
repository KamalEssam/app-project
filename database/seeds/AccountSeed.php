<?php

use Illuminate\Database\Seeder;
use App\Models\Account;

class AccountSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $account = new Account();
        $account->en_name = 'Clinic 1 Doctor 1';
        $account->ar_name = 'عيادة الدكتور 1';
        $account->unique_id = 'RK_ACC_1000';
        $account->due_amount = 5000;
        $account->due_date = "2020-03-07";
        $account->city_id = 1;
        $account->plan_id = 1;
        $account->is_published = 1;
        $account->save();

        $account = new Account();
        $account->en_name = 'Clinic 1 Doctor 2';
        $account->ar_name = 'عيادة الدكتور 1';
        $account->unique_id = 'RK_ACC_1001';
        $account->due_amount = 10000;
        $account->due_date = "2020-04-07";
        $account->city_id = 1;
        $account->plan_id = 2;
        $account->is_published = 1;
        $account->save();
    }
}