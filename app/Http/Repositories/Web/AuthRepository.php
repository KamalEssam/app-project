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
use App\Http\Interfaces\Web\AuthInterface;
use App\Models\User;
use DB;

class AuthRepository extends ParentRepository implements AuthInterface
{
    public $user;

    public function __construct()
    {
        $this->user = new User();
    }

    /**
     *  get the normal users count
     * @return mixed
     */
    public static function getUsersCount()
    {
        try {
            return User::where('role_id', ApiController::ROLE_USER)->count();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  create new user in database
     *
     * @param $request
     * @param $active
     * @return mixed
     */
    public function createUSer($request, $active)
    {
        try {
            return $this->user->create([
                'email' => $request->email,
                'name' => $request->name,
                'mobile' => $request->mobile,
                'is_active' => $active,
                'created_by' => isset(auth()->user()->id) ? auth()->user()->id : null,
                'clinic_id' => (isset($request->clinic_id) ? $request->clinic_id : null),
                'password' => (isset($request['password']) && !empty($request['password'])) ? $request['password'] : NULL
            ]);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  create new user in database
     *
     * @param $request
     * @param $active
     * @return mixed
     */
    public function createSale($request, $active)
    {
        try {
            $sale = $this->user->create([
                'email' => $request->email,
                'name' => $request->name,
                'mobile' => $request->mobile,
                'role_id' => WebController::ROLE_RK_SALES,
                'birthday' => $request->birthday,
                'gender' => $request->gender,
                'is_active' => $active,
                'address' => $request->address,
                'password' => (isset($request['password']) && !empty($request['password'])) ? $request['password'] : NULL
            ]);
            return $sale;
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  create new brand
     *
     * @param $request
     * @param $active
     * @return bool
     */
    public function createBrand($request, $active)
    {
        try {
            $brand = $this->user->create([
                'email' => $request->email,
                'name' => $request->name,
                'mobile' => $request->mobile,
                'role_id' => WebController::ROLE_BRAND,
                'is_active' => $active,
                'address' => $request->address,
                'password' => (isset($request['password']) && !empty($request['password'])) ? $request['password'] : NULL
            ]);
            return $brand;
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
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
            self::logErr($e->getMessage());
            return false;
        }
        DB::commit();
        return $user;
    }


    /**
     *  get user by role id and account id
     *
     * @param $role
     * @param $account_id
     * @return mixed
     */
    public static function getUserByRoleAndAccountId($role, $account_id)
    {
        try {
            return User::where('role_id', $role)->where('account_id', $account_id)->first();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get user providing the column and the value
     *  for example (unique_id,id,account_id,,)
     *
     * @param $column
     * @param $value
     * @return mixed
     */
    public static function getUserByColumn($column, $value)
    {
        try {
            return User::where($column, $value)->first();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get user by role_id and user_id
     *
     * @param $role
     * @param $user_id
     * @return mixed
     */
    public function getUserByRoleAndUserId($role, $user_id)
    {
        try {
            return $this->user->where('role_id', $role)->where('id', $user_id)->first();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get all mobiles of patients in the System
     *
     * @return mixed
     */
    public function getAllPatientsMobiles()
    {
        try {
            return $this->user->where('role_id', WebController::ROLE_USER)->pluck('mobile')->toArray();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }


    /**
     *  get all doctors in the System
     *
     * @return mixed
     */
    public function getAllDoctors()
    {
        try {
            return $this->user->where('role_id', WebController::ROLE_DOCTOR)->pluck('name', 'id')->toArray();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get all patients in the System
     *
     * @return mixed
     */
    public function getAllPatients()
    {
        try {
            return $this->user->where('role_id', WebController::ROLE_USER)->pluck('name', 'id')->toArray();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get all patients in the System by email
     *
     * @return mixed
     */
    public function getAllPatientsByEmails()
    {
        try {
            return $this->user->where('role_id', WebController::ROLE_USER)->pluck('email', 'id')->toArray();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get the name of the user using mobile
     *
     * @param $mobile
     * @return mixed
     */
    public function getPatientUsingMobile($mobile)
    {
        try {
            return $this->user->where('mobile', $mobile)->where('role_id', WebController::ROLE_USER)->first();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     * update notification last click
     *
     * @param $user
     * @return mixed
     * @throws \Exception
     */
    public function updateLastNotification($user)
    {
        DB::beginTransaction();
        try {
            $updated_user = $user->update([
                'last_notification_click' => now('Africa/Cairo')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            self::logErr($e->getMessage());
            return false;
        }
        DB::commit();

        return $updated_user;
    }

    public function getSuperAdmin()
    {
        return $this->user->where('role_id', WebController::ROLE_RK_SUPER_ADMIN)->first();
    }

    /**
     *  update user settings
     *
     * @param $user
     * @param $request
     * @return mixed
     */
    public function updateUser($user, $request)
    {
        try {
            return $user->update($request->all());
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     * @param $account_id
     * @param $role_id
     * @return mixed
     */
    public function getUsersUsingAccountAndRole($account_id, $role_id)
    {
        try {
            return $this->user->where('account_id', $account_id)->where('role_id', $role_id)->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  update user settings
     *
     * @param $user
     * @param $value
     * @return mixed
     * @throws \Exception
     */
    public function updateDoctorPremium($user, $value)
    {
        try {
            $user->update(['is_premium' => $value]);
            return $user;
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  update user settings
     *
     * @param $user_id
     * @param $value
     * @param $plan_id
     * @param $months
     * @return mixed
     */
    public function updateUserPremium($user_id, $value, $plan_id, $months)
    {
        try {
            $user = $this->user->where('id', $user_id)->update([
                'is_premium' => $value,
                'user_plan_id' => $plan_id,
                'expiry_date' => $months > 0 ? now()->addMonth($months)->format('Y-m-d') : null
            ]);
            return $user;
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  update user settings
     *
     * @param $user_id
     * @param $column
     * @param $value
     * @return mixed
     */
    public function updateUserPromCode($user_id, $column, $value)
    {
        try {
            $user = $this->user->where('id', $user_id)->update([
                $column => $value,
            ]);
            return $user;
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    public function getSalesAgents()
    {
        try {
            return $this->user->sales()->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    public function getBrands()
    {
        try {
            return $this->user->brand()->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get all brands
     *
     * @return mixed
     */
    public function getAllBrands()
    {
        try {
            return $this->user->brand()->pluck('name', 'id')->toArray();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     * @param $user
     * @param $column
     * @param $value
     * @return bool
     */
    public function updateUserColumn($user, $column, $value)
    {
        try {
            $user->update([$column => $value]);
            return $user;
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }
}
