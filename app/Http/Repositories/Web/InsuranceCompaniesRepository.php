<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:48 PM
 */

namespace App\Http\Repositories\Web;

use App\Http\Interfaces\Web\InsuranceCompaniesInterface;
use App\Models\InsuranceCompany;

class InsuranceCompaniesRepository extends ParentRepository implements InsuranceCompaniesInterface
{
    public $insuranceCompany;

    public function __construct()
    {
        $this->insuranceCompany = new InsuranceCompany();
    }

    /**
     *  get insuranceCompany by id
     *
     * @param $id
     * @return mixed
     */
    public function getInfluencerById($id)
    {
        try {
            return $this->insuranceCompany->find($id);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }


    /**
     *  get array of insurance company
     *
     * @return bool
     */
    public function getArrayOfInInsuranceCompany()
    {
        try {
            return $this->insuranceCompany->pluck(app()->getLocale() . '_name as name', 'id');
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return null;
        }
    }

    /**
     * get list of all insurance companies
     *
     * @return mixed
     */
    public function getAllInInsuranceCompanies()
    {
        try {
            return $this->insuranceCompany->orderBy('created_at')->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  create new insurance company
     *
     * @param $request
     * @return mixed
     */
    public function createInsuranceCompany($request)
    {
        return $this->insuranceCompany->create($request->all());
    }

    /**
     *  update insurance company
     *
     * @param $insuranceCompany
     * @param $request
     * @return mixed
     */
    public function updateInsuranceCompany($insuranceCompany, $request)
    {
        return $insuranceCompany->update($request->all());
    }

    /**
     * get list of all insurance companies for Api
     *
     * @return mixed
     */
    public function ApiAllInInsuranceCompanies()
    {
        try {
            return $this->insuranceCompany->select('id', app()->getLocale() . '_name as name')->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  add insurance company from import
     *
     * @param $company
     * @return bool
     */
    public function addInsuranceCompanyFromImport($company)
    {
        try {
            return $this->insuranceCompany->create($company);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }
}
