<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:45 PM
 */

namespace App\Http\Interfaces\Web;

interface InsuranceCompaniesInterface
{

    /**
     * get list of all insurance companies
     *
     * @return mixed
     */
    public function getAllInInsuranceCompanies();

    /**
     *  get insurance company by id
     *
     * @param $id
     * @return mixed
     */
    public function getInfluencerById($id);


    /**
     *  create new insurance company
     *
     * @param $request
     * @return mixed
     */
    public function createInsuranceCompany($request);

    /**
     *  update insurance company
     *
     * @param $influencer
     * @param $request
     * @return mixed
     */
    public function updateInsuranceCompany($influencer, $request);

    /**
     * array of insurance companies
     *
     * @return mixed
     */
    public function getArrayOfInInsuranceCompany();


    /**
     *  api list for insurance companies
     *
     * @return mixed
     */
    public function ApiAllInInsuranceCompanies();
}
