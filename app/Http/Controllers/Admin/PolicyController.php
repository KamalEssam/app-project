<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\WebController;
use App\Http\Repositories\Web\PolicyRepository;
use App\Http\Requests\PolicyRequest;
use DB;

class PolicyController extends WebController
{
    private $policyRepository;

    public function __construct(PolicyRepository $policyRepository)
    {
        $this->policyRepository = $policyRepository;
    }

    /**
     *  get list of all countries in the application
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $policies = $this->policyRepository->getAllPolicies();
        return view('admin.rk-admin.policies.index', compact('policies'));
    }

    /**
     * Store a newly created policy
     *
     * @param policyRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(PolicyRequest $request)
    {
        DB::beginTransaction();
        // add the policy
        try {
            $this->policyRepository->createPolicy($request);
        } catch (\Exception $e) {
            DB::rollback();
            // log error
            self::logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.policy_add_err'));
        }
        DB::commit();
        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.policy_added_ok'));
    }

    /**
     * show edit policy page
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        $policy = $this->policyRepository->getPolicyById($id);
        if (!$policy) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.policy_not_found'));
        }
        return view('admin.rk-admin.policies.edit', compact('policy'));
    }

    /**
     * Update the policy data
     *
     * @param policyRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(policyRequest $request, $id)
    {

        $policy = $this->policyRepository->getPolicyById($id);
        if (!$policy) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.policy_not_found'));
        }

        DB::beginTransaction();
        try {
            $request['updated_by'] = auth()->user()->id;
            $this->policyRepository->updatePolicy($policy, $request);
        } catch (\Exception $e) {
            DB::rollback();
            // log error
            self::logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.policy_update_err'));
        }
        DB::commit();
        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.policy_update_ok'));
    }

//    /**
//     * Remove the policy
//     *
//     * @param  int $id
//     * @return \Illuminate\Http\JsonResponse
//     */
//    public function destroy($id)
//    {
//        return $this->deleteItem($this->policyRepository->policy, $id);
//    }
}
