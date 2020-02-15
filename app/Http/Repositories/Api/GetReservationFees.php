<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 2/12/19
 * Time: 5:26 PM
 */

namespace App\Http\Repositories\Api;

class GetReservationFees
{
    private const TYPE_MONEY = 0;
    private const FEES_INCLUDED = 1;
    private const PREMIUM = 1;
    private const VAT_VALUE = 0.14;

    public $currency;
    public $clinic;
    public $account_type;
    public $fees;
    public $premium_fees;
    public $subtotal_fees;
    public $subtotal_fees_actual;
    public $total_fees;
    public $offer;
    public $offerAmount = 0;
    public $vat_value = 0;
    public $vat_included;
    public $doctor;
    public $user;
    public $result;
    public $discountPercentage;
    public $discountMoney = 0;
    public $services;
    public $services_prices = 0;

    public function __construct()
    {
        $this->currency = trans('lang.currency_type');
        $this->result = new \stdClass();
    }

    public function getResult()
    {

        // calculate the price of services first if exists
        $this->getServices();

        //  the check the offer
        if ($this->offer) {
            // just add the offer money to the
            if ($this->offer->price_type == self::TYPE_MONEY) {
                $this->offerAmount = $this->offer->price;
            }
            if ($this->checkIfOfferIncludesReservation()) {
                return $this->getOfferIncludedTotal();
            }
        }

        // calculate fees and offer and services prices
        $this->getSubTotal()->getDiscount();
        // return the total
        return $this->getTotal();
    }

    /**
     *  get the value of all added services
     *
     * @return void
     */
    public function getServices()
    {
        if ($this->services && count($this->services)) {
            $services_prices = array_column($this->services, 'price');
            $this->services_prices += array_sum($services_prices);
        }
    }

    /**
     *  check if offer includes reservation
     *
     * @return bool
     */
    private function checkIfOfferIncludesReservation(): bool
    {
        return $this->offer->reservation_fees_included == self::FEES_INCLUDED;
    }

    /**
     *  get the total for offer included
     *
     * @return \stdClass
     */
    private function getOfferIncludedTotal(): \stdClass
    {
        $this->result->subtotal_fees = 0;
        $this->result->total_fees = round($this->offerAmount + $this->services_prices, 2);
        $this->result->services = $this->services;
        $this->result->offer = $this->offer->price ?? 0;
        $this->result->currency = $this->currency;
        $this->result->is_doctor_premium = $this->IsDoctorPremium();
        return $this->result;
    }

    /**
     *  is patient premium
     *
     * @return bool
     */
    public function IsPatientPremium(): bool
    {
        return $this->user->is_premium == self::PREMIUM;
    }

    /**
     *  is doctor premium
     *
     * @return bool
     */
    public function IsDoctorPremium(): bool
    {
        return $this->doctor->is_premium == self::PREMIUM;
    }

    /**
     *  check if patient not expired
     *
     * @return bool
     */
    public function IsPatientNotExpired(): bool
    {
        return now()->format('Y-m-d') <= $this->user->expiry_date;
    }

    /**
     * calculate sub total
     */
    public function getSubTotal(): GetReservationFees
    {
        if ($this->IsPatientPremium() && $this->IsDoctorPremium() && $this->IsPatientNotExpired()) {
            $this->subtotal_fees = $this->fees;
            $this->subtotal_fees_actual = $this->premium_fees;
            $this->discountMoney = $this->fees - $this->premium_fees;
        } else {
            $this->subtotal_fees = $this->fees;
            $this->subtotal_fees_actual = $this->fees;
        }
        return $this;
    }

    /**
     * get discount number
     */
    public function getDiscount(): GetReservationFees
    {
        if ($this->IsPatientPremium()) {
            if ($this->IsDoctorPremium() && $this->IsPatientNotExpired()) {
                if ($this->fees <= 0) {
                    $this->discountPercentage = 100;
                } else {
                    $this->discountPercentage = floor(100 - ($this->premium_fees / $this->fees) * 100);
                }
            } else if (!$this->IsDoctorPremium()) {
                $this->discountPercentage = 0;
            }
        }
        return $this;
    }

    /**
     * get vat value
     */
    public function getVatValue(): GetReservationFees
    {
        if ($this->vat_included == 1) {
            $this->vat_value = round($this->subtotal_fees * self::VAT_VALUE, 2);
        } else {
            $this->vat_value = 0;
        }
        return $this;
    }

    /**
     * @return \stdClass
     */
    public function getTotal(): \stdClass
    {
        $this->result->subtotal_fees = $this->subtotal_fees_actual;      // Sub Total Fees
        $this->result->offer = $this->offer ? $this->offer->price : null;              // offer object
//        $this->result->vat = $this->vat_value;
        $this->result->currency = $this->currency;                 // currency (mostly EGP)
        // calculate total price from (reservation Fees, offer Price, Services Price)

        // calculate total from (actual_sub_total + offer price + total services)
        $this->result->total_fees = round($this->subtotal_fees_actual + $this->offerAmount + $this->services_prices, 2);
        $this->result->services = $this->services;       // list of services with prices
        $this->result->discount = $this->discountPercentage;      // discount percentage (in case of premium)
        $this->result->discount_money = $this->discountMoney;     // discount amount of money
        $this->result->is_doctor_premium = $this->IsDoctorPremium();       // doctor status if premium or not
        return $this->result;
    }
}
