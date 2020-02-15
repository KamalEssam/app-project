<?php

namespace App\Http\Controllers\Api\System;

use App\Http\Controllers\ApiController;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Validator;

class SubscriptionController extends ApiController
{
    public function __construct(Request $request)
    {
        $this->setLang($request);
    }

    public function subscription(Request $request)
    {
        $messages = [
            'email.unique' => trans('lang.already_subscribed'),
        ];
        // check validation
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:subscriptions',
        ], $messages);

        if ($validator->fails()) {
            return self::jsonResponse(false, 400, trans('lang.error-validation'), $validator->errors(), new \stdClass);
        }
        $subscription = Subscription::create($request->all());
        if (!$subscription) {
            return self::jsonResponse(false, 204, trans('lang.subscription-not-found'), [], new \stdClass);
        }
        return self::jsonResponse(true, 0, trans('lang.subscription-successfully'), [], new \stdClass);

    }
}
