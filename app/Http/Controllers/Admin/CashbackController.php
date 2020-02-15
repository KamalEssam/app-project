<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\WebController;
use App\Http\Repositories\Api\AuthRepository;
use App\Http\Repositories\Web\CashbackRepository;
use App\Models\DoctorIncome;
use App\Models\SeenaIncome;
use Illuminate\Http\Request;

class CashbackController extends WebController
{
    private $cashbackRepo;

    public function __construct(CashbackRepository $cashbackRepository)
    {
        $this->cashbackRepo = $cashbackRepository;
    }

    /**
     *  show list of all ads
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $cashbacks = $this->cashbackRepo->getCashBacks();
        return view('admin.doctor.cashback.index', compact('cashbacks'));
    }


    /**
     *  accept cash back request from patients
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function acceptRequest($id)
    {
        $cashback = $this->cashbackRepo->getCashBackById($id);

        if ($cashback && $cashback->is_approved != 1) {
            \DB::beginTransaction();
            try {
                // steps
                // 1 - update status of request to approved
                $this->cashbackRepo->updateRequest($id, 1);
                // 2 - add record in doctor income with doctor value
                DoctorIncome::create([
                    'request_id' => $cashback->id,
                    'account_id' => $cashback->account_id,
                    'income' => $cashback->doctor_cash
                ]);
                // 3 - add record in seena income with seena value
                SeenaIncome::create([
                    'request_id' => $cashback->id,
                    'account_id' => $cashback->account_id,
                    'income' => $cashback->seena_cash
                ]);
                // 4 - add the value of cashback to patient cashback field
                $patient = (new AuthRepository())->getUserById($cashback->patient_id);
                if ($patient) {
                    (new AuthRepository())->updateColumn($patient, 'cash_back', $patient->cash_back + $cashback->patient_cash);
                } else {
                    return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.cash-back-request-failed'));
                }

            } catch (\Exception $e) {
                \DB::rollBack();
                \Log::info($e->getMessage());
                return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.cash-back-request-failed'));
            }

            \DB::commit();
            // commit transaction

            return $this->messageAndRedirect(self::STATUS_OK, trans('lang.cash-back-request-ok'));
        }
        return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.cash-back-request-failed'));

    }

    /**
     *  decline patient request of cashback
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function declineRequest($id)
    {

        $cashback = $this->cashbackRepo->getCashBackById($id);
        if ($cashback) {
            $this->cashbackRepo->updateRequest($id, -1);
            return $this->messageAndRedirect(self::STATUS_OK, trans('lang.cash-back-request-declined'));
        }
        return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.cash-back-request-failed'));
    }

    public function reports()
    {
        $doctorCashback = $this->cashbackRepo->getDoctorCashBacks();
        return view('admin.doctor.cashback.reports', compact('doctorCashback'));
    }
}
