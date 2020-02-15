<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 10/2/18
 * Time: 11:57 AM
 */

namespace App\Http\Repositories\Api;

use App\Http\Controllers\ApiController;
use App\Http\Interfaces\Api\ClinicInterface;
use App\Http\Repositories\Web\HolidayRepository;
use App\Models\Clinic;
use App\Http\Traits\DateTrait;
use App\Models\User;
use DateInterval;
use DatePeriod;
use DB;
use Illuminate\Support\Facades\Config;

class ClinicRepository implements ClinicInterface
{
    use DateTrait;

    /**
     * @param $clinic_id
     * @return mixed
     */
    public function getClinicById($clinic_id)
    {
        return Clinic::find($clinic_id);
    }

    /**
     * get clinic days
     * @param $clinic_id
     * @param $start_date
     * @return mixed
     * @throws \Exception
     */
    public function getClinicDays($clinic_id, $start_date)
    {
        $response = new \stdClass();
        $response->status = false;
        $response->response = [];
        $available_days = [];
        // find clinic
        $clinic = $this->getClinicById($clinic_id);

        // get clinic working hours
        $working_hours = $clinic->workingHours->pluck('day')->toArray();

        $start_date = ($start_date != null) ? $start_date : self::getToday();
        try {
            // last date patient can reserve
            $reservation_deadline = (self::getToday())->addDays($clinic->reservation_deadline);
        } catch (\Exception $e) {
            $response->status = false;
            $response->response = [];
            return $response;
        }
        if (self::parseDate($start_date) != self::getToday()) {
            $start_date = self::parseDate($start_date)->addDays(1);
        }
        // to get last day
        $reservation_deadline = $reservation_deadline->modify('+1 day');
        // to add one day as counter
        $interval = new DateInterval('P1D');
        // get period patient can reserve in it
        $period = new DatePeriod(self::parseDate($start_date), $interval, $reservation_deadline);
        // get the holiday days
        $holidays = (new HolidayRepository())->getArrayOfDaysOfHolidayUsingClinic($clinic_id);

        // loop on days to get available days patient can reserve on it
        foreach ($period as $i => $date) {
            if ($i >= 15) {
                break;
            }
            // format date
            $date_formatted = self::getDateByFormat($date, 'Y-m-d');
            //get index of the day
            $index = self::getDayIndex($date_formatted);
            // make new object
            $day = new \stdClass();
            // check if returned day in available clinic days  and the day is not holiday
            if (in_array($index, $working_hours) && !in_array($date_formatted, $holidays)) {
                /*  count times available in this day*/

                $day->date = $date_formatted;
                $day->month = (app()->getLocale() == 'en') ? self::getDateByFormat($date, 'M') : \Config::get('months.' . self::getDateByFormat($date, 'M'));
                $day->day = (app()->getLocale() == 'en') ? self::getDateByFormat($date, 'D') : \Config::get('lists.days')[$index]['ar_name'];
                $day->day_number = (app()->getLocale() == 'en') ? self::getDateByFormat($date, 'd') : self::enToAr(self::getDateByFormat($date, 'd'));
                $is_avaliable = (new WorkingHourRepository())->getWorkingHoursInClinicThatIsNotReservedOrOver($date_formatted, $clinic_id);
                $day->is_available = $is_avaliable->count() > 0 ? 1 : 0;
                // dont pass the not avaliable days
                if ($day->is_available === 1) {

                }

                $available_days[] = $day;
            }
        }
        $response->status = true;
        $response->response = $available_days;

        return $response;
    }

    /**
     * get all clinics related to same account
     * @param $account_id
     * @return mixed
     */
    public function getClinicsRelatedToSameAccount($account_id)
    {
        try {
            $clinics_related_to_same_account = Clinic::where('account_id', $account_id)->get();
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }
        return $clinics_related_to_same_account;
    }

    /**
     * get clinic pattern for auth user
     * @param $user
     * @return mixed
     */
    public function getClinicPattern($user)
    {
        $assistant = User::where('id', $user->id)->where('role_id', ApiController::ROLE_ASSISTANT)->first();
        if (!$assistant) {
            return false;
        }
        $clinic = $this->getClinicById($assistant->clinic_id);
        if (!$clinic) {
            return false;
        }
        return $clinic;
    }

    /**
     *  get the info of clinic
     *
     * @param $clinic_id
     * @return \stdClass
     */
    public function getClinicInfo($clinic_id, $account_type)
    {
        try {
            if ($account_type == ApiController::ACCOUNT_TYPE_POLY) {
                return Clinic::join('accounts', 'clinics.account_id', 'accounts.id')
                    ->join('specialities', 'clinics.speciality_id', 'specialities.id')
                    ->where('clinics.id', $clinic_id)
                    ->select('clinics.id',
                        'clinics.fees',
                        'clinics.premium_fees',
                        'accounts.type as account_type',
                        'specialities.' . app()->getLocale() . '_speciality as speciality',
                        'clinics.' . app()->getLocale() . '_name',
                        DB::raw('CONCAT(clinics.' . app()->getLocale() . '_name," ", "(" ," ",specialities.' . app()->getLocale() . '_speciality," ", ")") AS name'),
                        'clinics.pattern'
                    )->first();
            }

            return Clinic::join('accounts', 'accounts.id', 'clinics.account_id')
                ->join('provinces', 'provinces.id', 'clinics.province_id')
                ->join('doctor_details', 'doctor_details.account_id', 'accounts.id')
                ->join('specialities', 'doctor_details.speciality_id', 'specialities.id')
                ->where('clinics.id', $clinic_id)
                ->select('clinics.id',
                    'clinics.fees',
                    'clinics.premium_fees',
                    'clinics.lat',
                    'clinics.lng',
                    'accounts.type as account_type', 'specialities.' . app()->getLocale() . '_speciality as speciality',
                    'clinics.pattern',
                    'provinces.' . app()->getLocale() . '_name as province_name',
                    DB::raw('CONCAT(SUBSTRING(clinics.' . app()->getLocale() . '_address,1,20)," ...") as name')
                )
                ->first();
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            return null;
        }
    }
}
