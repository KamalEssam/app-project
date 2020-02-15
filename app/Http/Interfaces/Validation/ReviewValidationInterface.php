<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 10/2/18
 * Time: 11:30 AM
 */

namespace App\Http\Interfaces\Validation;


use Illuminate\Http\Request;

interface ReviewValidationInterface
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function getReviewInformationForReservation(Request $request);

    /**
     * @param Request $request
     * @return mixed
     */
    public function addReview(Request $request);
}
