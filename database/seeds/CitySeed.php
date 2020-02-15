<?php

use Illuminate\Database\Seeder;
use App\Models\City;

class CitySeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $city = new City();
        $city->en_name = 'Cairo';
        $city->ar_name = 'القاهرة';
        $city->country_id = 1;
        $city->save();

        $city = new City();
        $city->en_name = 'Mansoura';
        $city->ar_name = 'المنصورة';
        $city->country_id = 1;
        $city->save();

        $city = new City();
        $city->en_name = 'Alex';
        $city->ar_name = 'الاسكندرية';
        $city->country_id = 1;
        $city->save();

        $city = new City();
        $city->en_name = 'Al Kods';
        $city->ar_name = 'القدس';
        $city->country_id = 2;
        $city->save();
    }
}