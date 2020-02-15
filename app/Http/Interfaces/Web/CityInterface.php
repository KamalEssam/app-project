<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:45 PM
 */

namespace App\Http\Interfaces\Web;

interface CityInterface
{

    /**
     * get list of all cities
     *
     * @return mixed
     */
    public function getAllCities();

    /**
     * get first city to set it default to doctors
     * @return mixed
     */
    public function getFirstCity();

    /**
     * get city by country id
     *
     * @param $id
     * @return mixed
     */
    public function getCitiesByCountryId($id);

    /**
     * get city by id
     *
     * @param $id
     * @return mixed
     */
    public function getCityById($id);



    /**
     *  create new Country
     *
     * @param $request
     * @return mixed
     */
    public function createCity($request);

    /**
     *  update City
     *
     * @param $plan
     * @param $request
     * @return mixed
     */
    public function updateCity($plan, $request);

    /**
     *  get row providing the column and the value
     *  for example (id,,)
     *
     * @param $column
     * @param $value
     * @return mixed
     */
    public static function getRecordByColumn($column, $value);

    /**
     *  get list of cities and provinces
     *
     * @return mixed
     */
    public function getCitiesWithProvinces();
}