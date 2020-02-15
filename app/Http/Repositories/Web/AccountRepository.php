<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:48 PM
 */

namespace App\Http\Repositories\Web;


use App\Http\Controllers\WebController;
use App\Http\Interfaces\Web\AccountInterface;
use App\Http\Traits\DateTrait;
use App\Models\Account;
use DB;

class AccountRepository extends ParentRepository implements AccountInterface
{
    use DateTrait;

    public $account;

    public function __construct()
    {
        $this->account = new Account();
    }

    /**
     *  get the accounts count
     *
     * @return mixed
     */
    public static function getAccountsCount()
    {
        return Account::join('users', function ($join) {
            $join->on('accounts.id', '=', 'users.account_id');
        })
            ->where('users.role_id', '=', WebController::ROLE_DOCTOR)
            ->where(function ($query) {
                // in case of debug mode Dont show Test Users
                if (debug_mode() == true) {
                    $query->whereNotIn('users.id', get_test_users('doctor'));
                }
            })->count();
    }

    /**
     *  get all the accounts ordered by  ( created_by )
     *
     * @param $type
     * @return mixed
     */
    public function getAllAccountsOrdered($type)
    {
        try {
            return $this->account
                ->join('users', function ($join) {
                    $join->on('accounts.id', '=', 'users.account_id');
                    $join->where('users.role_id', '=', WebController::ROLE_DOCTOR);
                })
                ->where(function ($query) use ($type) {
                    switch ($type) {
                        case 1:
                            $query->where('users.is_active', '!=', 1);
                            break;
                        case 2:
                            $query->where('accounts.is_published', '!=', 1);
                            break;
                        default:
                            break;
                    }
                })
                ->where(function ($query) {
                    // in case of debug mode Dont show Test Users
                    if (debug_mode() == true) {
                        $query->whereNotIn('users.id', get_test_users('doctor'));
                    }
                })
                ->orderBy('accounts.created_at')
                ->select(
                    'accounts.id as id',
                    'users.image as image',
                    'users.id as user_id',
                    'users.name as name',
                    'accounts.' . app()->getLocale() . '_name as account_name',
                    'accounts.unique_id as unique_id',
                    'users.mobile as mobile',
                    'accounts.is_published as is_published',
                    'users.is_active as is_active',
                    'users.is_premium',
                    'users.email'
                )
                ->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }


    /**
     *  create new account
     *
     * @param $request
     * @return mixed
     * @throws \Exception
     */
    public function createAccount($request)
    {
        DB::beginTransaction();
        try {
            $account = $this->account->create($request->all());
        } catch (\Exception $e) {
            DB::rollBack();
            self::logErr($e->getMessage());
            return false;
        }
        DB::commit();
        return $account;
    }

    /**
     *  get account by id
     *
     * @param $id
     * @return mixed
     */
    public static function getAccountById($id)
    {
        try {
            return Account::find($id);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     * update account data
     *
     * @param $account
     * @param $plan_id
     * @param $days
     * @param $price_of_day
     * @param $auth_id
     * @return mixed
     * @throws \Exception
     */
    public function updateAccount($account, $plan_id, $days, $price_of_day, $auth_id)
    {
        DB::beginTransaction();
        try {
            $account->plan_id = $plan_id;
            $account->updated_by = $auth_id;
            $account->due_amount = $days * $price_of_day;
            $account->due_date = self::addDays(self::getToday(), $days);
            $account->update();
        } catch (\Exception $e) {
            DB::rollBack();
            self::logErr($e->getMessage());
            return false;
        }
        DB::commit();
        return $account;
    }

    /**
     * @param $account
     * @param $attribute
     * @param $value
     * @return bool
     * @throws \Exception
     */
    public function update($account, $attribute, $value = '')
    {
        DB::beginTransaction();
        try {
            if (is_array($attribute)) {
                foreach ($attribute as $name => $val) {
                    $account->{$name} = $val;
                }
            } else {
                $account->{$attribute} = $value;
            }
            $account->update();
        } catch (\Exception $e) {
            DB::rollBack();
            self::logErr($e->getMessage());
            return false;
        }
        DB::commit();
        return $account;
    }
}
