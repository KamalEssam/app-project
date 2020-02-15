<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Repositories\Api\TokenRepository;
use App\Http\Repositories\Validation\TokenValidationRepository;
use Illuminate\Http\Request;

class TokenController extends ApiController
{
    private $tokenRepository, $tokenValidationRepository;

    /**
     * DoctorController constructor.
     * @param Request $request
     * @param TokenRepository $tokenRepository
     * @param TokenValidationRepository $tokenValidationRepository
     */
    public function __construct(Request $request, TokenRepository $tokenRepository, TokenValidationRepository $tokenValidationRepository)
    {
        $this->tokenRepository = $tokenRepository;
        $this->tokenValidationRepository = $tokenValidationRepository;
        $this->setLang($request);
    }

    /**
     *  create new token or update the old record with the new Token
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function setToken(Request $request)
    {
        // validate fields
        if (!$this->tokenValidationRepository->setTokenValidation($request)) {
            return self::jsonResponse(false, self::CODE_VALIDATION, $this->tokenValidationRepository->getFirstError(), $this->tokenValidationRepository->getErrors());
        }

        $tokenRaw = $this->tokenRepository->setToken($request);

        if (!$tokenRaw) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.invalid-token'));
        }

        return self::jsonResponse(true, self::CODE_CREATED, trans('lang.token-created'));
    }
}
