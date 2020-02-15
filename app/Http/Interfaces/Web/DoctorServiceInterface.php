<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:45 PM
 */

namespace App\Http\Interfaces\Web;

interface DoctorServiceInterface
{

    /**
     *  get speciality by id
     *
     * @param $id
     * @return mixed
     */
    public function getServiceById($id);

    /**
     *  create new speciality
     *
     * @param $request
     * @return mixed
     */
    public function createService($request);

    /**
     *  update speciality
     *
     * @param $service
     * @param $request
     * @return mixed
     */
    public function updateService($service, $request);

    /**
     *  get all services by id
     *
     * @param $account_id
     * @return mixed
     */
    public function getDoctorServices($account_id);

    /**
     * @param $id
     * @return mixed
     */
    public function getDoctorServiceByIdForEditing($id);
}
