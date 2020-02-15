<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\WebController;
use App\Http\Repositories\Web\ClinicRepository;
use App\Http\Repositories\Web\HolidayRepository;
use App\Http\Repositories\Web\NotificationRepository;
use App\Http\Requests\HolidayRequest;
use App\Http\Traits\DateTrait;
use App\Models\Holiday;
use DB;

class HolidayController extends WebController
{
    private $holidayRepository, $error;

    public function __construct(HolidayRepository $holidayRepository)
    {
        $this->holidayRepository = $holidayRepository;

    }

    /**
     *  hist of all holidays in this clinic
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if (auth()->user()->role_id == self::ROLE_DOCTOR && auth()->user()->account->type == 1) {
            $clinics = (new ClinicRepository())->getIdsOfAccountClinics(auth()->user()->account_id);
            $holidays = $this->holidayRepository->getHalidesByClinicId($clinics);
        } else {
            $holidays = $this->holidayRepository->getHalidesByClinicId(auth()->user()->clinic_id);
        }

        return view('admin.assistant.holiday.index', compact('holidays'));
    }

    /**
     *  Store a newly created policy
     *
     * @param HolidayRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(HolidayRequest $request)
    {
        DB::beginTransaction();
        // in case of single
        if (is_null($request['clinics']) && auth()->user()->role_id == self::ROLE_ASSISTANT) {
            $request['clinic_id'] = auth()->user()->clinic_id;
            if (!$this->storeFunction($request)) {
                return response()->json(['status' => false, 'msg' => $this->error]);
            }
        } else {
            if (in_array('-1', $request['clinics'])) {
                $clinics_list = (new ClinicRepository())->getIdsOfAccountClinics(auth()->user()->account_id);
                // in case of all clinic
            } else {
                // in case of some clinics only
                $clinics_list = $request['clinics'];
            }
            foreach ($clinics_list as $clinic_id) {
                $request['clinic_id'] = $clinic_id;
                if (!$this->storeFunction($request)) {
                    return response()->json(['status' => false, 'msg' => $this->error]);
                }
            }
        }
        return response()->json(['status' => true, 'msg' => trans('lang.holiday_added_ok')]);
    }

    public function storeFunction($request)
    {

        if ($this->holidayRepository->getHolidayByDayAndClinic($request['day'], $request['clinic_id'])) {
            $this->error = trans('lang.holiday_exists');
            return false;
        }

        // add the policy
        try {
            $request['day_index'] = DateTrait::getDayIndex($request['day']);
            $this->holidayRepository->createHoliday($request);
            // delete reservations in that day and send to then both sms and push notification
            (new NotificationRepository())->canceledReservationsOnHoliday($request['day']);
        } catch (\Exception $e) {
            DB::rollback();
            // log error
            $this->error = trans('lang.holiday_add_err');
            $this->logErr($e->getMessage());
            return false;
        }
        DB::commit();
        return true;
    }

    /**
     * Remove the policy
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        return $this->deleteItem($this->holidayRepository->holiday, $id);
    }
}