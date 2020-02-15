<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 10/1/18
 * Time: 4:39 PM
 */

namespace App\Http\Repositories\Validation;


use App\Http\Interfaces\Validation\AuthValidationInterface;
use Illuminate\Http\Request;
use Validator;

class AuthValidationRepository extends ValidationRepository implements AuthValidationInterface
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function userIdValidation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);
        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }
        return true;
    }

    /**
     *  set signup validation
     *
     * @param Request $request
     * @return bool|mixed
     */
    public function signupValidation(Request $request)
    {
        $messages = [
            'mobile.unique' => 'The mobile number has already been taken',
            'email.unique' => 'The email has already been taken',
        ];
        // validate fields
        $validator = Validator::make($request->all(), [
            'social_id' => 'sometimes',
            'image' => 'sometimes',
            'type' => 'sometimes',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required',
            'name' => 'required',
            'mobile' => 'required|unique:users,mobile',
        ], $messages);

        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }

        return true;
    }

    /**
     *  set doctor signup validation
     *
     * @param Request $request
     * @return bool|mixed
     */
    public function doctorSignUpValidation(Request $request)
    {
        $messages = [
            'mobile.unique' => 'The mobile number has already been taken',
            'email.unique' => 'The email has already been taken',
        ];
        // validate fields
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'mobile' => 'required|phone_number|digits:11|unique:users,mobile',
            'type' => 'required|numeric|min:0|max:1',
        ], $messages);

        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }

        return true;
    }


    /**
     *  set the validation of the login
     *
     * @param Request $request
     * @return mixed
     */
    public function loginValidation(Request $request)
    {
        // check validation
        $validator = Validator::make($request->all(), [
            'login' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }

        return true;
    }

    /**
     *  set the validation of the login
     *
     * @param Request $request
     * @return mixed
     */
    public function doctorLoginValidation(Request $request)
    {
        // check validation
        $validator = Validator::make($request->all(), [
            'login' => 'required',
            'password' => 'required',
            'type' => 'required|numeric|min:0|max:1'
        ]);
        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }

        return true;
    }

    /**
     *
     *
     * @param Request $request
     * @return mixed
     */
    public function logoutValidation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'serial' => 'required',
        ]);

        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }
        return true;
    }

    /**
     * forget password validation
     * @param Request $request
     * @return mixed
     */
    public function forgetPasswordValidation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|exists:users,mobile',
        ]);
        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }
        return true;
    }

    /**
     * reset password validation
     * @param Request $request
     * @return mixed
     */
    public function resetPasswordValidation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|exists:users,mobile',
            'new_password' => 'required',
        ]);
        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }
        return true;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function socialLoginValidation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'social_id' => 'required',
            'type' => 'required',
            'mobile' => 'required',
        ]);
        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }
        return true;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function changePasswordValidation(Request $request)
    {
        // Validation area
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required',
        ]);
        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }
        return true;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function notificationStatusValidation(Request $request)
    {
        // Validation area
        $validator = Validator::make($request->all(), [
            'status' => 'required|min:0|max:1',
        ]);
        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }
        return true;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function doctorUpdateDate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ar_bio' => 'required',
            'en_bio' => 'required',
        ]);
        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }
        return true;
    }


    /**
     * @param Request $request
     * @return mixed
     */
    public function setServices(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'services' => 'required',
        ]);

        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }
        return true;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function verifyCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pin' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }
        return true;
    }
}
