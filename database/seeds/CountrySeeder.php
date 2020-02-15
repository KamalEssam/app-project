<?php

use Illuminate\Database\Seeder;
use App\Models\Country;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $country  = new Country();
        $country->en_name = 'Egypt';
        $country->ar_name = 'مصر';
        $country->dialing_code = "02";
        $country->save();

        $country  = new Country();
        $country->en_name = 'Palastin';
        $country->ar_name = 'فلسطين';
        $country->dialing_code = "05";
        $country->save();
    }
}
