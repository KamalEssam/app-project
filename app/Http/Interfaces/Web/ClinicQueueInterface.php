<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:45 PM
 */

namespace App\Http\Interfaces\Web;

interface ClinicQueueInterface
{

    /**
     *  get clinic queue using clinic id
     *
     * @param $clinic_id
     * @return mixed
     */
    public function getClinicQueueByClinic($clinic_id);

    /**
     *  update queue to next patient
     *
     * @param $queue
     * @param $patientNumber
     * @return mixed
     */
    public function setQueueToNextReservation($queue, $patientNumber);


    /**
     * @param $queue
     * @return mixed
     */
    public function getClinicQueueByID($queue);

    /**
     *  create new clinicQueue
     *
     * @param $clinic_id
     * @param $queue
     * @return mixed
     */
    public function createQueue($clinic_id, $queue);

    /**
     *  update queue parameter
     *
     * @param $queue
     * @param $key
     * @param $value
     * @return mixed
     */
    public function updateQueue($queue, $key, $value);
}
