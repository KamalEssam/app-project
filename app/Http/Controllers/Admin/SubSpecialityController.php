<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\WebController;
use App\Http\Repositories\Web\SubSpecialityRepository;
use App\Http\Requests\SubSpecialityRequest;
use DB;
use Illuminate\Http\Request;

class SubSpecialityController extends WebController
{
    private $subspecialityRepository;

    public function __construct(SubSpecialityRepository $subspecialityRepository)
    {
        $this->subspecialityRepository = $subspecialityRepository;
    }

    public function all(Request $request)
    {
        if ($request->has('speciality_id')) {
            return $this->subspecialityRepository->getAllSubSpecialities($request['speciality_id']);
        }
    }

    /**
     * Store a newly created sub speciality in database
     *
     * @param SubSpecialityRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(SubSpecialityRequest $request)
    {
        // get auth user
        DB::beginTransaction();
        try {
            $iMax = count($request['en_name']);
            for ($i = 0; $i < $iMax; $i++) {
                $data = [
                    'en_name' => $request['en_name'][$i],
                    'ar_name' => $request['ar_name'][$i],
                    'speciality_id' => $request['speciality_id'],
                ];
                $this->subspecialityRepository->createSubSpeciality($data);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            self::logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.sub_speciality_add_err'), 'specialities.index');
        }
        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.sub_speciality_add_ok'), 'specialities.index');
    }


    /**
     *  show edit speciality form
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        $speciality = $this->subspecialityRepository->getSubSpecialityById($id);
        if (!$speciality) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.no_specialities'), 'specialities.index');
        }
        return view('admin.rk-admin.specialities.sub-edit', compact('speciality'));
    }

    /**
     * Update the specified speciality in storage.
     *
     * @param SubSpecialityRequest $request
     * @param  int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(SubSpecialityRequest $request, $id)
    {
        $speciality = $this->subspecialityRepository->getSubSpecialityById($id);
        if (!$speciality) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.no_specialities'), 'specialities.index');
        }

        DB::beginTransaction();
        try {
            $this->subspecialityRepository->updateSubSpeciality($speciality, $request);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            self::logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.sub_speciality_update_err'));
        }
        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.sub_speciality_update_ok'), 'specialities.index');
    }


    /**
     * Remove the specified sub Speciality from database.
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        return $this->deleteItem($this->subspecialityRepository->speciality, $id);
    }

    /**
     *  get list of sub specialities as api
     *
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function apiList(Request $request)
    {
        return $this->subspecialityRepository->apiAllSubSpecialities($request['speciality_id']);
    }
}
