<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 10/2/18
 * Time: 11:57 AM
 */

namespace App\Http\Repositories\Api;

use App\Http\Interfaces\Api\AttachmentInterface;
use App\Http\Repositories\Web\ParentRepository;
use App\Models\CashBack;


class CashBackRepository extends ParentRepository
{

    protected $cash_back;


    private const DOCTOR_PERCENTAGE_INSTALLMENT_WITHOUT_OFFER = 97.5;     // 97.5 %
    private const SEENA_PERCENTAGE_INSTALLMENT_WITHOUT_OFFER = 2.5;     // 97.5 %

    private const DOCTOR_PERCENTAGE_INSTALLMENT_WITH_OFFER = 90;     // 97.5 %
    private const SEENA_PERCENTAGE_INSTALLMENT_WITH_OFFER = 10;     // 97.5 %

    public function __construct()
    {
        $this->cash_back = new CashBack();
    }

    /**
     *  get first setting
     *
     * @param $patient_id
     * @param $reservaion_id
     * @param $clinic_id
     * @param $account_id
     * @param $total
     * @return mixed
     */
    public function requestCashBackForInstallmentwithOutOffer($patient_id, $reservaion_id, $clinic_id, $account_id, $total)
    {
        try {
            return $this->cash_back->create([
                'account_id' => $account_id,
                'patient_id' => $patient_id,
                'clinic_id' => $clinic_id,
                'reservation_id' => $reservaion_id,
                'patient_cash' => 0,  // in case of installment patient wont get any thing
                'doctor_cash' => $this->getMoneyUsingPercentage($total, self::DOCTOR_PERCENTAGE_INSTALLMENT_WITHOUT_OFFER),
                'seena_cash' => $this->getMoneyUsingPercentage($total, self::SEENA_PERCENTAGE_INSTALLMENT_WITHOUT_OFFER),
            ]);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get first setting
     *
     * @param $patient_id
     * @param $reservaion_id
     * @param $clinic_id
     * @param $account_id
     * @param $total
     * @return mixed
     */
    public function requestCashBackForInstallmentwithOffer($patient_id, $reservaion_id, $clinic_id, $account_id, $total)
    {
        try {
            return $this->cash_back->create([
                'account_id' => $account_id,
                'patient_id' => $patient_id,
                'clinic_id' => $clinic_id,
                'reservation_id' => $reservaion_id,
                'patient_cash' => 0,  // in case of installment patient wont get any thing
                'doctor_cash' => $this->getMoneyUsingPercentage($total, self::DOCTOR_PERCENTAGE_INSTALLMENT_WITH_OFFER),
                'seena_cash' => $this->getMoneyUsingPercentage($total, self::SEENA_PERCENTAGE_INSTALLMENT_WITH_OFFER),
            ]);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  check if specific reservation has cashBack
     *
     * @param $reservation_id
     * @return bool
     */
    public function checkIfReservaionHasCashBack($reservation_id)
    {
        try {
            return $this->cash_back->where('reservation_id', $reservation_id)->first();
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            return false;
        }
    }

    /**
     *  get the (patient , doctor, seena ) money using percentage
     *
     * @param $total
     * @param $percentage
     * @return float|int
     */
    private function getMoneyUsingPercentage($total, $percentage)
    {
        return ($percentage / 100) * $total;
    }
}
