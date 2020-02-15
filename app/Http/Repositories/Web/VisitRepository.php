<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:48 PM
 */

namespace App\Http\Repositories\Web;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\WebController;
use App\Http\Interfaces\Web\VisitInterface;
use App\Models\Visit;

class VisitRepository extends ParentRepository implements VisitInterface
{
    public $visit;

    public function __construct()
    {
        $this->visit = new Visit();
    }


    /**
     * get list of all countries
     *
     * @param $reservation_id
     * @return mixed
     */
    public function getVisitByReservationId($reservation_id)
    {
        try {
            return $this->visit->where('reservation_id', $reservation_id)->first();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     *  get visits using patient name or date or both
     *
     * @param $auth_user
     * @param string $date
     * @param string $name
     * @return mixed
     */
    public function filterVisitByDateAndPatientName($auth_user, $date = '', $name = '')
    {
        return $this->visit->join('users', 'visits.user_id', 'users.id')
            ->join('reservations', 'visits.reservation_id', 'reservations.id')
            ->join('clinics', 'reservations.clinic_id', 'clinics.id')
            ->where('visits.created_by',
                (new AuthRepository())->getUserByRoleAndAccountId(WebController::ROLE_DOCTOR, $auth_user->account_id)->id
            )->where(function ($query) use ($name, $date, $auth_user) {

                if ($auth_user->role_id == ApiController::ROLE_ASSISTANT) {
                    $query->where('clinics.id', $auth_user->clinic_id);
                }
                if ($date != 'none') {
                    $query->where('reservations.day', $date);
                }
                if ($name != 'none') {
                    $query->where('users.name', 'like', '%' . $name . '%');
                }
            })
            ->select('visits.*', 'reservations.day', 'clinics.' . app()->getLocale() . '_name as clinic_name', 'users.name as patient_name', 'users.id as user_id')
            ->paginate(13);
    }

    /**
     *  get all visits from Doctor
     *
     * @param $account_id
     * @return mixed
     */
    public function getAllVisitsForDoctorAccount($account_id)
    {
        // get visits that made by this Doctor
        return $this->visit->join('users', 'visits.user_id', 'users.id')
            ->join('reservations', 'visits.reservation_id', 'reservations.id')
            ->join('clinics', 'reservations.clinic_id', 'clinics.id')
            ->where('visits.created_by',
                (new AuthRepository())->getUserByRoleAndAccountId(WebController::ROLE_DOCTOR, $account_id)->id
            )->select('visits.*', 'reservations.day', 'clinics.' . app()->getLocale() . '_name as clinic_name', 'users.name as patient_name', 'users.id as user_id')
            ->get();
    }

    /**
     *  create new visit
     *
     * @param $request
     * @return mixed
     */
    public function createVisit($request)
    {
        try {
            return $this->visit->create($request->all());
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }
}