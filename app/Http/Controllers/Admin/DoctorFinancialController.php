<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\WebController;
use DB;

class DoctorFinancialController extends WebController
{

    const CASH_METHOD = 0;
    const CASHBACK_METHOD = 1;
    const INSTALLMENT_METHOD = 2;

    private $year,
        $month;

    public $response;

    public function __construct()
    {

        $this->year = request('year') ?? now()->format('Y');
        $this->month = request('month') ?? now()->format('m');

        $this->response = new \stdClass();

        $this->response->total_reservations = 0;
        $this->response->is_paid = false;

        $this->response->cash_reservations = 0;
        $this->response->cash_reservations_paid = 0;

        $this->response->online_reservations = 0;
        $this->response->online_reservations_paid = 0;

        $this->response->expected_cash_income = 0;
        $this->response->actual_cash_income = 0;

        $this->response->expected_online_income = 0;
        $this->response->actual_online_income = 0;

        $this->response->seena_income = 0;
    }


    /**
     *  show list of all ads
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {

        $this->response->is_paid = $this->getDoctorPaymentStatus();

        $this->reservationsNumbers()
            ->getTotalPaidCashReservations()
            ->getTotalPaidOnlineReservations()
            ->getTotalExpectedCashIncome()
            ->getTotalActualCashIncome()
            ->getTotalSeenaIncome()
            ->getTotalExpectedOnlineIncome()
            ->getTotalActualOnlineIncome();


        return view('admin.doctor.financial.index', ['results' => $this->response]);
    }

    /**
     *  get the number of reservations that is paid online and paid in cash
     *
     */
    private function reservationsNumbers()
    {
        $reservations = DB::table('reservations')
            ->whereIn('clinic_id', DB::table('clinics')->where('account_id', auth()->user()->account_id)->pluck('id'))
            ->where(DB::raw("(DATE_FORMAT(reservations.created_at,'%Y'))"), $this->year)
            ->where(DB::raw("(DATE_FORMAT(reservations.created_at,'%m'))"), $this->month)
            ->select('payment_method', DB::raw('COUNT(id) as reservations'))
            ->groupBy('payment_method')
            ->get();

        foreach ($reservations as $reservation) {
            if ($reservation->payment_method === self::CASH_METHOD) {
                $this->response->cash_reservations += $reservation->reservations ?? 0;
            } else {
                $this->response->online_reservations += $reservation->reservations ?? 0;
            }
            $this->response->total_reservations += $reservation->reservations ?? 0;
        }

        return $this;
    }

    /**
     *  get the paid cash reservations total number
     *
     */
    private function getTotalPaidCashReservations()
    {
        $reservations = DB::table('reservations')
            ->whereIn('clinic_id', DB::table('clinics')->where('account_id', auth()->user()->account_id)->pluck('id'))
            ->where(DB::raw("(DATE_FORMAT(day,'%Y'))"), $this->year)
            ->where(DB::raw("(DATE_FORMAT(day,'%m'))"), $this->month)
            ->where('payment_method', self::CASH_METHOD)
            ->where('status', self::R_STATUS_ATTENDED)
            ->where('transaction_id', -2)
            ->count();
        $this->response->cash_reservations_paid = $reservations ?? 0;

        return $this;
    }

    /**
     *  get total paid online reservations
     *
     */
    private function getTotalPaidOnlineReservations()
    {
        $reservations = DB::table('reservations')
            ->whereIn('clinic_id', DB::table('clinics')->where('account_id', auth()->user()->account_id)->pluck('id'))
            ->where(DB::raw("(DATE_FORMAT(day,'%Y'))"), $this->year)
            ->where(DB::raw("(DATE_FORMAT(day,'%m'))"), $this->month)
            ->where('payment_method', '!=', self::CASH_METHOD)
            ->where('status', self::R_STATUS_ATTENDED)
            ->where('transaction_id', '>', 1)
            ->count();
        $this->response->online_reservations_paid = $reservations;
        return $this;
    }

    /**
     *  the total expected number of paid in cash reservations
     *
     */
    private function getTotalExpectedCashIncome()
    {
        $reservations = DB::table('reservations')
            ->join('reservations_payment', 'reservations.id', 'reservations_payment.reservation_id')
            ->whereIn('reservations.clinic_id', DB::table('clinics')->where('account_id', auth()->user()->account_id)->pluck('id'))
            ->where(DB::raw("(DATE_FORMAT(reservations.day,'%Y'))"), $this->year)
            ->where(DB::raw("(DATE_FORMAT(reservations.day,'%m'))"), $this->month)
            ->whereIn('reservations.status', [self::R_STATUS_ATTENDED, self::R_STATUS_APPROVED, self::R_STATUS_MISSED])
            ->where('reservations.payment_method', '=', self::CASH_METHOD)
            ->select(DB::raw('SUM(reservations_payment.total) as total'))
            ->first();

        $this->response->expected_cash_income = $reservations->total ?? null;
        return $this;
    }

    /**
     *  the total Actual number of paid in cash reservations
     *
     */
    private function getTotalActualCashIncome()
    {
        $reservations = DB::table('reservations')
            ->join('reservations_payment', 'reservations.id', 'reservations_payment.reservation_id')
            ->whereIn('reservations.clinic_id', DB::table('clinics')->where('account_id', auth()->user()->account_id)->pluck('id'))
            ->where(DB::raw("(DATE_FORMAT(reservations.day,'%Y'))"), $this->year)
            ->where(DB::raw("(DATE_FORMAT(reservations.day,'%m'))"), $this->month)
            ->where('reservations.payment_method', '=', self::CASH_METHOD)
            ->where('status', self::R_STATUS_ATTENDED)
            ->where('transaction_id', -2)
            ->select(DB::raw('SUM(reservations_payment.total) as total'))
            ->first();

        $this->response->actual_cash_income += $reservations->total ?? 0;
        return $this;
    }


    /**
     *  the total expected number of paid in online reservations
     *
     */
    private function getTotalExpectedOnlineIncome()
    {
        $reservations = DB::table('reservations')
            ->join('reservations_payment', 'reservations.id', 'reservations_payment.reservation_id')
            ->whereIn('reservations.clinic_id', DB::table('clinics')->where('account_id', auth()->user()->account_id)->pluck('id'))
            ->where(DB::raw("(DATE_FORMAT(reservations.day,'%Y'))"), $this->year)
            ->where(DB::raw("(DATE_FORMAT(reservations.day,'%m'))"), $this->month)
            ->whereIn('reservations.status', [self::R_STATUS_ATTENDED, self::R_STATUS_APPROVED, self::R_STATUS_MISSED])
            ->where('reservations.payment_method', '!=', self::CASH_METHOD)
            ->where('transaction_id', '>', 1)
            ->select(DB::raw('SUM(reservations_payment.total) as total'))
            ->first();

        $this->response->expected_online_income = $reservations->total ?? null;
        return $this;
    }

    /**
     *  the total Actual number of paid in online reservations
     *
     */
    private function getTotalActualOnlineIncome()
    {
        $reservations = DB::table('reservations')
            ->join('reservations_payment', 'reservations.id', 'reservations_payment.reservation_id')
            ->whereIn('reservations.clinic_id', DB::table('clinics')->where('account_id', auth()->user()->account_id)->pluck('id'))
            ->where(DB::raw("(DATE_FORMAT(reservations.day,'%Y'))"), $this->year)
            ->where(DB::raw("(DATE_FORMAT(reservations.day,'%m'))"), $this->month)
            ->where('reservations.payment_method', '!=', self::CASH_METHOD)
            ->where('status', self::R_STATUS_ATTENDED)
            ->where('transaction_id', '>', 1)
            ->select(DB::raw('SUM(reservations_payment.total) as total'))
            ->first();

        $this->response->actual_online_income += $reservations->total ?? 0;
        return $this;
    }


    /**
     *  the total Actual number of paid in cash reservations
     *
     */
    private function getTotalSeenaIncome()
    {
        $reservations = DB::table('reservations')
            ->join('reservations_payment', 'reservations.id', 'reservations_payment.reservation_id')
            ->join('cash_back', 'reservations.id', 'cash_back.reservation_id')
            ->whereIn('reservations.clinic_id', DB::table('clinics')->where('account_id', auth()->user()->account_id)->pluck('id'))
            ->where(DB::raw("(DATE_FORMAT(reservations.day,'%Y'))"), $this->year)
            ->where(DB::raw("(DATE_FORMAT(reservations.day,'%m'))"), $this->month)
            ->where('reservations.payment_method', '=', self::CASHBACK_METHOD)
            ->where('status', self::R_STATUS_ATTENDED)
            ->where('transaction_id', '>', 1)
            ->select(
                DB::raw('COUNT(cash_back.id) as number'),
                DB::raw('SUM(cash_back.seena_cash) as seena_income'),
                DB::raw('SUM(cash_back.doctor_cash) as doctor_income'),
                DB::raw('SUM(cash_back.patient_cash) as patients_income')
            )
            ->first();

        $this->response->cash_back = $reservations;
        return $this;
    }

    /**
     *  get payment status
     *
     * @return bool|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     */
    private function getDoctorPaymentStatus()
    {
        return DB::table('doctor_reservation_payments')
                ->where('account_id', auth()->user()->account_id)
                ->where('month', $this->month)
                ->where('year', $this->year)
                ->first() ?? false;
    }
}
