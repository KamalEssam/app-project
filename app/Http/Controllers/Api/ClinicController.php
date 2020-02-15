<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Repositories\Api\ClinicRepository;
use App\Http\Repositories\Validation\ClinicValidationRepository;
use App\Models\TemporaryReservation;
use Illuminate\Http\Request;

class ClinicController extends ApiController
{
    private $clinicRepository, $clinicValidationRepository;

    /**
     * ClinicController constructor.
     * @param Request $request
     * @param ClinicRepository $clinicRepository
     * @param ClinicValidationRepository $clinicValidationRepository
     */
    public function __construct(Request $request, ClinicRepository $clinicRepository, ClinicValidationRepository $clinicValidationRepository)
    {
        $this->clinicRepository = $clinicRepository;
        $this->clinicValidationRepository = $clinicValidationRepository;
        $this->setLang($request);
    }

    /**
     * get available days for this clinic.
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function getClinicDays(Request $request)
    {
        $appeded = [];
        $user = auth()->guard('api')->user();
        if ($user) {
            if ($user->role_id == self::ROLE_ASSISTANT) {
                $request->request->add(['clinic_id' => $user->clinic_id]);
            } else {
                $appeded = ['device_token' => 'required'];
            }
        } else {
            $appeded = ['device_token' => 'required'];
        }

        // validate fields
        if (!$this->clinicValidationRepository->getClinicIdValidation($request, $appeded)) {
            return self::jsonResponse(false, self::CODE_VALIDATION, $this->clinicValidationRepository->getFirstError(), $this->clinicValidationRepository->getErrors(), []);
        }

        // in case if the user is Patient then save the reservation to temporary reservations table
        if (!$user) {
            $this->addTemperoryRservation($request->doctor_id, $request->device_token, $request->clinic_id);
        }

        $clinic_days = $this->clinicRepository->getClinicDays($request->clinic_id, $request->start_day);

        if ($clinic_days->status == false) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.something_wrong'));
        }
        return self::jsonResponse(true, self::CODE_OK, trans('lang.get-clinic-days'), "", $clinic_days->response);
    }

    /**
     *
     *
     * @param $doctor_id
     * @param $device_toke
     * @param $clinic_id
     */
    private function addTemperoryRservation($doctor_id, $device_toke, $clinic_id): void
    {
        TemporaryReservation::updateOrCreate([
            'doctor_id' => $doctor_id,
            'device_token' => $device_toke,
            'clinic_id' => $clinic_id,
        ]);
    }
}
