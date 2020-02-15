<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:45 PM
 */

namespace App\Http\Interfaces\Web;

interface CountryInterface
{

    /**
     * get list of all countries
     *
     * @return mixed
     */
    public function getAllCountries();

    /**
     * get country by id
     *
     * @param $id
     * @return mixed
     */
    public function getCountryById($id);


    /**
     *  create new Country
     *
     * @param $request
     * @return mixed
     */
    public function createCountry($request);

    /**
     *  update Country
     *
     * @param $plan
     * @param $request
     * @return mixed
     */
    public function updateCountry($plan, $request);

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