<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:45 PM
 */

namespace App\Http\Interfaces\Web;

interface SaleLeadsInterface
{

    /**
     * get list of all leads
     *
     * @return mixed
     */
    public function getAllLeads();

    /**
     * get lead by id
     *
     * @param $id
     * @return mixed
     */
    public function getLeadById($id);

    /**
     *  create new lead
     *
     * @param $request
     * @return mixed
     */
    public function createLead($request);

    /**
     *  update lead
     *
     * @param $lead
     * @param $request
     * @return mixed
     */
    public function updateLead($lead, $request);
}
