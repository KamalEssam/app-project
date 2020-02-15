<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\WebController;
use App\Http\Repositories\Web\CityRepository;
use App\Http\Repositories\Web\CountryRepository;

class SelectController extends WebController
{
    public function getFilteredCities($id)
    {
        $country = CountryRepository::getRecordByColumn('id', $id);
        if (!$country) {
            return view('admin.rk-admin.accounts.country-cities', ['cities' => []]);
        }
        $cities = (new CityRepository())->getCitiesByCountryId($country->id);
        if (!$cities) {
            return view('admin.rk-admin.accounts.country-cities', ['cities' => []]);
        }
        return view('admin.rk-admin.accounts.country-cities', compact('cities'));
    }
}
