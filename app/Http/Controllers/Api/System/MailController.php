<?php

namespace App\Http\Controllers\Api\System;

use App\Http\Controllers\ApiController;
use App\Http\Traits\MailTrait;
use App\Rules\GoogleRecaptcha;
use Illuminate\Http\Request;
use Validator;

class MailController extends ApiController
{
    public function __construct(Request $request)
    {
        $this->setLang($request);
    }

    use MailTrait;

    public function contactUs(Request $request)
    {
        // check validation

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'name' => 'required',
            'mobile' => 'required|regex:/(01)[0-9]{9}/',
            'message' => 'required',
            'g-recaptcha-response' => ['required', new GoogleRecaptcha]
        ]);

        if ($validator->fails()) {
            return self::jsonResponse(false, 400, trans('lang.error-validation'), $validator->errors(), new \stdClass);
        }

        $data = [
            //m.aman@rkanjel.com
            'to' => 'm.aman@rkanjel.com',
            'email' => $request->email,
            'name' => $request->name,
            'mobile' => $request->mobile,
            'message' => $request->message,
            'view' => 'emails.contact',
            'title' => 'rKlinic Contact Form',
            'subject' => 'New Contact Message',
        ];

        try {
            $this->sendContactMail($data);
        } catch (\Exception $e) {
            return self::jsonResponse(false, 400, trans('lang.send_email_failed'), $validator->errors(), new \stdClass);
        }
        return self::jsonResponse(true, 0, trans('lang.mail-send-successfully'), [], new \stdClass);

    }
}