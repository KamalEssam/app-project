<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Repositories\Api\ReportRepository;
use Illuminate\Http\Request;
use Validator;

class ReportController extends ApiController
{
    private $reportRepository;

    /**
     * WorkingHourController constructor.
     * @param Request $request
     * @param ReportRepository $reportRepo
     */
    public function __construct(Request $request, ReportRepository $reportRepo)
    {
        $this->reportRepository = $reportRepo;
        $this->setLang($request);
    }

    /**
     *  add new Report
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function addReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reservation_id' => 'required|numeric|exists:reservations,id',
            'body' => 'required',
            'type' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return self::jsonResponse(false, self::CODE_NOT_FOUND, $validator->errors()->first(), $validator->errors());
        }

        $user = auth()->guard('api')->user();
        if ($user == null) {
            return self::jsonResponse(false, self::CODE_NOT_FOUND, trans('lang.login_unauthorized'));
        }

        $request['user_id'] = $user->id;
        if ($this->reportRepository->addReport($request)) {
            return self::jsonResponse(true, self::CODE_OK, trans('lang.report_add_ok'));
        }

        return self::jsonResponse(true, self::CODE_OK, trans('lang.report_add_err'));
    }
}
