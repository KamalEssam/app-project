<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:48 PM
 */

namespace App\Http\Repositories\Web;


use App\Http\Interfaces\Web\PremiumPromoInterface;
use App\Models\PremiumPromoCodes;
use App\Models\PremiumRequest;

class PremiumPromoRepository extends ParentRepository implements PremiumPromoInterface
{
    public $city;

    public function __construct()
    {
        $this->promo = new PremiumPromoCodes();
    }

    /**
     * get promo by id
     *
     * @param $id
     * @return mixed
     */
    public function getPromoById($id)
    {
        try {
            return $this->promo->find($id);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  create new Promo
     *
     * @param $request
     * @return mixed
     */
    public function createPromo($request)
    {
        return $this->promo->create($request->all());
    }

    /**
     *  update City
     *
     * @param $promo
     * @param $request
     * @return mixed
     */
    public function updatePromo($promo, $request)
    {
        try {
            return $promo->update($request->all());
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
            return PremiumPromoCodes::where($column, $value)->first();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get list of all promos
     *
     * @return mixed
     */
    public function getAllPromos()
    {
        try {
            return $this->promo->orderBy('created_at')->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     * get promo by code
     *
     * @param $code
     * @return mixed
     */
    public function getPromoByCode($code)
    {
        try {
            return $this->promo->where('code', $code)->first();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     * @param $user_id
     * @param $promo_id
     * @return mixed
     */
    public function checkIfUserUsedPromoCode($user_id, $promo_id)
    {
        return PremiumRequest::where('user_id', $user_id)
            ->where('promo_code_id', $promo_id)
            ->where('approval',1)
            ->first();
    }
}
