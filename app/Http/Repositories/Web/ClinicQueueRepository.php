<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:48 PM
 */

namespace App\Http\Repositories\Web;


use App\Http\Interfaces\Web\ClinicQueueInterface;
use App\Models\ClinicQueue;

class ClinicQueueRepository extends ParentRepository implements ClinicQueueInterface
{
    protected $clinicQueue;

    public function __construct()
    {
        $this->clinicQueue = new ClinicQueue();
    }

    /**
     *  get clinic queue using clinic id
     *
     * @param $clinic_id
     * @return mixed
     */
    public function getClinicQueueByClinic($clinic_id)
    {
        try {
            return $this->clinicQueue->where('clinic_id', $clinic_id)->today()->first();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get queue by id
     *
     * @param $queue
     * @return bool
     */
    public function getClinicQueueByID($queue)
    {
        try {
            return $this->clinicQueue->where('id', $queue)->today()->first();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }


    /**
     *  update queue to next patient
     *
     * @param $queue
     * @param $patientNumber
     * @return mixed
     */
    public function setQueueToNextReservation($queue, $patientNumber)
    {
        try {
            $queue->update([
                'queue' => $patientNumber
            ]);
            return $queue;
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  create new clinicQueue
     *
     * @param $clinic_id
     * @param $queue
     * @return mixed
     */
    public function createQueue($clinic_id, $queue)
    {
        try {
            return $this->clinicQueue->create([
                'clinic_id' => $clinic_id,
                'queue' => $queue,
                'queue_status' => 1
            ]);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  update queue parameter
     *
     * @param $queue
     * @param $key
     * @param $value
     * @return mixed
     */
    public function updateQueue($queue, $key, $value)
    {
        try {
            $queue->update([
                $key => $value
            ]);

            return $queue;
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }
}
