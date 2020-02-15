<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:48 PM
 */

namespace App\Http\Repositories\Web;


use App\Http\Interfaces\Web\ProvinceInterface;
use App\Models\Province;

class ProvinceRepository extends ParentRepository implements ProvinceInterface
{
    public $province;

    public function __construct()
    {
        $this->province = new Province();
    }

    /**
     * get list of all cities
     *
     * @return mixed
     */
    public function getAllProvinces()
    {
        try {
            return $this->province->orderBy('created_at')->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     * get province by country id
     *
     * @param $id
     * @return mixed
     */
    public function getProvincesByCityId($id)
    {
        try {
            return $this->province->where('city_id', $id)->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  create new Province
     *
     * @param $request
     * @return mixed
     */
    public function createProvince($request)
    {
        try {
            return $this->province->create($request->all());
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  update Province
     *
     * @param $province
     * @param $request
     * @return mixed
     */
    public function updateProvince($province, $request)
    {
        try {
            return $province->update($request->all());
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
            return Province::where($column, $value)->first();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     * get province by id
     *
     * @param $id
     * @return mixed
     */
    public function getProvinceById($id)
    {
        try {
            return $this->province->find($id);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     * get first province to set it default to doctors
     * @return mixed
     */
    public function getFirstProvince()
    {
        try {
            return Province::first();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get provinces for Ajax in Clinic
     *
     * @param $id
     * @return bool
     */
    public function getProvincesByCityIdForAjax($id)
    {
        try {
            return $this->province->where('city_id', $id)->pluck(app()->getLocale() . '_name', 'id')->toArray();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }
}