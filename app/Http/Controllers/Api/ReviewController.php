<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Repositories\Api\AuthRepository;
use App\Http\Repositories\Api\DoctorRepository;
use App\Http\Repositories\Api\ReservationRepository;
use App\Http\Repositories\Api\ReviewRepository;
use App\Http\Repositories\Validation\ReviewValidationRepository;
use App\Http\Traits\UserTrait;
use Illuminate\Http\Request;

class ReviewController extends ApiController
{
    use UserTrait;
    private $reviewRepository;
    private $reviewValidation;

    /**
     * @param Request $request
     * @param ReviewRepository $reviewRepository
     * @param ReviewValidationRepository $reviewValidation
     */
    public function __construct(Request $request, ReviewRepository $reviewRepository, ReviewValidationRepository $reviewValidation)
    {
        $this->reviewRepository = $reviewRepository;
        $this->reviewValidation = $reviewValidation;
        $this->setLang($request);
    }

    /**
     *  get the reservation doctor details for review like image and doctor name
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getReservationDetailsForReview(Request $request)
    {
        // validate fields
        if (!$this->reviewValidation->getReviewInformationForReservation($request)) {
            return self::jsonResponse(false, self::CODE_VALIDATION, $this->reviewValidation->getFirstError(), $this->reviewValidation->getErrors());
        }

        $user = auth()->guard('api')->user();
        // if not unauthorized
        if ($user == null) {
            return self::jsonResponse(false, self::CODE_UNAUTHORIZED, trans('lang.unauthorized'));
        }

        $reservation = (new ReservationRepository())->getReservationWithReview($request['reservation_id']);

        if ($reservation) {
            if ($reservation->reviews == 0) {
                // get doctor reservation
                if ($reservation->clinic != null) {
                    // get doctor by account_id
                    $doctor = DoctorRepository::getDoctorInfoForReviewByAccountId($reservation->clinic->account_id);
                    return self::jsonResponse(true, self::CODE_OK, trans('lang.doctor'), '', $doctor);
                }
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.clinic_not_found'));
            }
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.reservation_has_reviews'));
        }
        return self::jsonResponse(false, self::CODE_FAILED, trans('lang.reservation_not_found'));
    }

    /**
     *  add review for reservation
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addReview(Request $request)
    {
        // validate fields
        if (!$this->reviewValidation->addReview($request)) {
            return self::jsonResponse(false, self::CODE_VALIDATION, $this->reviewValidation->getFirstError(), $this->reviewValidation->getErrors());
        }

        $user = auth()->guard('api')->user();
        // if not unauthorized
        if ($user == null) {
            return self::jsonResponse(false, self::CODE_UNAUTHORIZED, trans('lang.unauthorized'));
        }

        $reservation = (new ReservationRepository())->getReservationWithReview($request['reservation_id']);

        if ($reservation) {
            // in case reservations mixed and user want to review reservation not belonging to him
            if ($reservation->user_id != $user->id) {
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.you_are_not_reservation_owner'));
            }
            if ($reservation->reviews == 0) {
                // get doctor reservation
                if ($reservation->clinic != null) {
                    // get doctor by account_id
                    $account_id = $reservation->clinic->account_id;
                    $this->reviewRepository->addReview($account_id, $user->id, $request['rate'], $request['content'], $request['reservation_id']);
                    return self::jsonResponse(true, self::CODE_OK, trans('lang.review_added'));
                }
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.clinic_not_found'));
            }
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.reservation_has_review'));
        }
        return self::jsonResponse(false, self::CODE_FAILED, trans('lang.reservation_not_found'));
    }

    /**
     *  get list of doctor reviews
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getReviewsList(Request $request)
    {
        // validate fields
        if (!$this->reviewValidation->doctorReviews($request)) {
            return self::jsonResponse(false, self::CODE_VALIDATION, $this->reviewValidation->getFirstError(), $this->reviewValidation->getErrors());
        }

        if (strpos($request->user_id, 'RK_ACC') !== false) {
            $identifier = 'unique_id';
            $doctor = (new \App\Http\Repositories\Web\AuthRepository())::getUserByColumn($identifier, $request->user_id);
            if (!$doctor || $doctor->role_id != self::ROLE_DOCTOR) {
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.invalid_role'));
            }

            $user = (new AuthRepository())->getUserById($doctor->id);
        } else {
            $user = (new AuthRepository())->getUserById($request['user_id']);
        }

        if (!$user) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.doctor-not-found'));
        }

        if ($user->role_id != self::ROLE_DOCTOR) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.invalid-role'));
        }

        $offset = (isset($request->offset) && !empty($request->offset)) ? $request->offset : 0;
        $limit = (isset($request->limit) && !empty($request->limit)) ? $request->limit : 10;

        $reviews = (new ReviewRepository())->getDoctorReviews($user->account_id, $offset, $limit);

        if (!$reviews) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.reviews-not-found'));
        }

        return self::jsonResponse(true, self::CODE_OK, trans('lang.doctor-reviews'), '', $reviews);
    }

    /**
     *  Ignore Review of reservation
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function IgnoreReview(Request $request)
    {
        // validate fields
        if (!$this->reviewValidation->IgnoreReviewValidation($request)) {
            return self::jsonResponse(false, self::CODE_VALIDATION, $this->reviewValidation->getFirstError(), $this->reviewValidation->getErrors());
        }

        // ignore reservation
        $reservation_ignore = (new ReservationRepository())->updateReservaionColumn($request['reservation_id'], 'reservation_ignore', 1);
        if (!$reservation_ignore) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.reservation_update_err'));
        }
        return self::jsonResponse(true, self::CODE_OK, trans('lang.reservation'));
    }
}
