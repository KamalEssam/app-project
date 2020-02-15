<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:48 PM
 */

namespace App\Http\Repositories\Web;

use App\Http\Interfaces\Web\AdInterface;
use App\Models\Ad;
use DB;
use Illuminate\Database\Eloquent\Collection;

class AdRepository extends ParentRepository implements AdInterface
{
    public $ad;

    public function __construct()
    {
        $this->ad = new Ad();
    }

    /**
     *  get Ad by id
     *
     * @param $id
     * @return mixed
     */
    public function getAdById($id)
    {
        try {
            return $this->ad->find($id);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  create new Ad
     *
     * @param $request
     * @return mixed
     */
    public function createAd($request)
    {
        try {
            return $this->ad->create($request->all());
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  update ad
     *
     * @param $ad
     * @param $request
     * @return mixed
     */
    public function updateAd($ad, $request)
    {
        try {
            return $ad->update($request->all());
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get all ads by id
     *
     * @return mixed
     */
    public function getAds()
    {
        try {
            return $this
                ->ad
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return new Collection();
        }
    }

    /**
     *  get slides with latest offers
     *
     * @return mixed
     */
    public function getSlidesWithOffers()
    {
        try {
            return $this->ad
                ::with('doctor')
                ->with('offer:id,' . app()->getLocale() . '_name as name,' . app()->getLocale() . '_desc as desc,doctor_id,reservation_fees_included,price,old_price,is_featured,image,expiry_date,price_type', 'offer.doctor')
                ->where('is_active', 1)// only active
                ->where('date_from', '<=', now()->format('Y-m-d'))
                ->where('date_to', '>=', now()->format('Y-m-d'))
                ->where('time_from', '<=', now()->format('H:i:s'))
                ->where('time_to', '>=', now()->format('H:i:s'))
                ->select(
                    'id',
                    app()->getLocale() . '_title as title',
                    app()->getLocale() . '_desc as desc',
                    'screen_shot',
                    'background',
                    'type',
                    'offer_id',
                    'doctor_id',
                    'time_from',
                    'time_to',
                    'date_from',
                    'date_to',
                    'slide',
                    'created_at',
                    'priority')
                ->orderBy('priority', 'desc')
                ->get()
                ->sortBy('slide')
                ->unique('slide')
                ->values();

        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return new Collection();
        }
    }

    /**
     *  get slides for mobile
     *
     * @return Collection
     */
    public function getSlides()
    {
        try {
            return $this->ad
                ->where('is_active', 1)// only active
                ->where('date_from', '<=', now()->format('Y-m-d'))
                ->where('date_to', '>=', now()->format('Y-m-d'))
                ->where('time_from', '<=', now()->format('H:i:s'))
                ->where('time_to', '>=', now()->format('H:i:s'))
                ->select(
                    'id',
                    'screen_shot',
                    'background',
                    'time_from',
                    'time_to',
                    'date_from',
                    'date_to',
                    'slide',
                    'created_at',
                    'priority')
                ->orderBy('priority', 'desc')
                ->get()
                ->sortBy('slide')
                ->unique('slide')
                ->values();

        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return new Collection();
        }
    }
}
