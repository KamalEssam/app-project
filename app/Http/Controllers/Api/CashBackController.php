<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Repositories\Api\CashBackRepository;
use App\Http\Repositories\Api\ClinicRepository;
use App\Http\Repositories\Api\ReservationRepository;
use Illuminate\Http\Request;
use Validator;

class CashBackController extends ApiController
{
    private $cashBackRepo;

    private const CASH_REQUEST_SENT = 1;
//    private const CASH_REQUEST_APPROVED = 2;
//    private const CASH_REQUEST_DISAPPROVED = -1;

    /**
     * WorkingHourController constructor.
     * @param Request $request
     * @param CashBackRepository $cashBackRepository
     */
    public function __construct(Request $request, CashBackRepository $cashBackRepository)
    {
        $this->cashBackRepo = $cashBackRepository;
        $this->setLang($request);
    }

    /**
     *  create cashBack request from mobile or website
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function requestCashBack(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reservation_id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return self::jsonResponse(false, self::CODE_NOT_FOUND, $validator->errors()->first(), $validator->errors());
        }

        // check user authorization
        $auth = auth()->guard('api')->user();
        if ($auth == null) {
            return self::jsonResponse(false, self::CODE_UNAUTHORIZED, trans('lang.unauthorized'));
        }

        // check if reservation exists
        $reservation = (new ReservationRepository())->getReservationById($request->get('reservation_id'));
        if (!$reservation) {
            return self::jsonResponse(false, self::CODE_NOT_FOUND, trans('lang.reservations-not-found'));
        }

        // check if user owner of reservation
        if ($reservation->user_id != $auth->id) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.you_are_not_reservation_owner'));
        }

        // check if there is already cashback request for this reservation
        $is_cashBack = $this->cashBackRepo->checkIfReservaionHasCashBack($reservation->id);

        if ($is_cashBack) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.cash_back_already_sent'));
        }

        // get clinic account
        $clinic = (new ClinicRepository())->getClinicById($reservation->clinic_id);
        if (!$clinic) {
            return self::jsonResponse(false, self::CODE_NOT_FOUND, trans('lang.clinic_not_found'));
        }

        $fees = (new ReservationRepository())->getReservationFeesAfterReservation($reservation->id);

        if ($fees == null) {
            return self::jsonResponse(false, self::CODE_NOT_FOUND, trans('lang.fees_not_found'));
        }

        $cashBack = $this->cashBackRepo->requestCashBack($auth->id, $reservation->id, $clinic->id, $clinic->account_id, $fees->total_fees);
        if (!$cashBack) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.failed_to_request_cash_back'));
        }

        // update reservation cash back status to be pending
        (new ReservationRepository())->updateReservaionColumn($reservation->id, 'cashback_status', self::CASH_REQUEST_SENT);

        return self::jsonResponse(true, self::CODE_OK, trans('lang.request_cash_back_ok'));
    }

}
