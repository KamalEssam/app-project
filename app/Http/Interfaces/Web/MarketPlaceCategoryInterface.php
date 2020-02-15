<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:45 PM
 */

namespace App\Http\Interfaces\Web;

interface MarketPlaceCategoryInterface
{

    /**
     * get list of all categories
     *
     * @return mixed
     */
    public function getAllCategories();

    /**
     * get category by id
     *
     * @param $id
     * @return mixed
     */
    public function getCategoryById($id);


    /**
     *  create new category
     *
     * @param $request
     * @return mixed
     */
    public function createCategory($request);

    /**
     *  update category
     *
     * @param $category
     * @param $request
     * @return mixed
     */
    public function updateCategory($category, $request);

    /**
     *  get list of categories
     *
     * @return mixed
     */
    public function getCategoriesList();


    /**
     *
     *  api categories
     *
     * @return mixed
     */
    public function getApiCategories();
}
