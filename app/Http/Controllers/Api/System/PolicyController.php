<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 11/17/18
 * Time: 3:26 PM
 */

namespace App\Http\Controllers\Api\System;


use App\Http\Controllers\ApiController;
use App\Http\Repositories\Web\PolicyRepository;
use Illuminate\Http\Request;
use Validator;

class PolicyController extends ApiController
{

    public function __construct(Request $request)
    {
        $this->setLang($request);
    }

    /**
     *  get list of policies
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function SitePolicies(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|min:0|max:5',
        ]);

        if ($validator->fails()) {
            return self::jsonResponse(false, self::CODE_VALIDATION, trans('lang.error-validation'), $validator->errors(), new \stdClass);
        }

        $policies = (new PolicyRepository())->getPoliciesForSite($request['type']);
        return self::jsonResponse(true, self::CODE_OK, trans('lang.policies'), [], $policies);
    }

}
