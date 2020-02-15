<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\WebController;

use DB;
use Illuminate\Http\Request;

class StatisticsController extends WebController
{

    const IS_PUNLISHED = 1;
    const PENDING_PUNLISHED = 2;
    const NOT_PUNLISHED = 0;
    const IS_PREMIUM = 1;
    const NOT_PREMIUM = 0;
    const IS_ACTIVE = 1;
    const NOT_ACTIVE = 0;

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function accountPublishStatistics()
    {
        // get total accounts
        // get published accounts
        $accounts = \DB::table('accounts')
            ->join('users', 'users.account_id', 'accounts.id')
            ->where('users.role_id', self::ROLE_DOCTOR)
            ->where('accounts.is_published', self::IS_PUNLISHED)
            ->where(function ($query) {
                // in case of debug mode Dont show Test Users
                if (debug_mode() == true) {
                    $query->whereNotIn('users.id', get_test_users('doctor'));
                }
            })
            ->unionAll(\DB::table('accounts')
                ->join('users', 'users.account_id', 'accounts.id')
                ->where('users.role_id', self::ROLE_DOCTOR)
                ->whereIn('accounts.is_published', [self::NOT_PUNLISHED, self::PENDING_PUNLISHED])
                ->where(function ($query) {
                    // in case of debug mode Dont show Test Users
                    if (debug_mode() == true) {
                        $query->whereNotIn('users.id', get_test_users('doctor'));
                    }
                })
                ->select(\DB::raw('count(accounts.id) as account')))
            ->select(\DB::raw('count(accounts.id) account'))->get()->toArray();

        $accounts_publish_statistics = array();
        foreach ($accounts as $account) {
            $accounts_publish_statistics[] = $account->account;
        }

        return response()->json(['status' => true, 'account_published' => $accounts_publish_statistics]);
        // get the un-published will be (all - published)
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function accountActiveStatistics()
    {
        // get total accounts
        // get published accounts
        $accounts = \DB::table('accounts')
            ->join('users', 'users.account_id', 'accounts.id')
            ->where('users.role_id', self::ROLE_DOCTOR)
            ->where('users.is_active', self::IS_ACTIVE)
            ->where(function ($query) {
                // in case of debug mode Dont show Test Users
                if (debug_mode() == true) {
                    $query->whereNotIn('users.id', get_test_users('doctor'));
                }
            })
            ->unionAll(\DB::table('accounts')
                ->join('users', 'users.account_id', 'accounts.id')
                ->where('users.role_id', self::ROLE_DOCTOR)
                ->where('users.is_active', self::NOT_ACTIVE)
                ->where(function ($query) {
                    // in case of debug mode Dont show Test Users
                    if (debug_mode() == true) {
                        $query->whereNotIn('users.id', get_test_users('doctor'));
                    }
                })
                ->select(\DB::raw('count(accounts.id) as account')))
            ->select(\DB::raw('count(accounts.id) account'))->get()->toArray();

        $accounts_active_statistics = array();
        foreach ($accounts as $account) {
            $accounts_active_statistics[] = $account->account;
        }

        return response()->json(['status' => true, 'account_active' => $accounts_active_statistics]);
        // get the inactive will be (active - in-active)
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function accountPremiumStatistics()
    {
        // get total accounts
        // get published accounts
        $accounts = \DB::table('accounts')
            ->join('users', 'users.account_id', 'accounts.id')
            ->where('users.role_id', self::ROLE_DOCTOR)
            ->where('users.is_premium', self::IS_PREMIUM)
            ->where(function ($query) {
                // in case of debug mode Dont show Test Users
                if (debug_mode() == true) {
                    $query->whereNotIn('users.id', get_test_users('doctor'));
                }
            })
            ->unionAll(\DB::table('accounts')
                ->join('users', 'users.account_id', 'accounts.id')
                ->where('users.role_id', self::ROLE_DOCTOR)
                ->where('users.is_premium', self::NOT_PREMIUM)
                ->where(function ($query) {
                    // in case of debug mode Dont show Test Users
                    if (debug_mode() == true) {
                        $query->whereNotIn('users.id', get_test_users('doctor'));
                    }
                })
                ->select(\DB::raw('count(accounts.id) as account')))
            ->select(\DB::raw('count(accounts.id) account'))->get()->toArray();

        $accounts_premium_statistics = array();
        foreach ($accounts as $account) {
            $accounts_premium_statistics[] = $account->account;
        }

        return response()->json(['status' => true, 'account_premium' => $accounts_premium_statistics]);
        // get the inactive will be (active - in-active)
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function accountSingleStatistics()
    {
        // get total accounts
        // get published accounts
        $accounts = \DB::table('accounts')
            ->join('users', 'users.account_id', 'accounts.id')
            ->where('users.role_id', self::ROLE_DOCTOR)
            ->where('accounts.type', self::ACCOUNT_TYPE_SINGLE)
            ->where(function ($query) {
                // in case of debug mode Dont show Test Users
                if (debug_mode() == true) {
                    $query->whereNotIn('users.id', get_test_users('doctor'));
                }
            })
            ->unionAll(\DB::table('accounts')
                ->join('users', 'users.account_id', 'accounts.id')
                ->where('users.role_id', self::ROLE_DOCTOR)
                ->where('accounts.type', self::ACCOUNT_TYPE_POLY)
                ->where(function ($query) {
                    // in case of debug mode Dont show Test Users
                    if (debug_mode() == true) {
                        $query->whereNotIn('users.id', get_test_users('doctor'));
                    }
                })
                ->select(\DB::raw('count(accounts.id) as account')))
            ->select(\DB::raw('count(accounts.id) account'))->get()->toArray();

        $accounts_single_statistics = array();
        foreach ($accounts as $account) {
            $accounts_single_statistics[] = $account->account;
        }

        return response()->json(['status' => true, 'account_single' => $accounts_single_statistics]);
        // get the inactive will be (active - in-active)
    }

    /**
     *  get data for registered doctor
     *
     * @param Request $request
     * @return \Illuminate\Support\Collection
     */
    public function registeredAccounts(Request $request)
    {
        $year = $request['year'] ?? now()->format('Y');
        $month = $request['month'] ?? now()->format('m');
        $type = $request['type'] ?? 1;

        // type  => 1 (doctors)
        // type  => 2 (patients)
        // type  => 3 (reservations)

        if (in_array($type, [1, 2])) {
            $graphDate = DB::table('users')
                ->where(DB::raw("(DATE_FORMAT(created_at,'%Y'))"), $year)
                ->where(DB::raw("(DATE_FORMAT(created_at,'%m'))"), $month)
                ->where(function ($query) use ($type) {
                    if ($type == 1) {
                        $query->where('role_id', self::ROLE_DOCTOR);
                    } else {
                        $query->where('role_id', self::ROLE_USER);
                    }
                })
                ->select(DB::raw("(DATE_FORMAT(created_at,'%d')) as day_id"), DB::raw("CONCAT(DATE_FORMAT(created_at,'%a'),'(',DATE_FORMAT(created_at,'%d'),')') as day"), DB::raw('COUNT(*) as count'))
                ->orderBy('day_id')
                ->groupBy('day_id')
                ->get();
        } else {
            $graphDate = DB::table('reservations')
                ->where(DB::raw("(DATE_FORMAT(created_at,'%Y'))"), $year)
                ->where(DB::raw("(DATE_FORMAT(created_at,'%m'))"), $month)
                ->select(DB::raw("(DATE_FORMAT(created_at,'%d')) as day_id"), DB::raw("CONCAT(DATE_FORMAT(created_at,'%a'),'(',DATE_FORMAT(created_at,'%d'),')') as day"), DB::raw('COUNT(*) as count'))
                ->orderBy('day_id')
                ->groupBy('day_id')
                ->get();
        }


        $labels = $graphDate->pluck('day');
        $data = $graphDate->pluck('count');

        return response()->json(['status' => true, 'labels' => $labels, 'data' => $data]);
    }

    /**
     *  get the reservations statistics
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function doctorReservations(Request $request)
    {
        $year = $request['year'] ?? now()->format('Y');
        $month = $request['month'] ?? now()->format('m');
        $clinic_id = $request['clinic'] ?? -1;

        // type  => 1 (doctors)
        // type  => 2 (patients)

        $graphDate = DB::table('reservations')
            ->where(DB::raw("(DATE_FORMAT(created_at,'%Y'))"), $year)
            ->where(DB::raw("(DATE_FORMAT(created_at,'%m'))"), $month)
            ->whereIn('clinic_id',
                DB::table('clinics')
                    ->where('account_id', auth()->user()->account_id)
                    ->where(function ($query) use ($clinic_id) {
                        if ($clinic_id != -1) {
                            $query->where('clinics.id', $clinic_id);
                        }
                    })
                    ->select('id')
                    ->pluck('id')
            )
            ->select(DB::raw("(DATE_FORMAT(created_at,'%d')) as day_id"), DB::raw("CONCAT(DATE_FORMAT(created_at,'%a'),'(',DATE_FORMAT(created_at,'%d'),')') as day"), DB::raw('COUNT(*) as count'))
            ->orderBy('day_id')
            ->groupBy('day_id')
            ->get();

        $labels = $graphDate->pluck('day');
        $data = $graphDate->pluck('count');

        return response()->json(['status' => true, 'labels' => $labels, 'data' => $data]);
    }
}
