<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Repositories\Web\InsuranceCompaniesRepository;
use Illuminate\Http\Request;

class InsuranceCompanyController extends ApiController
{
    private $insuranceRepository;

    /**
     * DoctorController constructor.
     * @param InsuranceCompaniesRepository $insuranceRepository
     * @param Request $request
     */
    public function __construct(InsuranceCompaniesRepository $insuranceRepository, Request $request)
    {
        $this->setLang($request);
        $this->insuranceRepository = $insuranceRepository;
    }

    /**
     *  get list of all insurance companies
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function getAllInsuranceCompanies(Request $request): \Illuminate\Http\JsonResponse
    {
        $insuranceCompanies = $this->insuranceRepository->ApiAllInInsuranceCompanies();
        if (!$insuranceCompanies) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.no_insurance_companies'));
        }
        return self::jsonResponse(true, self::CODE_OK, trans('lang.insurance_companies'), '', $insuranceCompanies);
    }
}
