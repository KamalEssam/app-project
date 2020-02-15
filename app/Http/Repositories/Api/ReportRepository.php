<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 10/2/18
 * Time: 11:57 AM
 */

namespace App\Http\Repositories\Api;

use App\Http\Repositories\Web\ParentRepository;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportRepository extends ParentRepository
{
    public $report;

    public function __construct()
    {
        $this->report = new Report();
    }

    /**
     *  add report from mobile and web
     *
     * @param Request $request
     * @return bool
     */
    public function addReport(Request $request)
    {
        try {
            return $this->report->create($request->all());
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get list of all reports sent from customers
     *
     * @return bool
     */
    public function getReports()
    {
        try {
            return $this->report->orderBy('created_at', 'desc')->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get report by Id
     *
     * @param $id
     * @return bool
     */
    public function getReportById($id)
    {
        try {
            return $this->report->find($id);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  update report status
     *
     * @param $report
     * @param $status
     * @return bool
     */
    public function updateReport($report, $status)
    {
        try {
            return $report->update([
                'status' => $status
            ]);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }
}
