<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:48 PM
 */

namespace App\Http\Repositories\Web;


use App\Http\Interfaces\Web\CityInterface;
use App\Models\City;

class CityRepository extends ParentRepository implements CityInterface
{
    public $city;

    public function __construct()
    {
        $this->city = new City();
    }

    /**
     * get list of all cities
     *
     * @return mixed
     */
    public function getAllCities()
    {
        try {
            return $this->city->orderBy('created_at')->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     * get city by country id
     *
     * @param $id
     * @return mixed
     */
    public function getCitiesByCountryId($id)
    {
        try {
            return $this->city->where('country_id', $id)->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  create new City
     *
     * @param $request
     * @return mixed
     */
    public function createCity($request)
    {
        try {
            return $this->city->create($request->all());
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  update City
     *
     * @param $city
     * @param $request
     * @return mixed
     */
    public function updateCity($city, $request)
    {
        try {
            return $city->update($request->all());
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get row providing the column and the value
     *  for example (id,,)
     *
     * @param $column
     * @param $value
     * @return mixed
     */
    public static function getRecordByColumn($column, $value)
    {
        try {
            return City::where($column, $value)->first();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     * get city by id
     *
     * @param $id
     * @return mixed
     */
    public function getCityById($id)
    {
        try {
            return $this->city->find($id);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     * get first city to set it default to doctors
     * @return mixed
     */
    public function getFirstCity()
    {
        try {
            return City::first();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get list of cities and provinces
     *
     * @return mixed
     */
    public function getCitiesWithProvinces()
    {
        try {
            return $this->city::with(array('provinces' => function ($query) {
                $query->select('id', 'city_id', app()->getLocale() . '_name as name');
            }))
                ->select('id', app()->getLocale() . '_name as name')
                ->get()
                ->reject(function ($value, $key) {
                    // remove the cities that dont have provinces
                    if (count($value->provinces) == 0) {
                        return true;
                    }
                    return false;
                })->values();

        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }
}
