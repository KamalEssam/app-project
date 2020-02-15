<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:48 PM
 */

namespace App\Http\Repositories\Web;

use App\Http\Interfaces\Web\OfferCategoryInterface;
use App\Models\OffersCategories;
use Illuminate\Support\Collection;

class OfferCategoryRepository extends ParentRepository implements OfferCategoryInterface
{
    public $offerCategory;

    public function __construct()
    {
        $this->offerCategory = new OffersCategories();
    }

    /**
     * get list of all categories
     *
     * @return mixed
     */
    public function getAllCategories()
    {
        try {
            return $this->offerCategory::withCount('offers as offers')->orderBy('created_at')->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     * get offer category by id
     *
     * @param $id
     * @return mixed
     */
    public function getOfferCategoryById($id)
    {
        try {
            return $this->offerCategory->find($id);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  create new offer category
     *
     * @param $request
     * @return mixed
     */
    public function createOfferCategory($request)
    {
        try {
            return $this->offerCategory->create($request->all());
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  update offer category
     *
     * @param $offerCategory
     * @param $request
     * @return mixed
     */
    public function updateOfferCategory($offerCategory, $request)
    {
        try {
            return $offerCategory->update($request->all());
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get row providing the column and the value
     *
     * @param $column
     * @param $value
     * @return mixed
     */
    public static function getRecordByColumn($column, $value)
    {
        try {
            return OffersCategories::where($column, $value)->first();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get array of offer categories
     *
     * @return mixed
     */
    public function getArrayOfOfferCategories()
    {
        try {
            return $this->offerCategory::pluck(app()->getLocale() . '_name as name', 'id')->toArray();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return null;
        }
    }

    /**
     * get the offer categories
     * @param $request
     * @return Collection
     */
    public function offer_categories($request)
    {
        try {
            return $this->offerCategory
                ->where(function ($query) use ($request) {
                    if ($request->has('speciality_id') && $request->get('speciality_id') != -1) {
                        $query->where('speciality_id', $request->get('speciality_id'));
                    }
                })
                ->select(
                    'id', app()->getLocale() . '_name as name', 'image'
                )->withCount(['offers as offers' => function ($query) {
                    $query->whereDate('expiry_date', '>=', now()->format('Y-m-d'));
                }])
                ->orderBy('offers', 'desc')
                ->orderBy('offers_categories.created_at', 'desc')
                ->get()
                ->reject(function ($value, $key) {
                    return $value->offers == 0;
                })
                ->values();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return new Collection();
        }
    }
}
