<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:48 PM
 */

namespace App\Http\Repositories\Web;


use App\Http\Interfaces\Web\OfferInterface;
use App\Models\Offers;
use Illuminate\Database\Eloquent\Collection;

class OfferRepository extends ParentRepository implements OfferInterface
{
    public $offer;

    public function __construct()
    {
        $this->offer = new Offers();
    }

    /**
     * get list of all offers
     *
     * @return mixed
     */
    public function getAllOffers()
    {
        try {
            return $this->offer::with('category:id,' . app()->getLocale() . '_name as name')->orderBy('created_at')->get();
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
    public function getOfferById($id)
    {
        try {
            return $this->offer->where('id', $id)->select(
                'id',
                'category_id',
                app()->getLocale() . '_name as name',
                app()->getLocale() . '_desc as desc',
                'doctor_id',
                'reservation_fees_included',
                'old_price',
                'price',
                'views_no',
                'users_booked',
                'is_featured',
                'image',
                'expiry_date'
            )->first();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    public function getWebOfferById($id)
    {
        try {
            return $this->offer::with('services')->find($id);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     * get offer for api
     *
     * @param $id
     * @return mixed
     */
    public function ApiGetOfferById($id)
    {
        try {
            return $this->offer->join('users', 'users.id', 'offers.doctor_id')
                ->join('accounts', 'accounts.id', 'users.account_id')
                ->whereDate('offers.expiry_date', '>=', now()->format('Y-m-d'))
                ->where('offers.id', $id)
                ->select(
                    'offers.id as id',
                    'offers.' . app()->getLocale() . '_name as name',
                    'offers.' . app()->getLocale() . '_desc as desc',
                    'offers.is_featured',
                    'offers.image',
                    'offers.reservation_fees_included',
                    'offers.price',
                    'offers.old_price',
                    'offers.users_booked',
                    'offers.doctor_id as doctor_id',
                    'accounts.' . app()->getLocale() . '_name as doctor_name'
                )->first();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return null;
        }
    }


    /**
     *  create new offer category
     *
     * @param $request
     * @return mixed
     */
    public function createOffer($request)
    {
        try {
            return $this->offer->create($request->all());
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  update offer category
     *
     * @param $offer
     * @param $request
     * @return mixed
     */
    public function updateOffer($offer, $request)
    {
        try {
            return $offer->update($request->all());
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
            return Offers::where($column, $value)->first();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get offers for the api
     *
     * @param $is_featured
     * @param null $request
     * @return mixed
     */
    public function ApiGetOffers($is_featured, $request = null)
    {
        $offset = (isset($request->offset) && !empty($request->offset)) ? $request->offset : 0;
        $limit = (isset($request->limit) && !empty($request->limit)) ? $request->limit : 10;

        $orderBy = ['offers.is_featured', 'desc'];
        if ($request->has('most_viewed') && $request->get('most_viewed') == 1) {
            $orderBy = ['offers.views_no', 'desc'];
        }

        try {
            return $this->offer->join('users', 'users.id', 'offers.doctor_id')
                ->join('accounts', 'accounts.id', 'users.account_id')
                ->join('doctor_details', 'accounts.id', 'doctor_details.account_id')
                ->where(function ($query) use ($is_featured, $request) {
                    if ($is_featured == 1) {
                        $query->where('is_featured', 1);
                    }
                    if ($request->has('category_id')) {
                        $query->where('offers.category_id', $request->get('category_id'));
                    }
                })
                ->whereDate('offers.expiry_date', '>=', now()->format('Y-m-d'))
                ->select(
                    'offers.id as id',
                    'offers.' . app()->getLocale() . '_name as name',
                    'offers.' . app()->getLocale() . '_desc as desc',
                    'offers.is_featured',
                    'offers.image',
                    'offers.reservation_fees_included',
                    'offers.price',
                    'offers.category_id',
                    'offers.old_price',
                    'offers.users_booked',
                    'offers.doctor_id as doctor_id',
                    'accounts.' . app()->getLocale() . '_name as doctor_name',
                    'offers.expiry_date',
                    'offers.created_at',
                    'offers.views_no'
                )
                ->orderBy($orderBy[0], $orderBy[1])
                ->orderBy('doctor_details.featured_rank', 'desc')
                ->orderBy('offers.created_at', 'desc')
                ->offset($offset)
                ->limit($limit)
                ->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get one offer for the api
     *
     * @param $offer_id
     * @return mixed
     */
    public function ApiGetOneOffer($offer_id)
    {
        try {
            return $this->offer->join('users', 'users.id', 'offers.doctor_id')
                ->join('accounts', 'accounts.id', 'users.account_id')
                ->join('doctor_details', 'accounts.id', 'doctor_details.account_id')
                ->where('offers.id', $offer_id)
                ->whereDate('offers.expiry_date', '>=', now()->format('Y-m-d'))
                ->select(
                    'offers.id as id',
                    'offers.' . app()->getLocale() . '_name as name',
                    'offers.' . app()->getLocale() . '_desc as desc',
                    'offers.is_featured',
                    'offers.image',
                    'offers.reservation_fees_included',
                    'offers.price',
                    'offers.category_id',
                    'offers.old_price',
                    'offers.users_booked',
                    'offers.doctor_id as doctor_id',
                    'accounts.' . app()->getLocale() . '_name as doctor_name',
                    'offers.expiry_date'
                )
                ->first();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  increase
     *
     * @param $offer_id
     * @return mixed
     */
    public function ApiIncreaseOffersViews($offer_id)
    {
        try {
            return $this->offer->find($offer_id)->increment('views_no');
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  increase
     *
     * @param $offer_id
     * @return mixed
     */
    public function ApiIncreaseOffersUsage($offer_id)
    {
        try {
            return $this->offer->find($offer_id)->increment('users_booked');
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get Api Offers
     *
     * @return bool
     */
    public function getApiOffers()
    {
        try {
            return $this
                ->offer
                ->whereDate('offers.expiry_date', '>=', now()->format('Y-m-d'))
                ->pluck(app()->getLocale() . '_name as name', 'id');
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }
}
