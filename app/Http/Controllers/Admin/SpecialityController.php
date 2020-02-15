<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\WebController;
use App\Http\Repositories\Web\SpecialityRepository;
use App\Http\Requests\SpecialityRequest;
use DB;

class SpecialityController extends WebController
{
    private $specialityRepository;
    private $specialities_limit = 0;

    public function __construct(SpecialityRepository $specialityRepository)
    {
        $this->specialityRepository = $specialityRepository;
        $this->specialities_limit = DB::table('settings')->where('id', 1)->first()->min_featured_stars;
    }

    /**
     *  show all specialities
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $specialities = $this->specialityRepository->getAllSpecialities();

        return view('admin.rk-admin.specialities.index', compact('specialities'));
    }

    /**
     * Store a newly created speciality in database
     *
     * @param SpecialityRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(SpecialityRequest $request)
    {
        if (isset($request->is_featured)) {
            $featured_limit = $this->specialityRepository->getFeaturedSpecialitiesCount();
            if ($featured_limit >= $this->specialities_limit) {
                return $this->messageAndRedirect(self::STATUS_ERR, trans('can-not-make-speciality-featured'));
            }
        }

        // get auth user
        DB::beginTransaction();
        try {
            $this->specialityRepository->createSpeciality($request);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            self::logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.speciality_add_err'), 'specialities.index');
        }
        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.speciality_add_ok'), 'specialities.index');
    }


    /**
     *  show edit speciality form
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        $speciality = SpecialityRepository::getSpecialityById($id);
        if (!$speciality) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.no_specialities'), 'specialities.index');
        }
        return view('admin.rk-admin.specialities.edit', compact('speciality'));
    }

    /**
     * Update the specified speciality in storage.
     *
     * @param SpecialityRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(SpecialityRequest $request, int $id)
    {
        $speciality = SpecialityRepository::getSpecialityById($id);
        if (!$speciality) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.no_specialities'), 'specialities.index');
        }

        if (isset($request->is_featured)) {
            $featured_limit = $this->specialityRepository->getFeaturedSpecialitiesCount($speciality->id);
            if ($featured_limit >= $this->specialities_limit) {
                return $this->messageAndRedirect(self::STATUS_ERR, trans('can-not-make-speciality-featured'));
            }
        }

        DB::beginTransaction();
        try {
            $this->specialityRepository->updateSpeciality($speciality, $request);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            self::logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.speciality_update_err'));
        }
        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.speciality_update_ok'), 'specialities.index');
    }

    /**
     *  set speciality featured or not
     *
     * @param $id
     * @param $status
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function featured($id, $status)
    {
        if (!isset($status) || !is_numeric($status) || !isset($id) || !is_numeric($status)) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.something_wrong'), 'specialities.index');
        }

        $speciality = SpecialityRepository::getSpecialityById($id);
        if (!$speciality) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.no_specialities'), 'specialities.index');
        }

        if ($status == self::ACTIVE) {
            $featured_limit = $this->specialityRepository->getFeaturedSpecialitiesCount();
            if ($featured_limit >= $this->specialities_limit) {
                return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.can-not-make-speciality-featured'));
            }
        }


        DB::beginTransaction();
        try {
            $this->specialityRepository->setFeatured($speciality, $status);
        } catch (\Exception $e) {
            DB::rollBack();
            self::logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.speciality_update_err'), 'specialities.index');
        }
        DB::commit();
        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.speciality_update_ok'), 'specialities.index');
    }

    /**
     * Remove the specified Speciality from database.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        return $this->deleteItem($this->specialityRepository->speciality, $id);
    }
}
