<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:48 PM
 */

namespace App\Http\Repositories\Web;

use App\Models\CashBack;
use DB;
use Illuminate\Database\Eloquent\Collection;

class CashbackRepository extends ParentRepository
{
    public $cash;

    public function __construct()
    {
        $this->cash = new CashBack();
    }

    /**
     *  get CashBack by id
     *
     * @param $id
     * @return mixed
     */
    public function getCashBackById($id)
    {
        try {
            return $this->cash->find($id);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /*
     *  get all cash back by id
     *
     * @return mixed
     */
    public function getCashBacks()
    {
        try {
            return $this
                ->cash
                ->where('account_id', auth()->user()->account_id)
                ->orderBy('created_at', 'desc')// get last requests first
                ->orderBy('is_approved', 'asc')// then order them by approved asc 0 -> 1 as not paid first
                ->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return new Collection();
        }
    }

    /*
 *  get all cash back by id
 *
 * @return mixed
 */
    public function getDoctorCashBacks()
    {
        try {
            return $this
                ->cash
                ->join('doctor_income', 'doctor_income.request_id', 'cash_back.id')
                ->where('doctor_income.account_id', auth()->user()->account_id)
                ->select(
                    DB::raw("(DATE_FORMAT(doctor_income.created_at,'%b')) as month_name"),
                    DB::raw('SUM(doctor_income.income) as doctor_income'),
                    DB::raw('SUM(cash_back.seena_cash) as seena_income'),
                    DB::raw('SUM(cash_back.patient_cash) as patient_income'),
                    DB::raw("(DATE_FORMAT(doctor_income.created_at,'%m')) as month"),
                    DB::raw("(DATE_FORMAT(doctor_income.created_at,'%Y')) as year")
                )
                ->groupBy('month', 'year')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return new Collection();
        }
    }

    /**
     *  update request (Accept - Decline)
     *
     * @param $id
     * @param $value
     * @return bool
     */
    public function updateRequest($id, $value)
    {
        try {
            return $this->cash->where('id', $id)->update([
                'is_approved' => $value
            ]);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }
}
