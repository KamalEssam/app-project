<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\WebController;
use App\Http\Repositories\Web\AuthRepository;
use App\Http\Repositories\Web\ClinicRepository;
use App\Http\Repositories\Web\DoctorDetailsRepository;
use App\Http\Repositories\Web\DoctorServiceRepository;
use App\Http\Repositories\Web\PremiumRequestRepository;
use App\Http\Repositories\Web\ServiceRepository;
use App\Models\DoctorDetail;
use DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class PremiumRequestController extends WebController
{
    private $premiumRequest;

    public function __construct(PremiumRequestRepository $premiumRequest)
    {
        $this->premiumRequest = $premiumRequest;
    }

    /**
     *  get list of all plans
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $status = $request['status'] == null ? -1 : (($request['status'] == 'approved') ? 1 : (($request['status'] == 'declined') ? 0 : -1));
        $premiumRequests = $this->premiumRequest->getAllRequestsByStatus($status);
        if (!$premiumRequests) {
            $premiumRequests = new Collection();
        }
        return view('admin.rk-admin.premiumRequests.index', compact('premiumRequests'));
    }

    /**
     *  approve or decline the request to membership
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeStatus(Request $request)
    {
        if ((!is_null($request['request_id']) && is_numeric($request['request_id'])) && (!is_null($request['status']) && in_array($request['status'], [0, 1]))) {
            $getRequest = $this->premiumRequest->getPremiumRequestById($request['request_id']);

            if (!$getRequest) {
                return response()->json(['status' => false, 'msg' => 'request not found']);
            }
            // update request
            $updated_request = $this->premiumRequest->SetRequestStatus($getRequest, $request['status']);

            if (!$updated_request) {
                return response()->json(['status' => false, 'msg' => 'failed to do this action']);
            }
            if ($updated_request->approval == 1) {
                (new AuthRepository())->updateUserPremium($updated_request->user_id, 1, $updated_request->plan_id, $updated_request->plan->months);
                (new AuthRepository())->updateUserPromCode($updated_request->user_id, 'premium_code_id', null);
                $msg = $updated_request->user->name . ' request has been approved';
            } else {
                $msg = $updated_request->user->name . ' request has been declined';
            }

            return response()->json(['status' => true, 'msg' => $msg]);
        } else {
            return response()->json(['status' => false, 'msg' => 'Invalid Arguments']);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */

    public function setDoctorPremium(Request $request)
    {
        try {
            $services_value = $request['service_value'];
            $services_id = $request['service_id'];

            DB::beginTransaction();
            for ($i = 0, $iMax = count($services_value); $i < $iMax; $i++) {
                (new DoctorServiceRepository())->updateServicePremiumPrice($services_id[$i], $services_value[$i]);
            }

            $branch_id = $request['branch_id'];
            $services_fees = $request['fees'];
            $services_fees_follow_up = $request['follow_fees'];

            for ($j = 0, $jMax = count($branch_id); $j < $jMax; $j++) {
                (new ClinicRepository())->updateClinicPremiumPrice($branch_id[$j], $services_fees[$j], $services_fees_follow_up[$j]);
            }
            // then set Doctor as Premium
            (new AuthRepository())->updateDoctorPremium(auth()->user(), 1);

            DB::commit();
        } catch (\Exception $e) {
            return redirect()->route('admin');
        }

        // update min fee
        (new DoctorDetailsRepository())->updateMinFees();

        return redirect()->route('admin');
    }
}
