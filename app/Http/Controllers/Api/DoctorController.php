<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Repositories\Api\AuthRepository;
use App\Http\Repositories\Api\DoctorRepository;
use App\Http\Repositories\Api\ProfileRepository;
use App\Http\Repositories\Api\ReservationRepository;
use App\Http\Repositories\Api\ReviewRepository;
use App\Http\Repositories\Validation\DoctorValidationRepository;
use App\Http\Repositories\Web\CityRepository;
use App\Http\Repositories\Web\InsuranceCompaniesRepository;
use App\Http\Traits\UserTrait;
use App\Models\Service;
use Illuminate\Http\Request;
use Validator;

class DoctorController extends ApiController
{
    private $doctorRepository, $doctorValidationRepository;
    use UserTrait;

    /**
     * DoctorController constructor.
     * @param Request $request
     * @param DoctorRepository $doctorRepository
     * @param DoctorValidationRepository $doctorValidationRepository
     */
    public function __construct(Request $request, DoctorRepository $doctorRepository, DoctorValidationRepository $doctorValidationRepository)
    {
        $this->doctorRepository = $doctorRepository;
        $this->doctorValidationRepository = $doctorValidationRepository;
        $this->setLang($request);
    }

    /**
     *  get list of doctors in the applications
     *
     * @param Request $request
     * @return mixed
     */
    public function getAllDoctors(Request $request)
    {
        // parse sub_specialities to be array
        if (!is_array($request->sub_specialities)) {
            $request->sub_specialities = json_decode($request->sub_specialities);
        }
        // parse insurance companies to be array
        if (!is_array($request->insurance_companies)) {
            $request->insurance_companies = json_decode($request->insurance_companies);
        }

        $doctors = $this->doctorRepository->getAllDoctors($request);
        if (empty($doctors)) {
            $doctors = [];
        }

        return self::jsonResponse(true, self::CODE_OK, trans('lang.all-doctors'), new \stdClass(), $doctors);
    }

    /**
     * get doctor profile details
     *
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function getDoctorProfile(Request $request)
    {
        $identifier = 'id';

        if (strpos($request->doctor_id, 'RK_ACC') !== false) {
            $identifier = 'unique_id';
            $doctor = (new \App\Http\Repositories\Web\AuthRepository())->getUserByColumn($identifier, $request->doctor_id);
            if (!$doctor || !self::checkIfDoctor($doctor->id)) {

                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.invalid_role'));
            }
            $request->doctor_id = $doctor->id;
        }


        $user = auth()->guard('api')->user();

        // Check if patient
        if ($user && self::checkIfDoctor($user->id)) {
            $request->request->add(['doctor_id' => $user->id]);
        }

        // validate fields
        if (!$this->doctorValidationRepository->getDoctorIdValidation($request)) {
            return self::jsonResponse(false, self::CODE_VALIDATION, $this->doctorValidationRepository->getFirstError(), $this->doctorValidationRepository->getErrors());
        }

        $doctor_profile = $this->doctorRepository->getDoctorProfile($request->doctor_id);
        if (!$doctor_profile) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.something_wrong'));
        }

        $doctor_user = self::getUserById($request->doctor_id);
        if (!$doctor_user) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.something_wrong'));
        }

        if (!$user || $user->role_id == ApiController::ROLE_USER) {
            $increase_count_of_view = $this->doctorRepository->counterViewsForAccount($doctor_user->account_id);
            if ($increase_count_of_view === false) {
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.something_wrong'));
            }
        }
        return self::jsonResponse(true, self::CODE_OK, trans('lang.doctor-profile'), new \stdClass(), $doctor_profile);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function addAndRemoveToFavouriteList(Request $request)
    {
        $user = auth()->guard('api')->user();
        // if not unauthorized
        if ($user == null) {
            return self::jsonResponse(false, self::CODE_UNAUTHORIZED, trans('lang.unauthorized'));
        }
        // Check if patient
        if (!self::checkIfPatient($user)) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.invalid-role'));
        }
        // validate fields
        if (!$this->doctorValidationRepository->addAndRemoveToFavouriteListValidation($request)) {
            return self::jsonResponse(false, self::CODE_VALIDATION, $this->doctorValidationRepository->getFirstError(), $this->doctorValidationRepository->getErrors());
        }
        return self::jsonResponse(true, self::CODE_OK, trans('lang.doctor-profile'), new \stdClass(), $this->doctorRepository->addAndRemoveToFavouriteList($request, $user));
    }

    /**
     * get all Specialities and min & max fess of all clinics
     * @return mixed
     */
    public function filter()
    {
        $specialities = $this->doctorRepository->getSpecialities();
        if (!$specialities) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.something_wrong'));
        }
        $filter = new \stdClass();
        $filter->specialities = $specialities;
        return self::jsonResponse(true, self::CODE_OK, trans('lang.get-filter-data'), new \stdClass(), $filter);
    }

    /**
     * @return mixed
     */
    public function getProvincesList()
    {
        $cities = (new CityRepository())->getCitiesWithProvinces();
        if ($cities === false) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.something_wrong'));
        }
        return self::jsonResponse(true, self::CODE_OK, trans('lang.get-filter-data'), new \stdClass(), $cities);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getMyDoctorsList(Request $request)
    {
        $user = auth()->guard('api')->user();
        // if nit authorized
        if ($user == null) {
            return self::jsonResponse(false, self::CODE_UNAUTHORIZED, trans('lang.unauthorized'));
        }
        // Check if patient
        if (!self::checkIfPatient($user)) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.invalid-role'));
        }
        $get_my_doctor_list = $this->doctorRepository->getMyDoctorsList($request, $user);
        if (!$get_my_doctor_list) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.something_wrong'));
        }
        return self::jsonResponse(true, self::CODE_OK, trans('lang.get-filter-data'), new \stdClass(), $get_my_doctor_list);
    }

    /**
     * get doctor clinics
     * @param Request $request
     * @return mixed
     */
    public function getDoctorClinics(Request $request)
    {
        $user = auth()->guard('api')->user();

        if ($request->has('unique_id')) {
            $user = (new AuthRepository())->getUserByUniqueId($request->get('unique_id'));
        }

        if ($user && $user->role_id == self::ROLE_DOCTOR) {
            // if nit authorized
            if ($user == null) {
                return self::jsonResponse(false, self::CODE_UNAUTHORIZED, trans('lang.unauthorized'));
            }
            $doctor = self::getUserById($user->id);
            if (!$doctor) {
                return self::jsonResponse(false, self::CODE_NOT_FOUND, trans('lang.user_not_found'));
            }
            $doctor_role = self::checkIfDoctor($doctor->id);
            if ($doctor_role === false) {
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.not_valid'));
            }
        } else {
            // validate fields
            if (!$this->doctorValidationRepository->getDoctorIdValidation($request)) {
                return self::jsonResponse(false, self::CODE_VALIDATION, $this->doctorValidationRepository->getFirstError(), $this->doctorValidationRepository->getErrors());
            }
        }

        $doctor_id = ($user && $user->role_id == self::ROLE_DOCTOR) ? $user->id : $request->doctor_id;

        $get_doctor_clinics = $this->doctorRepository->getDoctorClinics($doctor_id);

        if (!$get_doctor_clinics) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.something_wrong'));
        }
        if ($get_doctor_clinics->count() <= 0) {
            return self::jsonResponse(true, self::CODE_NO_CLINICS, trans('lang.doctor-clinics-empty'), new \stdClass(), $get_doctor_clinics);
        }
        return self::jsonResponse(true, self::CODE_OK, trans('lang.doctor-clinics'), new \stdClass(), $get_doctor_clinics);
    }

    /**
     *  splash service
     *
     * @param Request $request
     * @return mixed
     */
    public function getSplashService(Request $request)
    {
        $response = new \stdClass();
        $response->reservation = null;
        $response->user = null;

        $user = auth()->guard('api')->user();

        if ($user) {
            $response->user = (new ProfileRepository())->getProfile($user);

            if ($user->is_premium == 1) {
                $response->user->plan = $user->userPlan;
                $response->user->plan->expiry_date = $user->expiry_date;
            } else {
                $response->user->plan = null;
            }
            // append premium object to active Doctor
            // check if
            $lastReservation = (new ReviewRepository())->getLastReservationNotReviewed($user->id);

            if ($lastReservation) {
                $reservation = (new ReservationRepository())->getReservationWithReview($lastReservation->id);

                if ($reservation && $reservation->reviews == 0 && $reservation->clinic != null) {
                    // get doctor reservation
                    // get doctor by account_id
                    $doctor = DoctorRepository::getDoctorInfoForReviewByAccountId($reservation->clinic->account_id);
                    if ($doctor) {
                        $response->reservation = $doctor;
                        $response->reservation->reservation_id = $lastReservation->id;
                    }
                }
            }
        }

        // get list of specialities and sub specialities
        $specialities = $this->doctorRepository->getSpecialities();
        if (!$specialities) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.something_wrong'));
        }
        $response->specialities = $specialities;

        // get list of cities and provinces
        $cities = (new CityRepository())->getCitiesWithProvinces();
        if ($cities === false) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.something_wrong'));
        }
        $response->cities = $cities;

        $insuranceCompanies = (new InsuranceCompaniesRepository())->ApiAllInInsuranceCompanies();
        if (!$insuranceCompanies) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.no_insurance_companies'));
        }
        $response->insuranceCompanies = $insuranceCompanies;

        return self::jsonResponse(true, self::CODE_OK, '', new \stdClass(), $response);
    }

    /**
     * recommend doctor (share doctor)
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function recommendDoctorAccount(Request $request)
    {
        // validate fields
        if (!$this->doctorValidationRepository->RecommendDoctorValidation($request)) {
            return self::jsonResponse(false, self::CODE_VALIDATION, $this->doctorValidationRepository->getFirstError(), $this->doctorValidationRepository->getErrors());
        }

        $user = auth()->guard('api')->user();
        if ($user == null) {
            $user = \App\Http\Repositories\Web\AuthRepository::getUserByColumn('unique_id', $request->user_id);
        }

        // in case of authenticated user
        // Check if patient
        if ($user && !self::checkIfPatient($user)) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.invalid-role'));
        }

        // get doctor id using account_id
        $doctor = \App\Http\Repositories\Web\AuthRepository::getUserByColumn('unique_id', $request->doctor_id);
        if (!$doctor || $doctor->role_id != self::ROLE_DOCTOR) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.invalid-role'));
        }

        $request['account_id'] = $doctor->account_id;

        // check if the user already recommended to this doctor once before
        if ($this->doctorRepository->checkIfRecommendationExists($user->id, $doctor->account_id, $request->receiver_serial)) {
            return self::jsonResponse(true, self::CODE_OK, trans('lang.doctor-recommended-successfully'));
        }

        $recommend_doctor = $this->doctorRepository->recommendDoctorAccount($user->id, $doctor->account_id, $request->receiver_serial);
        if ($recommend_doctor === false) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.something_wrong'));
        }
        return self::jsonResponse(true, self::CODE_OK, trans('lang.doctor-recommended-successfully'));
    }

    /**
     *  get list of Doctor Services
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDoctorServices(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|numeric|exists:users,id',
        ]);
        if ($validator->fails()) {
            return self::jsonResponse(false, self::CODE_VALIDATION, $validator->errors()->first(), $validator->errors());
        }
        $doctor = (new AuthRepository())->getUserById($request['doctor_id']);
        // then get list of Doctor Service
        // get doctor services
        $services = Service::join('account_service', 'account_service.service_id', 'services.id')
            ->where('account_id', $doctor->account_id)
            ->select('account_service.id', 'services.' . app()->getLocale() . '_name as name', 'account_service.price', 'account_service.premium_price')
            ->get();
        if (!$services) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.no_services'));
        }
        return self::jsonResponse(false, self::CODE_OK, trans('lang.total_services'), '', $services);
    }
}
