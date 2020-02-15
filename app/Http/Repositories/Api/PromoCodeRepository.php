<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:48 PM
 */

namespace App\Http\Repositories\Api;

use App\Http\Repositories\Web\ParentRepository;
use App\Models\PremiumPromoCodes;

class PromoCodeRepository extends ParentRepository
{
    protected $promo;

    public function __construct()
    {
        $this->promo = new PremiumPromoCodes();
    }

    /**
     *  get code model by name
     *
     * @param $code
     * @return mixed
     */
    public function getCodeByName($code)
    {
        try {
            return $this->promo->where('code', $code)->first();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }


    /**
     *  check if promo-code exists or not and check if used by the user or not
     *
     * @param $patient_id
     * @param $code
     * @return bool
     */
    public function checkCodeValidationAndUsage($patient_id, $code)
    {
        // first check if code exists
        $promoCode = $this->getCodeByName($code);
        if (!$promoCode) {
            return false;
        }

        // then check code is expired or not
        if (now()->format('Y-m-d') > $promoCode->expiry_date) {
            return false;
        }

        // finally check if user user this code for reservation or not
        $is_code_used = (new ReservationRepository())->getReservationByPromoCodeAndPatinet($patient_id, $promoCode->id);

        if ($is_code_used) {
            return false;
        }

        // all ok
        return true;
    }

    /**
     *  get code value after validation and usage for user
     *
     * @param $patient_id
     * @param $code
     * @return mixed
     */
    public function getCodeValueAfterValidation($patient_id, $code)
    {
        if ($this->checkCodeValidationAndUsage($patient_id, $code)) {
            return $this->getCodeByName($code);
        }

        return false;
    }

}
