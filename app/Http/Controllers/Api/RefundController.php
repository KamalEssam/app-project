<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Repositories\Api\RefundRepository;
use App\Http\Repositories\Api\ReservationRepository;
use Illuminate\Http\Request;
use Validator;

class RefundController extends ApiController
{
    private $refundRepo;

    private const BEFORE_RESERVATION_DAY = 5;
    private const AFTER_RESERVATION_DAY = 10;


    /**
     * WorkingHourController constructor.
     * @param Request $request
     * @param RefundRepository $refundRepository
     */
    public function __construct(Request $request, RefundRepository $refundRepository)
    {
        $this->refundRepo = $refundRepository;
        $this->setLang($request);
    }

    /**
     *  add new Refund Request
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addRefundRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reservation_id' => 'required|numeric|exists:reservations,id',
        ]);

        if ($validator->fails()) {
            return self::jsonResponse(false, self::CODE_VALIDATION, $validator->errors()->first(), $validator->errors());
        }

        // check user authorization
        $auth = auth()->guard('api')->user();
        if ($auth == null) {
            return self::jsonResponse(false, self::CODE_UNAUTHORIZED, trans('lang.unauthorized'));
        }

        $reservation = (new ReservationRepository())->getReservationById($request['reservation_id']);

        // check if reservation user is the one who ask for refund
        if ($reservation->user_id !== $auth->id) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.you_are_not_reservation_owner'));
        }

        // check if there was request on this reservation or not
        if ($this->refundRepo->checkForRefundRequest($auth->id, $request['reservation_id'])) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.refund-request-already-sent'));
        }

        if ($reservation->payment_method === self::METHOD_CASH) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.refund-request-for-online-only'));
        }

        // if the refund after or before the reservation day
        $condition = $reservation->day > now()->format('Y-m-d') ? 0 : 1;

        $refund = $this->refundRepo->addRefundRequest($auth->id, $reservation->id, $condition);
        if (!$refund) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.refund-request-err'));
        }
        return self::jsonResponse(true, self::CODE_OK, trans('lang.refund-request-ok'));
    }

    /**
     *  check for the status of the refund for reservation
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkRefundStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reservation_id' => 'required|numeric|exists:reservations,id',
        ]);

        if ($validator->fails()) {
            return self::jsonResponse(false, self::CODE_VALIDATION, $validator->errors()->first(), $validator->errors());
        }

        // check user authorization
        $auth = auth()->guard('api')->user();
        if ($auth == null) {
            return self::jsonResponse(false, self::CODE_UNAUTHORIZED, trans('lang.unauthorized'));
        }

        $reservation = (new ReservationRepository())->getReservationById($request['reservation_id']);

        // check if reservation user is the one who ask for refund
        if ($reservation->user_id !== $auth->id) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.you_are_not_reservation_owner'));
        }

        // check if there was request on this reservation or not
        if ($this->refundRepo->checkForRefundRequest($auth->id, $request['reservation_id'])) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.refund-request-already-sent'));
        }

        if ($reservation->payment_method !== self::METHOD_ONLINE) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.refund-request-for-online-only'));
        }

        $payment = \DB::table('reservations_payment')->where('reservation_id', $reservation->id)->first();

        if (!$payment) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.refund-request-err'));
        }

        // if the refund after or before the reservation day
        $condition = $reservation->day > now()->format('Y-m-d') ? 0 : 1;

        $deduction_percent = $condition ? self::AFTER_RESERVATION_DAY : self::BEFORE_RESERVATION_DAY;

        $msg = $deduction_percent . '% will be dedicated from your reservation total fee (which is ' . $payment->total ?? 0 . ' EGP) due to refund policy, then you will receive ' . ($payment->total - ($payment->total * ($deduction_percent / 100)));

        return self::jsonResponse(true, self::CODE_OK, trans('lang.refund-request-err'), '', $msg);
    }
}
