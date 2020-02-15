<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:45 PM
 */

namespace App\Http\Interfaces\Web;

interface PremiumPromoInterface
{

    /**
     *  get list of all promos
     *
     * @return mixed
     */
    public function getAllPromos();

    /**
     * get promo by id
     *
     * @param $id
     * @return mixed
     */
    public function getPromoById($id);

    /**
     *  create new Promo
     *
     * @param $request
     * @return mixed
     */
    public function createPromo($request);

    /**
     *  update City
     *
     * @param $promo
     * @param $request
     * @return mixed
     */
    public function updatePromo($promo, $request);

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
