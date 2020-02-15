<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:48 PM
 */

namespace App\Http\Repositories\Web;

use App\Http\Interfaces\Web\CountryInterface;
use App\Models\Country;
use Illuminate\Database\Eloquent\Collection;

class CountryRepository extends ParentRepository implements CountryInterface
{
    public $country;

    public function __construct()
    {
        $this->country = new Country();
    }

    /**
     * get list of all countries
     *
     * @return mixed
     */
    public function getAllCountries()
    {
        try {
            return $this->country->orderBy('created_at')->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return new Collection();
        }
    }

    /**
     * get country by id
     *
     * @param $id
     * @return mixed
     */
    public function getCountryById($id)
    {
        try {
            return Country::find($id);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  create new Country
     *
     * @param $request
     * @return mixed
     */
    public function createCountry($request)
    {
        try {
            return $this->country->create($request->all());
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  update Country
     *
     * @param $country
     * @param $request
     * @return mixed
     */
    public function updateCountry($country, $request)
    {
        try {
            return $country->update($request->all());
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
            return Country::where($column, $value)->first();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }
}