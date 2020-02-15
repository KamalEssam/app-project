<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:45 PM
 */

namespace App\Http\Interfaces\Web;

interface ProvinceInterface
{

    /**
     * get list of all provinces
     *
     * @return mixed
     */
    public function getAllProvinces();

    /**
     * get first province to set it default to doctors
     * @return mixed
     */
    public function getFirstProvince();

    /**
     * get city by country id
     *
     * @param $id
     * @return mixed
     */
    public function getProvincesByCityId($id);

    /**
     * get city by id
     *
     * @param $id
     * @return mixed
     */
    public function getProvinceById($id);


    /**
     *  create new Country
     *
     * @param $request
     * @return mixed
     */
    public function createProvince($request);

    /**
     *  update City
     *
     * @param $plan
     * @param $request
     * @return mixed
     */
    public function updateProvince($plan, $request);

    /**
     *  get row providing the column and the value
     *  for example (id,,)
     *
     * @param $column
     * @param $value
     * @return mixed
     */
    public static function getRecordByColumn($column, $value);
}