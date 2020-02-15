<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:48 PM
 */

namespace App\Http\Repositories\Web;


use App\Http\Controllers\WebController;
use App\Http\Interfaces\Web\UserInterface;
use App\Models\Account;
use App\Models\DoctorDetail;
use App\Models\Plan;
use App\Models\Speciality;
use App\Models\User;
use App\Http\Traits\FileTrait;
use DB;

class UserRepository extends ParentRepository implements UserInterface
{
    use FileTrait;

    public $user;

    public function __construct()
    {
        $this->user = new User();
    }


    /**'
     * get profile data by auth user id
     * @param $user_id
     * @return mixed
     */
    public function getUserById($user_id)
    {
        try {
            return $this->user->find($user_id);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     * get doctor settings
     * @param $account_id
     * @return mixed
     */
    public function getDoctorDetailsByAccountId($account_id)
    {
        try {
            return DoctorDetail::where('account_id', $account_id)->first();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     * get doctor account
     * @param $account_id
     * @return mixed
     */
    public function getAccountById($account_id)
    {
        try {
            return Account::find($account_id);

        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     * get doctor plan
     * @param $plan_id
     * @return mixed
     */
    public function getPlanById($plan_id)
    {
        try {
            return Plan::where('id', $plan_id)->first();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     * get doctor speciality
     * @param $speciality_id
     * @return mixed
     */
    public function getSpecialityById($speciality_id)
    {
        try {
            return Speciality::where('id', $speciality_id)->first();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     * get doctor data plus his profile
     * @param $profile
     * @param $method
     * @return mixed
     */
    public function getDoctorAccountData($profile, $method)
    {

        $doctor_details = $this->getDoctorDetailsByAccountId($profile->account_id);
        if (!$doctor_details) {
            abort('500');
        }
        $account = $this->getAccountById($profile->account_id);
        if (!$account) {
            abort('500');
        }

        $profile->en_name = $account['en_name'];
        $profile->ar_name = $account['ar_name'];

        $profile->en_title = $account['en_title'];
        $profile->ar_title = $account['ar_title'];


        $profile->en_address = $account['en_address'];
        $profile->ar_address = $account['ar_address'];

        $profile->sub_specialities = $account->subSpecialities;
        $profile->insurance_companies = $account->insuranceCompanies;


        // lat and lng in case of edit profile
        $profile->lng = $account['lng'];
        $profile->lat = $account['lat'];

        $plan = $this->getPlanById($account->plan_id);
        if (!$plan) {
            abort('500');
        }
        $speciality = $this->getSpecialityById($doctor_details->speciality_id);

        if ($speciality) {
            try {
                if ($method == WebController::METHOD_EDIT) {
                    $profile->speciality_id = $speciality->id;
                }

                $profile->en_speciality = $speciality->en_speciality;
                $profile->ar_speciality = $speciality->ar_speciality;
            } catch (\Exception $e) {
                return WebController::catchExceptions($e->getMessage());
            }
        }

        try {

            $profile->plan_en_name = $plan->en_name;
            $profile->plan_ar_name = $plan->ar_name;
        } catch (\Exception $e) {
            return WebController::catchExceptions($e->getMessage());
        }

        try {
            $profile->en_bio = $doctor_details->en_bio;
            $profile->ar_bio = $doctor_details->ar_bio;

            // get restrict visit
            $profile->restrict_visit = $doctor_details->restrict_visit;

        } catch (\Exception $e) {
            return WebController::catchExceptions($e->getMessage());
        }
        try {
            $profile->due_date = $account->due_date;
            if ($method == WebController::METHOD_INDEX) {
                $profile->due_amount = $account->due_amount;
            };
        } catch (\Exception $e) {
            return WebController::catchExceptions($e->getMessage());
        }

        return $profile;
    }

    /**
     * update profile data
     * @param $profile
     * @param $request
     * @return mixed
     * @throws \Throwable
     */
    public function updateProfileData($profile, $request)
    {
        DB::beginTransaction();
        try {
            $updated_profile = $profile->update($request->except('image'));
        } catch (\Exception $e) {
            DB::rollBack();
            self::logErr($e->getMessage());
            return false;
        }

        DB::commit();
        return $updated_profile;
    }

    /**
     * when update profile update doctor details
     * @param $profile
     * @param $request
     * @return mixed
     * @throws \Exception
     */
    public function updateDoctorDetails($profile, $request)
    {
        $doctor_details = $this->getDoctorDetailsByAccountId($profile->account_id);
        if (!$doctor_details) {
            return false;
        }
        DB::beginTransaction();

        try {
            $doctor_details->en_bio = $request->en_bio;
            $doctor_details->ar_bio = $request->ar_bio;
            $doctor_details->speciality_id = $request->speciality_id;
            if (isset($request->restrict_visit)) {
                $doctor_details->restrict_visit = 1;
            } else {
                $doctor_details->restrict_visit = 0;
            }
            $doctor_details->update();

            // update account
            $account = $this->getAccountById($profile->account_id);

            // update the sub specialities
            $account->subSpecialities()->sync($request->sub_specialities);

            // update the insurance companies
            $account->insuranceCompanies()->sync($request->insurance_companies);

            if ($account) {

                // in case of poly clinic
                if ($account->type == 1) {
                    $account->lat = $request->lat;
                    $account->lng = $request->lng;
                }

                $account->en_name = $request->en_name;
                $account->ar_name = $request->ar_name;

                // Title
                $account->en_title = $request->en_title;
                $account->ar_title = $request->ar_title;

                $account->en_address = $request->en_address;
                $account->ar_address = $request->ar_address;
                $account->update();
            }

        } catch (\Exception $e) {
            DB::rollBack();
            self::logErr($e->getMessage());
            return false;
        }


        if ($account->type == 1) {
            // update all clinics with this account id
            (new ClinicRepository())->updatePolyClinicsWithLatAndLng($account->id, $account->lat, $account->lng);
        }
        DB::commit();
    }

    /**
     * set profile image
     * @param $profile
     * @param $request
     * @return mixed
     * @throws \Exception
     */
    public function setUserImage($profile, $request)
    {
        $old_image = $profile->getOriginal('image');
        $image_path = 'assets/images/profiles/' . $profile->unique_id . '/';
        // Check Path (if not exists => create it)
        if (!file_exists(getcwd() . '/' . $image_path)) {
            mkdir(getcwd() . '/' . $image_path, 0777, true);
        }
        // delete the image
        if ($old_image != 'default.png') {
            $is_deleted = FileTrait::deleteFile($image_path . $old_image);
            if (!$is_deleted) {
                abort('500');
            }
        }
        $image = FileTrait::uploadFile($request->image, $image_path);

        if (!$image) {
            abort('500');
        }
        DB::beginTransaction();
        try {
            $profile->image = $image;
            $profile->update();
        } catch (\Exception $e) {
            DB::rollBack();
            self::logErr($e->getMessage());
            return false;
        }
        DB::commit();
    }

}
