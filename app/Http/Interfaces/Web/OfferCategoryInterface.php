<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:45 PM
 */

namespace App\Http\Interfaces\Web;

interface OfferCategoryInterface
{

    /**
     * get list of all categories
     *
     * @return mixed
     */
    public function getAllCategories();


    /**
     * get offer category by id
     *
     * @param $id
     * @return mixed
     */
    public function getOfferCategoryById($id);

    /**
     *  create new offer category
     *
     * @param $request
     * @return mixed
     */
    public function createOfferCategory($request);

    /**
     *  update offer category
     *
     * @param $offerCategory
     * @param $request
     * @return mixed
     */
    public function updateOfferCategory($offerCategory, $request);

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
     *  get array of offer categories
     *
     * @return mixed
     */
    public function getArrayOfOfferCategories();
}
