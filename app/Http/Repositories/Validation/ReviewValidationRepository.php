<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 10/1/18
 * Time: 4:39 PM
 */

namespace App\Http\Repositories\Validation;


use App\Http\Interfaces\Validation\ReviewValidationInterface;
use Illuminate\Http\Request;
use Validator;

class ReviewValidationRepository extends ValidationRepository implements ReviewValidationInterface
{

    /**
     * @param Request $request
     * @return mixed
     */
    public function getReviewInformationForReservation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reservation_id' => 'required|numeric',
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
    public function IgnoreReviewValidation(Request $request)
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

    /**
     * @param Request $request
     * @return mixed
     */
    public function addReview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reservation_id' => 'required|numeric',
            'rate' => 'required|numeric|min:0|max:5'
        ]);

        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }
        return true;
    }

    /**
     *  reviews of doctors
     *
     * @param Request $request
     * @return bool
     */
    public function doctorReviews(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }
        return true;
    }
}
