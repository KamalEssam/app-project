<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:48 PM
 */

namespace App\Http\Repositories\Web;


use App\Http\Interfaces\Web\StandByInterface;
use App\Models\ReservationStandBy;

class StandByRepository extends ParentRepository implements StandByInterface
{
    public $standBy;

    public function __construct()
    {
        $this->standBy = new ReservationStandBy();
    }

    /**
     *  set stand By Reservation
     *
     * @param $reservation_id
     * @param $queue
     * @param $clinic_id
     * @return mixed
     * @throws \Exception
     */
    public function setStandBy($reservation_id, $queue, $clinic_id)
    {
        try {
            return $standBy = $this->standBy
                ->create([
                    'reservation_id' => $reservation_id,
                    'clinic_id' => $clinic_id,
                    'queue' => $queue,
                ]);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get the Stand By reservation for this clinic
     *
     * @param $clinic_id
     * @return mixed
     */
    public function getStandBy($clinic_id)
    {
        try {
            return $this->standBy->where('clinic_id', $clinic_id)
                ->first();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  deletes stand By using Id
     *
     * @param $standBy
     * @return mixed
     */
    public function deleteStandBy($standBy)
    {
        try {
            return $standBy->delete();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }
}