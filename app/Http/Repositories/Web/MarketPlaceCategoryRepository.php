<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:48 PM
 */

namespace App\Http\Repositories\Web;


use App\Http\Interfaces\Web\MarketPlaceCategoryInterface;
use App\Models\MarketPlaceCategory;

class MarketPlaceCategoryRepository extends ParentRepository implements MarketPlaceCategoryInterface
{
    public $marketPlaceCategory;

    public function __construct()
    {
        $this->marketPlaceCategory = new MarketPlaceCategory();
    }

    /**
     * get list of all categories
     *
     * @return mixed
     */
    public function getAllCategories()
    {
        try {
            return $this->marketPlaceCategory::all();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     * get category by id
     *
     * @param $id
     * @return mixed
     */
    public function getCategoryById($id)
    {
        try {
            return $this->marketPlaceCategory->find($id);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  create new category
     *
     * @param $request
     * @return mixed
     */
    public function createCategory($request)
    {
        try {
            return $this->marketPlaceCategory->create($request->all());
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  update category
     *
     * @param $category
     * @param $request
     * @return mixed
     */
    public function updateCategory($category, $request)
    {
        try {
            return $category->update($request->all());
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get list of categories
     *
     * @return mixed
     */
    public function getCategoriesList()
    {
        try {
            return $this->marketPlaceCategory->pluck(app()->getLocale() . '_name', 'id');
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *
     *  api categories
     *
     * @return mixed
     */
    public function getApiCategories()
    {
        try {
            return $this->marketPlaceCategory->active()->select('id', app()->getLocale() . '_name as name', 'image')->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }
}
