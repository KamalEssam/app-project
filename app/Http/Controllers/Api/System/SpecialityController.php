<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 11/17/18
 * Time: 3:26 PM
 */

namespace App\Http\Controllers\Api\System;


use App\Http\Controllers\ApiController;
use App\Http\Repositories\Validation\SpecialityValidationRepository;
use App\Http\Repositories\Web\SpecialityRepository;
use Illuminate\Http\Request;

class SpecialityController extends ApiController
{

    private $specialityRepository, $specialityValidationRepository;
    private $specialities_limit = 5;

    public function __construct(Request $request, SpecialityRepository $specialityRepository, SpecialityValidationRepository $specialityValidationRepository)
    {
        $this->specialityRepository = $specialityRepository;
        $this->specialityValidationRepository = $specialityValidationRepository;
        $this->setLang($request);
    }


    /**
     *  get list of specialities
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function SiteSpecialitiesList(Request $request)
    {
        // check if featured or noy
        $featured = (isset($request->featured) && ($request->featured == self::TRUE)) ? self::TRUE : self::FALSE;
        // get all featured Specialities and doctors related to it
        $specialities = $this->specialityRepository->getFeaturedSpecialities($this->specialities_limit, $featured);

        return self::jsonResponse(true, 20, trans('lang.specialities'), [], $specialities);
    }

    /**
     * get all doctors belong to this speciality
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSpecialtyDetails(Request $request)
    {
        // validate fields
        if (!$this->specialityValidationRepository->getSpecialityIdValidation($request)) {
            return self::jsonResponse(false, self::CODE_VALIDATION, $this->specialityValidationRepository->getFirstError(), $this->specialityValidationRepository->getErrors());
        }
        // get all doctors related to this speciality
        $doctors = $this->specialityRepository->getDoctorsBySpecialitySlug($request->slug);

        $specialityDetails = $this->specialityRepository->getSpecialityBySlugWithLocale($request->slug);

        $specialityDetails->doctors = $doctors;


        return self::jsonResponse(true, 20, trans('lang.doctors'), [], $specialityDetails);
    }

}