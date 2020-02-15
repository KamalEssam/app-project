<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:48 PM
 */

namespace App\Http\Repositories\Web;

use App\Http\Interfaces\Web\SaleLeadsInterface;
use App\Models\SalesLeads;

class saleLeadsRepository extends ParentRepository implements SaleLeadsInterface
{
    public $lead;

    public function __construct()
    {
        $this->lead = new SalesLeads();
    }

    /**
     * get list of all leads
     *
     * @return mixed
     */
    public function getAllLeads()
    {
        try {
            return $this->lead->where('sale_id', auth()->user()->id)->orderBy('created_at')->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     * get lead by id
     *
     * @param $id
     * @return mixed
     */
    public function getLeadById($id)
    {
        try {
            return $this->lead->find($id);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  create new lead
     *
     * @param $request
     * @return mixed
     */
    public function createLead($request)
    {
        try {
            return $this->lead->create($request->all());
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  create new lead
     *
     * @param $lead
     * @return mixed
     */
    public function addLeadsFromImport($lead)
    {
        try {
            return $this->lead->create($lead);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  update lead
     *
     * @param $lead
     * @param $request
     * @return mixed
     */
    public function updateLead($lead, $request)
    {
        try {
            return $lead->update($request->all());
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     * @return mixed
     */
    public function getLeadsForExporting()
    {
        return $this->lead->where('sale_id', auth()->user()->id)->orderBy('created_at')->select('name', 'mobile')->get();
    }

    /**
     *   get the count of sales leads
     *
     * @return mixed
     */
    public function getCountOfSalesLeads()
    {
        return $this->lead->where('sale_id', auth()->user()->id)->count();
    }
}
