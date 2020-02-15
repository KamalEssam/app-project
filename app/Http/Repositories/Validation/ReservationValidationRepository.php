<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 10/2/18
 * Time: 11:31 AM
 */

namespace App\Http\Repositories\Validation;


use App\Http\Interfaces\Validation\ReservationValidationInterface;
use Illuminate\Http\Request;
use Validator;


class ReservationValidationRepository extends ValidationRepository implements ReservationValidationInterface
{
    /**
     * @param Request $request
     * @return bool|mixed
     */
    public function addReservationValidation(Request $request)
    {
        // Validation area
        $validator = Validator::make($request->all(), [
            'clinic_id' => 'required|exists:clinics,id',
            'type' => 'required',
            'day' => 'required',
            'payment_method' => 'required|max:1|min:0',
            'transaction_id' => 'sometimes',
            'working_hour_id' => 'sometimes',
        ]);

        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }
        return true;
    }

    /**
     * check reservation id to reschedule reservation
     * @param Request $request
     * @return mixed
     */
    public function reservationIdValidation(Request $request)
    {
        // Validation area
        $validator = Validator::make($request->all(), [
            'reservation_id' => 'required|exists:reservations,id',
        ]);
        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }
        return true;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function setStatusValidation(Request $request)
    {
        // Validation area
        $validator = Validator::make($request->all(), [
            'status' => 'required',
            'reservation_id' => 'required|exists:reservations,id',
        ]);
        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }
        return true;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getClinicIdValidation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'clinic_id' => 'required|exists:clinics,id',
        ]);
        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }
        return true;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function nextQueueValidation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|numeric|min:3|max:4',
        ]);
        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }
        return true;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function setStandBy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reservation_id' => 'required|numeric|exists:reservations,id',
        ]);
        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }
        return true;
    }
}