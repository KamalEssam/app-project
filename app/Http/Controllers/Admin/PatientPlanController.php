<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\WebController;
use App\Http\Repositories\Web\PatientPlanRepository;
use App\Http\Requests\PatientPlanRequest;
use Illuminate\Database\Eloquent\Collection;
use DB;

class PatientPlanController extends WebController
{
    private $patientplanRepository;

    public function __construct(PatientPlanRepository $patientplanRepository)
    {
        $this->patientplanRepository = $patientplanRepository;
    }

    /**
     *  get list of all plans
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $plans = $this->patientplanRepository->getAllPlans();
        if (!$plans) {
            $plans = new Collection();
        }
        return view('admin.rk-admin.patient_plans.index', compact('plans'));
    }

    /**
     * Show the form for creating new plan
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.rk-admin.patient_plans.create');
    }

    /**
     * Store a newly created plan
     *
     * @param PatientPlanRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(PatientPlanRequest $request)
    {
        DB::beginTransaction();
        // create new plan
        try {
            // when user select unlimited plan
            $this->patientplanRepository->createPlan($request);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            // log message
            $this->logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.plan_add_err'), 'patient-plans.index');
        }
        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.plan_added_ok'), 'patient-plans.index');
    }

    /**
     *  show plan edit form
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        $plan = $this->patientplanRepository->getPlanById($id);
        if (!$plan) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.plan_not_found'), 'patient-plans.index');
        }
        return view('admin.rk-admin.patient_plans.edit', compact('plan'));
    }

    /**
     * Update the specified plan
     *
     * @param PatientPlanRequest $request
     * @param  int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(PatientPlanRequest $request, $id)
    {

        $plan = $this->patientplanRepository->getPlanById($id);
        if (!$plan) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.plan_update_err'), 'patient-plans.index');
        }

        DB::beginTransaction();
        try {
            $this->patientplanRepository->updatePlan($plan, $request);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            // log message
            $this->logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.plan_update_err'), 'patient-plans.index');
        }

        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.plan_update_ok'), 'patient-plans.index');
    }

//    /**
//     * Remove the specified plan from database.
//     *
//     * @param  int $id
//     * @return \Illuminate\Http\JsonResponse
//     */
//    public function destroy($id)
//    {
//        return $this->deleteItem($this->patientplanRepository->plan, $id);
//    }
}
