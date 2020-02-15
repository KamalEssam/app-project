<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:48 PM
 */

namespace App\Http\Repositories\Api;


use App\Http\Interfaces\Api\AuthInterface;
use App\Models\Account;
use App\Models\User;
use App\Http\Controllers\ApiController;
use DB;

class AuthRepository implements AuthInterface
{
    protected $user;

    public function __construct()
    {
        $this->user = new User();
    }

    /**
     *  check if user exists using mobile
     * @param $mobile
     * @return mixed
     */
    public function getUserWithMobile($mobile)
    {
        try {
            $user = $this->user->where('mobile', $mobile)->first();
            if (!$user) {
                return false;
            }
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }
        return $user;
    }

    /**
     * @param $email
     * @return null
     */
    public function getUserWithEmail($email)
    {
        try {
            $user = $this->user->where('email', $email)->first();
            if (!$user) {
                return false;
            }
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }
        return $user;
    }

    /**
     * @param $column
     * @param $social_id
     * @return mixed|null
     */
    public function getUserWithSocialId($column, $social_id)
    {
        try {
            $user = $this->user->where($column, $social_id)->first();
            if (!$user) {
                return false;
            }
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }
        return $user;
    }

    /**
     * set user password ( used in case of change password or when user set password first time )
     *
     * @param $user
     * @param $password
     * @return mixed
     * @throws \Exception
     */
    public function setPassword($user, $password)
    {
        DB::beginTransaction();
        try {
            $user->password = $password;
            $user->save();
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
        DB::commit();
        return $user;
    }

    /**
     * create new user
     * @param $request
     * @return mixed
     * @throws \Exception
     */
    public function createUser($request)
    {
        try {
            $user = $this->user->create($request);
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }
        return $user;
    }

    /**
     * update new user
     * @param $user
     * @param $request
     * @return mixed
     */
    public function updateUser($user, $request)
    {
        try {
            $user->update($request->all());
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * update user data after create
     *
     * @param $userCounter
     * @param $user
     * @return mixed
     */
    public function updateAfterCreate($userCounter, $user)
    {
        try {
            $user->unique_id = (999 + $userCounter);
            $user->created_by = $user->id;
            $user->save();
        } catch (\Exception $e) {
            return null;
        }
        return $user;
    }

    /**
     * @param $user
     * @return mixed
     */
    public function createToken($user)
    {
        return $user->createToken('rk-anjel')->accessToken;
    }

    /**
     * @param $request
     * @return bool
     */
    public function attemptLogin($request)
    {
        try {
            $login = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';
            return auth()->attempt([$login => request('login'), 'password' => request('password')]);
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }
    }

    /**
     *  get user data
     *
     * @param $user
     * @return mixed|\stdClass
     */
    public function getUserData($user)
    {
        $the_user = new \stdClass();
        $the_user->id = $user->id;
        $the_user->name = $user->name;
        $the_user->email = $user->email;
        $the_user->gender = $user->gender;
        $the_user->mobile = $user->mobile;
        $the_user->birthday = is_null($user->birthday) ? "Not set" : $user->birthday;
        $the_user->address = $user->address;
        $the_user->image = $user->image;
        $the_user->unique_id = $user->unique_id;
        $the_user->clinic_id = $user->clinic_id;
        $the_user->account_id = $user->account_id;
        $the_user->facebook_id = $user->facebook_id;
        $the_user->google_id = $user->google_id;
        $the_user->height = $user->height;
        $the_user->weight = $user->weight;
        $the_user->is_active = $user->is_active;
        $the_user->is_notification = $user->is_notification;
        $the_user->pin = $user->pin;
        $the_user->role_id = $user->role_id;
        $the_user->is_premium = $user->is_premium ?? '0';
        $the_user->points = $user->points;
        $the_user->cash_back = $user->cash_back;

        return $the_user;
    }

    /**
     *  activate user
     *
     * @param $user
     * @return mixed
     */
    public function activateUser($user)
    {
        try {
            $user->is_active = 1;
            $user->update();
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }
        return $user;
    }


    /**
     * Forget password
     * @param $user
     * @param $new_password
     * @return null
     */
    public function updatePassword($user, $new_password)
    {
        try {
            $user->password = $new_password;
            $user->update();
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }
        return true;
    }

    public function updateColumn($user, $column, $value)
    {
        DB::beginTransaction();
        try {
            $user[$column] = $value;
            $user->update();
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiController::catchExceptions($e->getMessage());
        }
        DB::commit();
        return $user;
    }

    /**
     * @param $account_id
     * @param $role
     * @return mixed
     */
    public function getUserByAccount($account_id, $role)
    {
        return $this->user->where('account_id', $account_id)->where('role_id', $role)->first();
    }

    /**
     * @param $user
     * @param $new_password
     * @return mixed
     */
    public function updateUserPassword($user, $new_password)
    {
        try {
            $user->password = $new_password;
            $user->update();
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }
        return $user;
    }


    /**
     *  get user
     * @param $user_id
     * @return mixed
     */
    public function getUserById($user_id)
    {
        try {
            return $this->user->where('id', $user_id)->first();
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }
    }

    public function getDoctorDateForOffer($doctor_id)
    {
        try {
            $get_doctor_details = Account::join('users', 'accounts.unique_id', 'users.unique_id')
                ->join('doctor_details', 'accounts.id', 'doctor_details.account_id')
                ->leftjoin('account_user', 'accounts.id', 'account_user.account_id')
                ->leftjoin('specialities', 'specialities.id', 'doctor_details.speciality_id')
                ->leftjoin('reviews', 'reviews.account_id', 'accounts.id')
                ->where('users.id', $doctor_id)
                ->where('users.role_id', ApiController::ROLE_DOCTOR)
                ->select('users.unique_id',
                    'accounts.id',
                    'users.id as user_id',
                    'accounts.type as account_type',
                    'accounts.' . app()->getLocale() . '_name as name',
                    'account_user.active',
                    'users.account_id',
                    DB::raw('IF(users.image = "default.png",CONCAT("' . asset('assets/images/') . '","/", users.image), CONCAT("' . asset('assets/images/profiles/') . '/",users.unique_id,"/", users.image)) as image'),
                    'specialities.' . app()->getLocale() . '_speciality as speciality',
                    'doctor_details.' . app()->getLocale() . '_bio as bio',
                    'doctor_details.min_fees',
                    'doctor_details.min_premium_fees',
                    'accounts.is_published',
                    'users.is_premium',
                    'users.name',
                    'users.email',
                    'users.gender',
                    'users.birthday',
                    'users.address',
                    'users.role_id',
                    'users.height',
                    'users.weight',
                    'users.points',
                    'users.cash_back',
                    'doctor_details.featured_rank as sponsored',
                    'accounts.' . app()->getLocale() . '_name as account_name',
                    'accounts.' . app()->getLocale() . '_address as account_address',
                    'accounts.' . app()->getLocale() . '_title as title',
                    DB::raw('TRUNCATE(AVG(reviews.rate),2) as rate')
                )
                ->withCount('reviews as reviews')
                ->first();

            if (!$get_doctor_details) {
                return false;
            }

            $get_doctor_details->id = $get_doctor_details->user_id;
            return $get_doctor_details;
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }
    }

    /**
     *  get user
     * @param $unique_id
     * @return mixed
     */
    public function getUserByUniqueId($unique_id)
    {
        try {
            return $this->user->where('unique_id', $unique_id)->first();
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }
    }

    /**
     * get user by mobile number
     * @param $mobile
     * @return mixed
     */
    public function getUserByMobile($mobile)
    {
        return $this->user->where('mobile', $mobile)->first();
    }


    /**
     * @param $user
     * @param $status
     * @return mixed
     */
    public function updateNotification($user, $status)
    {
        try {
            $user->update(['is_notification' => $status]);
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }
        return $user;
    }

    /**
     * get account
     * @param $account_id
     * @return mixed
     */
    public function getAccountById($account_id)
    {
        try {
            return Account::where('id', $account_id)->first();
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }
    }

    /**
     *  update created by
     * @param $record
     * @param $created_by
     * @return mixed
     */
    public function setCreatedBy($record, $created_by)
    {
        try {
            $record->created_by = $created_by;
            $record->update();
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }
    }

}
