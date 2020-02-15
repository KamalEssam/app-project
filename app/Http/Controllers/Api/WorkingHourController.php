<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Repositories\Api\ClinicRepository;
use App\Http\Repositories\Api\WorkingHourRepository;
use App\Http\Repositories\Validation\WorkingHourValidationRepository;
use App\Http\Traits\DateTrait;
use Illuminate\Http\Request;

class WorkingHourController extends ApiController
{
    private $workingHourRepository, $workingHourValidationRepository;

    /**
     * WorkingHourController constructor.
     * @param Request $request
     * @param WorkingHourRepository $workingHourRepository
     * @param WorkingHourValidationRepository $workingHourValidationRepository
     */
    public function __construct(Request $request, WorkingHourRepository $workingHourRepository, WorkingHourValidationRepository $workingHourValidationRepository)
    {
        $this->workingHourRepository = $workingHourRepository;
        $this->workingHourValidationRepository = $workingHourValidationRepository;
        $this->setLang($request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDayWorkingHours(Request $request)
    {
        $user = auth()->guard('api')->user();
        if ($user && $user->role_id == self::ROLE_ASSISTANT) {
            $request->request->add(['clinic_id' => $user->clinic_id]);
        }
        // validate fields
        if (!$this->workingHourValidationRepository->getDayWorkingHoursValidation($request)) {
            return self::jsonResponse(false, self::CODE_VALIDATION, $this->workingHourValidationRepository->getFirstError(), $this->workingHourValidationRepository->getErrors());
        }

        $clinic = (new ClinicRepository())->getClinicById($request->clinic_id);
        if ($clinic) {
            if ($clinic->pattern == self::PATTERN_INTERVAL) {
                $day_working_hours = $this->workingHourRepository->getWorkingHoursInClinicThatIsNotReservedOrOver($request->day, $request->clinic_id);
            } else {
                // get the breaks in case of interval
                $dayIndex = DateTrait::getDayIndex($request->day);
                $day_working_hours = $this->workingHourRepository->getArrayOfBreakTimesInQueue($request->clinic_id, $dayIndex);
            }
            return self::jsonResponse(true, self::CODE_OK, trans('lang.get-day-times'), new \stdClass(), $day_working_hours);
        }

        return self::jsonResponse(false, self::CODE_OK, trans('lang.get-day-times'), new \stdClass(), '');
    }
}
