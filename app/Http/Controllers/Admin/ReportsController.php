<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\WebController;
use App\Http\Repositories\Api\ReportRepository;
use Illuminate\Http\Request;
use Validator;

class ReportsController extends WebController
{
    private $reportRepository;

    /**
     * @param ReportRepository $reportRepo
     */
    public function __construct(ReportRepository $reportRepo)
    {
        $this->reportRepository = $reportRepo;
    }

    /**
     *  list of all reports
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $reports = $this->reportRepository->getReports();
        return view('admin.rk-admin.reports.index', compact('reports'));
    }

    /**
     *  update report status
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|min:1',
            'status' => 'required|numeric|min:0|max:2'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => 'Validation Error']);
        }

        $report = $this->reportRepository->getReportById($request->get('id'));
        if (!$report) {
            return response()->json(['status' => false, 'msg' => 'Report Doesnt Exist']);
        }

        if (!$this->reportRepository->updateReport($report, $request->get('status'))) {
            return response()->json(['status' => false, 'msg' => 'Failed To Update Report']);
        }

        return response()->json(['status' => true, 'msg' => '']);
    }
}
