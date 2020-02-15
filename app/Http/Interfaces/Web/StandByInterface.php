<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:45 PM
 */

namespace App\Http\Interfaces\Web;

interface StandByInterface
{
    /**
     * @param $reservation_id
     * @param $queue
     * @param $clinic_id
     * @return mixed
     */
    public function setStandBy($reservation_id, $queue, $clinic_id);

    /**
     * @param $clinic_id
     * @return mixed
     */
    public function getStandBy($clinic_id);

    /**
     *  deletes stand By using Id
     *
     * @param $standBy
     * @return mixed
     */
    public function deleteStandBy($standBy);
}