<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\WebController;
use App\Http\Repositories\Web\PlanRepository;
use App\Http\Requests\PlanRequest;
use Illuminate\Database\Eloquent\Collection;

class PlanController extends WebController
{
    private $planRepository;

    public function __construct(PlanRepository $planRepository)
    {
        $this->planRepository = $planRepository;
    }

    /**
     *  get list of all plans
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $plans = $this->planRepository->getAllPlans();
        if (!$plans)
            $plans = new Collection();
        return view('admin.rk-admin.plans.index', compact('plans'));
    }

//    /**
//     * Show the form for creating new plan
//     *
//     * @return \Illuminate\Http\Response
//     */
//    public function create()
//    {
//        return view('admin.rk-admin.plans.create');
//    }
//
//    /**
//     * Store a newly created plan
//     *
//     * @param PlanRequest $request
//     * @return \Illuminate\Http\RedirectResponse
//     * @throws \Exception
//     */
//    public function store(PlanRequest $request)
//    {
//        DB::beginTransaction();
//        // create new plan
//        try {
//
//            $request['created_by'] = auth()->user()->id;
//            // when user select unlimited plan
//            $this->planRepository->createPlan($request);
//            DB::commit();
//        } catch (\Exception $e) {
//            DB::rollback();
//            // log message
//            $this->logErr($e->getMessage());
//            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.plan_add_err'), 'plans.index');
//        }
//
////        TODO  in future we could activate the plan image
////        if ($request->file('image')) {
////            $image = Super::uploadFile($request->image, 'assets/images/plans');
////            if (!$image) {
////                Flashy::error('There is problem in load image');
////                return view('admin.rk-admin.plans.form');
////            }
////            $plan->image = $image;
////            $plan->update();
////        }
////
//        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.plan_added_ok'), 'plans.index');
//    }
//
//    /**
//     *  show plan edit form
//     *
//     * @param $id
//     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
//     */
//    public function edit($id)
//    {
//        $plan = $this->planRepository->getPlanById($id);
//        if (!$plan) {
//            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.plan_not_found'), 'plans.index');
//        }
//        return view('admin.rk-admin.plans.edit', compact('plan'));
//    }
//
//    /**
//     * Update the specified plan
//     *
//     * @param PlanRequest $request
//     * @param  int $id
//     * @return \Illuminate\Http\RedirectResponse
//     * @throws \Exception
//     */
//    public function update(PlanRequest $request, $id)
//    {
//
//        $plan = $this->planRepository->getPlanById($id);
//        if (!$plan) {
//            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.plan_update_err'), 'plans.index');
//        }
//
//        DB::beginTransaction();
//        try {
//            $request['updated_by'] = auth()->user()->id;
//            $this->planRepository->updatePlan($plan, $request);
//            DB::commit();
//        } catch (\Exception $e) {
//            DB::rollback();
//            // log message
//            $this->logErr($e->getMessage());
//            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.plan_update_err'), 'plans.index');
//        }
//
//        // TODO  in future we could activate the plan image
////        if ($request->image) {
////            if ($plan->image != 'default.png') {
////                $is_deleted = Super::deleteFile('assets/images/plans' . $plan->image);
////                if (!$is_deleted) {
////                    abort('500');
////                }
////            }
////            $image = Super::uploadFile($request->file('image'), 'assets/images/plans');
////            if (!$image) {
////                Flashy::error('There is problem in load image');
////                return view('admin.rk-admin.plans.form');
////            }
////            $plan->image = $image;
////            $plan->update();
////        }
//
//        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.plan_update_ok'), 'plans.index');
//    }
//
//    /**
//     * Remove the specified plan from database.
//     *
//     * @param  int $id
//     * @return \Illuminate\Http\JsonResponse
//     */
//    public function destroy($id)
//    {
//        return $this->deleteItem($this->planRepository->plan, $id);
//    }
}
