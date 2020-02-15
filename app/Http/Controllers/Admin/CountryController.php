<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\WebController;
use App\Http\Repositories\Web\CountryRepository;
use App\Http\Requests\CountryRequest;
use DB;

class CountryController extends WebController
{
    private $countryRepository;

    public function __construct(CountryRepository $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    /**
     *  get list of all countries in the application
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $countries = $this->countryRepository->getAllCountries();
        return view('admin.rk-admin.countries.index', compact('countries'));
    }

    /**
     * Store a newly created country
     *
     * @param CountryRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(CountryRequest $request)
    {
        DB::beginTransaction();
        // add the country
        try {
            $this->countryRepository->createCountry($request);
        } catch (\Exception $e) {
            DB::rollback();
            // log error
            $this->logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.country_add_err'));
        }
        DB::commit();
        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.country_added_ok'), 'countries.index');
    }

    /**
     * show edit country page
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        $country = $this->countryRepository->getCountryById($id);
        if (!$country) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.country_not_found'));
        }
        return view('admin.rk-admin.countries.edit', compact('country'));
    }

    /**
     * Update the country data
     *
     * @param CountryRequest $request
     * @param  int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(CountryRequest $request, $id)
    {

        $country = $this->countryRepository->getCountryById($id);
        if (!$country) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.country_not_found'));
        }

        DB::beginTransaction();
        try {
            $this->countryRepository->updateCountry($country, $request);
        } catch (\Exception $e) {
            DB::rollback();
            // log error
            $this->logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.country_update_err'));
        }
        DB::commit();
        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.country_update_ok'), 'countries.index');
    }

    /**
     * Remove the country
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        return $this->deleteItem($this->countryRepository->country, $id);
    }
}