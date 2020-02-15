<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\WebController;
use App\Http\Repositories\Web\CityRepository;
use App\Http\Repositories\Web\CountryRepository;
use App\Http\Requests\CityRequest;
use DB;

class CityController extends WebController
{
    private $cityRepository;

    public function __construct(CityRepository $cityRepository)
    {
        $this->cityRepository = $cityRepository;
    }

    /**
     *  show list of all cities
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $cities = $this->cityRepository->getAllCities();
        return view('admin.rk-admin.cities.index', compact('cities'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        // get all countries
        $countries = (new CountryRepository())->getAllCountries();
        return view('admin.rk-admin.cities.create', compact('countries'));
    }

    /**
     * Store new city in database
     *
     * @param CityRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(CityRequest $request)
    {
        // add city
        DB::beginTransaction();
        try {
            $request['created_by'] = auth()->user()->id;
            $this->cityRepository->createCity($request);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            // log error
            $this->logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.city_add_err'));
        }

        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.city_added_ok'), 'cities.index');
    }

    /**
     *  show edit city form
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        $city = $this->cityRepository->getCityById($id);
        if (!$city) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.city_not_found'));
        }
        // get all countries
        $countries = (new CountryRepository())->getAllCountries();
        if (count($countries) == 0) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.no_countries'));
        }
        return view('admin.rk-admin.cities.edit', compact('city', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CityRequest $request
     * @param  int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(CityRequest $request, $id)
    {
        $city = $this->cityRepository->getCityById($id);
        if (!$city) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.city_not_found'));
        }

        DB::beginTransaction();
        // update city data
        try {
            $request['updated_by'] = auth()->user()->id;
            $this->cityRepository->updateCity($city, $request);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            // log error
            $this->logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.city_update_err'));
        }
        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.city_update_ok'), 'cities.index');
    }

    /**
     * Remove city
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        return $this->deleteItem($this->cityRepository->city, $id);
    }
}
