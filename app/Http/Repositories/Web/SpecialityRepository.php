<?php

/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:48 PM
 */

namespace App\Http\Repositories\Web;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\WebController;
use App\Http\Interfaces\Web\SpecialityInterface;
use App\Http\Traits\DateTrait;
use App\Models\Account;
use App\Models\Speciality;
use DB;
use Illuminate\Database\Eloquent\Collection;

class SpecialityRepository extends ParentRepository implements SpecialityInterface
{
    use DateTrait;
    public $speciality;

    public function __construct()
    {
        $this->speciality = new Speciality();
    }


    /**
     * get first speciality
     *
     * @return mixed
     */
    public static function getFirstSpeciality()
    {
        try {
            return Speciality::first();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get speciality by id
     *
     * @param $id
     * @return mixed
     */
    public static function getSpecialityById($id)
    {
        try {
            return Speciality::find($id);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get speciality by id with locale
     *
     * @param $slug
     * @return mixed
     */
    public function getSpecialityBySlugWithLocale($slug)
    {
        try {
            return $this->speciality
                ->select('id',
                    app()->getLocale() . '_speciality as speciality',
                    'image'
                )
                ->where('slug', $slug)
                ->first();


        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }


    /**
     *  get all specialties
     *
     * @return mixed
     */
    public function getAllSpecialities()
    {
        try {
            return $this->speciality::withCount('doctorDetails as doctors')->orderBy('created_at')->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return new Collection();
        }
    }

    /**
     *  create new speciality
     *
     * @param $request
     * @return mixed
     */
    public function createSpeciality($request)
    {
        try {
            return $this->speciality->create($request->all());
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }

    }

    /**
     *  update speciality
     *
     * @param $speciality
     * @param $request
     * @return mixed
     */
    public function updateSpeciality($speciality, $request)
    {
        try {
            return $speciality->update($request->all());

        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     * get count of featured specialities
     *
     * @param string $id
     * @return mixed
     */
    public function getFeaturedSpecialitiesCount($id = '')
    {
        try {
            return $this->speciality->where('is_featured', 1)
                ->where(function ($query) use ($id) {
                    if ($id != '' && is_numeric($id)) {
                        $query->where('id', '!=', $id);
                    }
                })
                ->count();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  set featured status
     *
     * @param $speciality
     * @param $status
     * @return mixed
     */
    public function setFeatured($speciality, $status)
    {
        try {
            return $speciality->update([
                'is_featured' => $status
            ]);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get list of featured specialities
     *
     * @param int $limit
     * @param $featured
     * @return mixed
     */
    public function getFeaturedSpecialities($limit = 5, $featured)
    {
        try {
            $specialities = $this->speciality
                ->select('id',
                    app()->getLocale() . '_speciality as speciality',
                    'slug',
                    'image'
                )->withCount(['offerCategories as categories', 'offers as offers' => function ($query) {
                    $query->whereDate('offers.expiry_date', '>=', now()->format('Y-m-d'));
                }])
                ->where(function ($query) use ($featured, $limit) {
                    if ($featured == 1) {
                        $query->where('is_featured', 1);
                        $query->limit($limit);
                    }
                })
                ->get();
            if ($specialities->count() > 0) {
                foreach ($specialities as $speciality) {
                    $doctors = $this->getDoctorsBySpecialitySlug($speciality->slug);
                    $speciality->doctors = $doctors;
                }
            }
            return $specialities;
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     * get all doctors belong to this speciality
     * @param $slug
     * @return mixed
     */
    public function getDoctorsBySpecialitySlug($slug)
    {
        try {
            return Account::join('users', 'accounts.id', 'users.account_id')
                ->join('doctor_details', 'accounts.id', 'doctor_details.account_id')
                ->join('specialities', 'specialities.id', 'doctor_details.speciality_id')
                ->where('accounts.is_published', ApiController::TRUE)
                ->where('accounts.due_date', '>', self::getDateByFormat(self::getToday(), 'Y-m-d'))
                ->where('specialities.slug', $slug)
                ->where('users.role_id', WebController::ROLE_DOCTOR)
                ->select('accounts.' . app()->getLocale() . '_name as account_name', 'doctor_details.min_fees', DB::raw('IF(users.image = "default.png",CONCAT("' . asset('assets/images/') . '","/", users.image), CONCAT("' . asset('assets/images/profiles/') . '/",users.unique_id,"/", users.image)) as image'), 'users.id', 'specialities.' . app()->getLocale() . '_speciality as speciality', 'doctor_details.' . app()->getLocale() . '_bio as bio')
                ->withCount('myRecommends')
                ->withCount('usersWhoFavouriteMe as followers')
                ->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get list of specialities with sponsored doctors
     *
     * @return mixed
     */
    public function getAllSpecialitiesWithSponsored()
    {
        try {
            return $this->speciality
                ->select(
                    'id',
                    'en_speciality',
                    'ar_speciality',
                    'image',
                    'created_by',
                    'updated_by',
                    'is_featured'
                )
                ->withCount('sponseredDoctors as doctors')
                ->orderBy('created_at')
                ->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return new Collection();
        }
    }

    /**
     *  get specific specialty with sponsor
     *
     * @param $id
     * @return mixed
     */
    public function getSpecialityWithSponsord($id)
    {
        try {
            return $this->speciality
                ->select(
                    'id',
                    'en_speciality',
                    'ar_speciality',
                    'image',
                    'created_by',
                    'updated_by',
                    'is_featured'
                )
                ->where('id', $id)
                ->withCount('sponseredDoctors as doctors')
                ->orderBy('created_at')
                ->first();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    public function getSponsoredDoctorsBySpecialityId($id)
    {
        try {
            return Account::join('doctor_details', 'doctor_details.account_id', 'accounts.id')
                ->join('users', 'users.account_id', 'accounts.id')
                ->where('users.role_id', 1)
                ->where('doctor_details.speciality_id', $id)
                ->where('doctor_details.featured_rank', '>', 0)
                ->select(
                    'accounts.id as id',
                    'accounts.' . app()->getLocale() . '_name as name',
                    'doctor_details.featured_rank'
                )
                ->orderBy('doctor_details.featured_rank', 'desc')
                ->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  remove sponsor doctor using account_id
     *
     * @param $account_id
     * @return bool
     */
    public function removeSponsorDoctor($account_id)
    {
        try {
            (new DoctorDetailsRepository())->updateColumn($account_id, 'featured_rank', 0);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }

        return true;
    }
}
