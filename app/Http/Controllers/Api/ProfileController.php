<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Repositories\Api\ProfileRepository;
use App\Http\Repositories\Validation\ProfileValidationRepository;
use Illuminate\Http\Request;

class ProfileController extends ApiController
{
    private $profileRepository, $profileValidationRepository;

    /**
     * ProfileController constructor.
     * @param Request $request
     * @param ProfileRepository $profileRepository
     * @param ProfileValidationRepository $profileValidationRepository
     */
    public function __construct(Request $request, ProfileRepository $profileRepository, ProfileValidationRepository $profileValidationRepository)
    {
        $this->profileRepository = $profileRepository;
        $this->profileValidationRepository = $profileValidationRepository;
        $this->setLang($request);
    }

    /**
     *  get profile data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProfile(Request $request)
    {
        $user = auth()->guard('api')->user();
        // if not unauthorized
        if ($user == null) {
            return self::jsonResponse(false, self::CODE_UNAUTHORIZED, trans('lang.unauthorized'));
        }
        $profile = $this->profileRepository->getProfile($user);

        if ($profile == false) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.something_wrong'));
        }

        if ($user->is_premium == 1) {
            $profile->plan = $user->userPlan;
            $profile->plan->expiry_date = $user->expiry_date;
        } else {
            $profile->plan = null;
        }

        return self::jsonResponse(true, self::CODE_OK, trans('lang.profile'), new \stdClass(), $profile);
    }

    /**
     *  set profile image
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function setProfileImage(Request $request)
    {
        $user = auth()->guard('api')->user();
        // validate fields
        if (!$this->profileValidationRepository->setImageValidation($request)) {
            return self::jsonResponse(false, self::CODE_VALIDATION, $this->profileValidationRepository->getFirstError(), $this->profileValidationRepository->getErrors());
        }

        $image = $this->profileRepository->setImage($user, $request->image);

        if (!$image) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.error_image'));
        }
        return self::jsonResponse(true, self::CODE_OK, trans('lang.image-uploaded-successfully'), new \stdClass(), $user);
    }

    /**
     * edit profile
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function editProfile(Request $request)
    {
        $user = auth()->guard('api')->user();
        // if not unauthorized
        if ($user === null) {
            return self::jsonResponse(false, self::CODE_UNAUTHORIZED, trans('lang.unauthorized'));
        }
        $updated_profile = $this->profileRepository->editProfile($user, $request);

        if ($updated_profile == false) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.something_wrong'));
        }

        $profile = $this->getProfile($request);

        if ($user->is_premium == 1) {
            $profile->plan = $user->userPlan;
            $profile->plan->expiry_date = $user->expiry_date;
        } else {
            $profile->plan = null;
        }
        return self::jsonResponse(true, self::CODE_OK, trans('lang.profile-updated-successfully'), new \stdClass(), $profile);
    }

}
