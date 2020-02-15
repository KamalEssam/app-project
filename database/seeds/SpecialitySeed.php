<?php

use Illuminate\Database\Seeder;
use App\Models\Speciality;

class SpecialitySeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $plan = new Speciality();
        $plan->en_speciality = 'Dermatology';
        $plan->ar_speciality = 'جلدية';
        $plan->save();

        $plan = new Speciality();
        $plan->en_speciality = 'Psychologist';
        $plan->ar_speciality = 'نفسية';
        $plan->save();
    }
}