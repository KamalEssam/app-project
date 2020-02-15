<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 2/12/19
 * Time: 5:26 PM
 */

namespace App\Http\Repositories\Api;

use DB;

class GetReservationFeesAfterReservation
{
    private $currency;
    private $payment;
    public $result;

    private $subtotal_fees;
    private $discount;
    private $total_fees;
    private $offer;
    private $promo;
    private $services;
    private $transactionId = 0;
    private $cashBack = 0;

    /**
     *  constructor
     *
     * GetReservationFeesAfterReservation constructor.
     * @param $payment
     */
    public function __construct($payment)
    {
        $this->currency = trans('lang.currency_type');
        $this->payment = $payment;
        $this->result = new \stdClass();
    }

    public function getResult()
    {
        // calculate fees
        return $this->getSubTotal()
            ->getDiscount()
            ->getServices()
            ->getTransactionId()
            ->getOfferMoney()
            ->getPromoMoney()
            ->getTotal();
        // return the total
    }

    /**
     *  get reservation transaction id
     *
     * @return $this
     */
    public function getTransactionId()
    {
        if ($this->payment->reservation_id) {
            $reservation = DB::table('reservations')->find($this->payment->reservation_id);
            if ($reservation) {
                $this->transactionId = $reservation->transaction_id > 1 ? $reservation->transaction_id : 0;
            } else {
                $this->transactionId = 0;
            }

        }
        return $this;
    }

    public function getCashBack()
    {
        if ($this->payment->reservation_id) {
            $cashBack = DB::table('cash_back')->where('reservation_id', $this->payment->reservation_id)->first();
            if ($cashBack) {
                $this->cashBack = $reservation->patient_cash ?? 0;
            } else {
                $this->cashBack = 0;
            }
        }
        return $this;
    }

    /**
     *  get subTotal
     *
     * @return $this
     */
    public function getSubTotal()
    {
        $this->subtotal_fees = $this->payment->fees ?? 0;
        return $this;
    }

    /**
     *  get discount money
     *
     * @return $this
     */
    public function getDiscount()
    {
        $this->discount = $this->payment->discount ?? 0;
        return $this;
    }


    /**
     *  get list of services
     *
     * @return $this
     */
    public function getServices()
    {
        // get services
        $services = DB::table('reservation_services')
            ->where('reservation_id', $this->payment->reservation_id)
            ->select(app()->getLocale() . '_name as name', 'price')
            ->get()->toArray();

        $this->services = $services ?? [];
        return $this;
    }

    /**
     *  get offer amount of money
     *
     * @return $this
     */
    public function getOfferMoney()
    {
        $this->offer = $this->payment->offer ?? 0;
        return $this;
    }

    public function getPromoMoney()
    {
        $this->promo = $this->payment->promo ?? 0;
        return $this;
    }

    /**
     * @return \stdClass
     */
    public function getTotal(): \stdClass
    {
        $this->result->subtotal_fees = $this->subtotal_fees;
        $this->result->offer = $this->offer ?? 0;
        $this->result->promo = $this->promo;
        $this->result->discount = $this->discount;
        $this->result->currency = $this->currency;
        $this->result->services = $this->services;
        $this->result->total_fees = $this->payment->total;
        $this->result->transaction_id = $this->transactionId;
        $this->result->cash_back = $this->cashBack;
        return $this->result;
    }
}
