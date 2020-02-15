<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 10/2/18
 * Time: 11:57 AM
 */

namespace App\Http\Repositories\Api;

use App\Http\Controllers\ApiController;
use App\Http\Interfaces\Api\ReviewInterface;
use App\Http\Repositories\Web\ParentRepository;
use App\Models\Reservation;
use App\Models\Review;
use Illuminate\Support\Collection;

class ReviewRepository extends ParentRepository implements ReviewInterface
{

    public $review;

    public function __construct()
    {
        $this->review = new Review();
    }

    /**
     * get doctor reviews by account id
     *
     * @param $account_id
     * @param $offset
     * @param $limit
     * @return mixed
     */
    public function getDoctorReviews($account_id, $offset, $limit)
    {
        try {
            return $this->review::with(
                array('user' => function ($query) {
                    $query->select('id', 'name', 'image');
                })
            )
                ->where('account_id', $account_id)
                ->orderBy('reviews.created_at', 'desc')
                ->offset($offset)
                ->limit($limit)
                ->get();

        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return new Collection();
        }
    }

    /**
     *  add review to reservation
     *
     * @param $doctor_account_id
     * @param $user_id
     * @param $rate
     * @param $content
     * @param $reservation_id
     * @return mixed
     */
    public function addReview($doctor_account_id, $user_id, $rate, $content, $reservation_id)
    {
        try {
            return $this->review->create([
                'account_id' => $doctor_account_id,
                'user_id' => $user_id,
                'rate' => $rate,
                'content' => $content,
                'reservation_id' => $reservation_id
            ]);

        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  check if reservation has review or not
     *
     * @param $reservation_id
     * @return mixed
     */
    public function checkReservationReview($reservation_id)
    {
        try {
            $review = $this->review->where('reservation_id', $reservation_id)->first();
            if ($review) {
                return $review;
            }
            return false;
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }


    /**
     *  get last reservation which is not reviews
     *
     * @param $user_id
     * @return mixed
     */
    public function getLastReservationNotReviewed($user_id)
    {
        try {
            return Reservation::where('user_id', $user_id)
                ->whereIn('status', [ApiController::STATUS_ATTENDED, ApiController::STATUS_MISSED])
                ->where('reservation_ignore', 0)// Get Not Ignored Reservations
                ->whereNotIn('id',
                    $this->review->where('user_id', $user_id)->select('reservation_id')->get()->toArray()
                )
                ->orderBy('created_at', 'desc')
                ->first();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }
}
