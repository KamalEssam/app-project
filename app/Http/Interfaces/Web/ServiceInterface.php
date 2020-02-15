<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:45 PM
 */

namespace App\Http\Interfaces\Web;

interface ServiceInterface
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
     * @return mixed
     */
    public function getServices();

    /**
     * @param $data
     * @return mixed
     */
    public function insertServices($data);


    /**
     * @return mixed
     */
    public function getArrayOfServices();

    /**
     *  get all services by id
     *
     * @return mixed
     */
    public function apiGetServices();
}
