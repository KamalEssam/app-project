<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:48 PM
 */

namespace App\Http\Repositories\Web;


use App\Http\Interfaces\Web\SalesInterface;
use App\Models\LogAccount;

class SalesRepository extends ParentRepository implements SalesInterface
{
    public $sale;

    public function __construct()
    {
        $this->sale = new LogAccount();
    }

    /**
     * get list of all added sales accounts
     *
     * @return mixed
     */
    public function getSalesAddedAccounts()
    {
        return $this->sale->orderBy('created_at', 'desc')->get();
    }

    /**
     * get first city to set it default to doctors
     * @param $user_id
     * @param $filter
     * @return mixed
     */
    public function getCurrentSalesAddedAccounts($user_id, $filter)
    {
        return $this->sale
            ->join('users', 'users.account_id', 'sales_created_accounts.account_id')
            ->join('accounts', 'users.account_id', 'accounts.id')
            ->join('doctor_details', 'doctor_details.account_id', 'accounts.id')
            ->leftJoin('account_service', 'accounts.id', 'account_service.account_id')
            ->where('users.role_id', 1)
            ->where('sales_id', $user_id)
            ->orderBy('users.created_at', 'asc')
            ->groupBy('users.id')
            ->select(
                'users.id as id',
                'accounts.id as account_id',
                'users.email',
                'users.name',
                'users.image',
                'users.is_premium',
                'users.unique_id',
                'users.mobile',
                'accounts.' . app()->getLocale() . '_name as account_name',
                'accounts.is_published',
                'users.created_at',
                'accounts.type',
                'users.login_counter',
                \DB::raw('count(account_service.id)')
            )
            ->where(function ($query) use ($filter) {
                switch ($filter) {
                    case 1:
                        $query->where('users.image', 'default.png');
                        break;
                    case 2:
                        $query->where('doctor_details.ar_bio', '')
                            ->orWhere('doctor_details.ar_bio', null)
                            ->orWhere('doctor_details.en_bio', '')
                            ->orWhere('doctor_details.en_bio', null)
                            ->orWhere('doctor_details.ar_bio', 'لا توجد بيانات للعرض')
                            ->orWhere('doctor_details.en_bio', 'No Data To Show');
                        break;
                    case 3:
                        $query->where('users.login_counter', 0);
                        break;
                    case 4:
                        // no services
                        $query->whereNull('account_service.id');
                        break;
                    default:
                        break;
                }
            })
            ->get();
    }

    /**
     *  get count of all added accounts
     *
     * @return mixed
     */
    public function getSalesAccountCount()
    {
        return $this->sale->where('sales_id', auth()->user()->id)->count();
    }
}
