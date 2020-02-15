<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\WebController;
use App\Http\Repositories\Web\ClinicRepository;
use App\Http\Repositories\Web\DoctorDetailsRepository;
use App\Http\Requests\ClinicRequest;
use App\Models\Clinic;
use Illuminate\Http\Request;
use DB;

class ClinicController extends WebController
{
    /**
     * Display a listing of all clinics
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        $clinics = (new ClinicRepository())->getDoctorClinicsOrdered(auth()->user()->account_id);
        return view('admin.doctor.clinics.index', compact('clinics'));
    }

    /**
     *  store new clinic in database
     *
     * @param ClinicRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(ClinicRequest $request)
    {
        $request['account_id'] = auth()->user()->account_id;
        DB::beginTransaction();
        try {
            // in case of poly clinic
            (new ClinicRepository())->createClinic($request);
        } catch (\Exception $e) {
            DB::rollback();
            self::logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, (auth()->user()->account->type == 0) ? trans('lang.clinic_add_err') : trans('lang.clinic_add_err_poly'), 'clinics.index');
        }
        DB::commit();

        // update the min fees and premium min fees
        if (auth()->user()->role_id == self::ROLE_DOCTOR) {
            (new DoctorDetailsRepository())->updateMinFees();
        }
        return $this->messageAndRedirect(self::STATUS_OK, (auth()->user()->account->type == 0) ? trans('lang.clinic_added_ok') : trans('lang.clinic_added_ok_poly'), 'clinics.index');
    }

    /**
     *  show edit clinic form
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        $clinic = ClinicRepository::getClinicById($id);
        if (!$clinic) {
            return $this->messageAndRedirect(self::STATUS_ERR, (auth()->user()->account->type == 0) ? trans('lang.clinic_not_found') : trans('lang.clinic_not_found_poly'));
        }
        return view('admin.doctor.clinics.edit', compact('clinic'));
    }

    /**
     * Update the specified clinic in database.
     *
     * @param ClinicRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function update(ClinicRequest $request, $id)
    {
        $auth_user = auth()->user();
        $clinic = ClinicRepository::getClinicById($id);
        if (!$clinic) {
            return $this->messageAndRedirect(self::STATUS_ERR, (auth()->user()->account->type == 0) ? trans('lang.clinic_not_found') : trans('lang.clinic_not_found_poly'));
        }
        DB::beginTransaction();
        try {
            // update the clinic
            $clinic = (new ClinicRepository())->updateClinicInDoctorAndAssistant($clinic, $this->checkIfUSerIsDoctor($auth_user), $request, $auth_user->id);
            DB::commit();
        } catch (\Exception $e) {
            \Log::info($e->getMessage());

            DB::rollback();
            self::logErr($e->getMessage());
            return $this->redirectWhenUpdateClinic($auth_user, $clinic, self::STATUS_ERR, (auth()->user()->account->type == 0) ? trans('lang.clinic_update_err') : trans('lang.clinic_update_err_poly'));
        }
        // update the min fees and premium min fees
        if ($auth_user->role_id == self::ROLE_DOCTOR) {
            (new DoctorDetailsRepository())->updateMinFees();
        }
        return $this->redirectWhenUpdateClinic($auth_user, $clinic, self::STATUS_OK, (auth()->user()->account->type == 0) ? trans('lang.clinic_update_ok') : trans('lang.clinic_update_ok_poly'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        // In case of Clinic
        try {
            // update the fees and min fees with new values excepting the current id
            // because we cant do this option after deleting
            (new DoctorDetailsRepository())->updateMinFees($id);
        } catch (\Exception $e) {
            DB::rollback();
            self::logErr($e->getMessage());
            return response()->json(['msg' => false], 200);
        }
        DB::commit();
        return $this->deleteItem(Clinic::class, $id);
    }

    /***********************      helper methods       ****************************/

    /**
     *  redirect user when update clinic information
     *
     * @param $auth_user
     * @param $clinic
     * @param $status
     * @param $msg
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectWhenUpdateClinic($auth_user, $clinic, $status, $msg)
    {
        if ($this->checkIfUSerIsDoctor($auth_user)) {
            return $this->messageAndRedirect($status, $msg, 'clinics.index', $clinic->id);
        }
        return $this->messageAndRedirect($status, $msg, '');
    }

    /**
     *  check if user is Doctor or not
     *
     * @param $auth_user
     * @return bool
     */
    public function checkIfUSerIsDoctor($auth_user)
    {
        return $auth_user->role_id == self::ROLE_DOCTOR;
    }


    // *************************** Ajax Area ******************************************************************************************

    public function clinicSystem(Request $request)
    {
        $clinic = ClinicRepository::getClinicById($request->id);
        if (!$clinic) {
            abort(404, 'Clinic not found');
        }
        return $clinic;
    }
}
