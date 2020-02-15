<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\WebController;
use App\Http\Repositories\Web\CityRepository;
use App\Http\Repositories\Web\ProvinceRepository;
use App\Http\Requests\ProvinceRequest;
use DB;
use Illuminate\Http\Request;

class ProvinceController extends WebController
{
    private $provinceRepository;

    public function __construct(ProvinceRepository $provinceRepository)
    {
        $this->provinceRepository = $provinceRepository;
    }

    /**
     *  show list of all cities
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $provinces = $this->provinceRepository->getAllProvinces();
        return view('admin.rk-admin.provinces.index', compact('provinces'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        // get all cities
        $cities = (new CityRepository())->getAllCities();
        return view('admin.rk-admin.provinces.create', compact('cities'));
    }

    /**
     * Store new province in database
     *
     * @param ProvinceRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(ProvinceRequest $request)
    {
        // add province
        DB::beginTransaction();
        try {
            $this->provinceRepository->createProvince($request);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            // log error
            $this->logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.province_add_err'));
        }

        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.province_added_ok'), 'provinces.index');
    }

    /**
     *  show edit province form
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        $province = $this->provinceRepository->getProvinceById($id);
        if (!$province) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.province_not_found'));
        }
        // get all cities
        $cities = (new CityRepository())->getAllCities();
        if (count($cities) == 0) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.no_cities'));
        }
        return view('admin.rk-admin.provinces.edit', compact('province', 'cities'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ProvinceRequest $request
     * @param  int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(ProvinceRequest $request, $id)
    {
        $province = $this->provinceRepository->getProvinceById($id);
        if (!$province) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.province_not_found'));
        }

        DB::beginTransaction();
        // update province data
        try {
            $this->provinceRepository->updateProvince($province, $request);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            // log error
            $this->logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.province_update_err'));
        }
        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.province_update_ok'), 'provinces.index');
    }

    /**
     * Remove province
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        return $this->deleteItem($this->provinceRepository->province, $id);
    }

    /**
     *  get list of provinces for city
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProvincesByCityId(Request $request)
    {
        $city_id = $request->city_id;
        if (is_numeric($city_id)) {
            $provinces = $this->provinceRepository->getProvincesByCityIdForAjax($city_id);
            return response()->json(['status' => true, 'provinces' => $provinces]);
        } else {
            return response()->json(['status' => false]);
        }
    }
}
