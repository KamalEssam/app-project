<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\WebController;
use App\Http\Repositories\Web\InsuranceCompaniesRepository;
use App\Http\Requests\InsuranceCompaniesRequest;
use App\Imports\InsuranceCompaniesImport;
use DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class InsuranceCompanyController extends WebController
{
    private $insuranceRepository;

    public function __construct(InsuranceCompaniesRepository $insuranceCompanies)
    {
        $this->insuranceRepository = $insuranceCompanies;
    }

    /**
     *  show list of all insurance companies
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $insurance_companies = $this->insuranceRepository->getAllInInsuranceCompanies();
        return view('admin.rk-admin.insurance_companies.index', compact('insurance_companies'));
    }

    /**
     * Store new insurance company in database
     *
     * @param InsuranceCompaniesRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(InsuranceCompaniesRequest $request)
    {
        // add city
        DB::beginTransaction();
        try {
            $this->insuranceRepository->createInsuranceCompany($request);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // log error
            self::logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.insurance_company_add_err'));
        }

        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.insurance_company_add_ok'), 'insurance_company.index');
    }

    /**
     *  show edit insurance company form
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        $insurance_company = $this->insuranceRepository->getInfluencerById($id);
        if (!$insurance_company) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.insurance_company_not_found'));
        }
        return view('admin.rk-admin.insurance_companies.edit', compact('insurance_company'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param InsuranceCompaniesRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(InsuranceCompaniesRequest $request, $id)
    {
        $insurance_company = $this->insuranceRepository->getInfluencerById($id);
        if (!$insurance_company) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.insurance_company_not_found'));
        }

        DB::beginTransaction();
        // update insurance company
        try {
            $this->insuranceRepository->updateInsuranceCompany($insurance_company, $request);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // log error
            self::logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.insurance_company_update_err'));
        }
        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.insurance_company_update_ok'), 'insurance_company.index');
    }

    /**
     * Remove Insurance Company
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        return $this->deleteItem($this->insuranceRepository->insuranceCompany, $id);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function importCompanies(Request $request)
    {
        if (!$request['insurance_companies']) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.insurance_company_import_err'));
        }
        Excel::import(new InsuranceCompaniesImport(), $request['insurance_companies']);
        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.insurance_company_update_ok'), 'insurance_company.index');
    }
}
