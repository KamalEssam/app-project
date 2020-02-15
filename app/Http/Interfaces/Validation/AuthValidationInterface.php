<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 10/1/18
 * Time: 4:33 PM
 */

namespace App\Http\Interfaces\Validation;


use Illuminate\Http\Request;

interface AuthValidationInterface
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function userIdValidation(Request $request);

    /**
     *   set the validation of sign up
     *
     * @param Request $request
     * @return mixed
     */
    public function signupValidation(Request $request);

    /**
     *   set the validation of sign up
     *
     * @param Request $request
     * @return mixed
     */
    public function doctorSignUpValidation(Request $request);

    /**
     *  set the validation of the login
     *
     * @param Request $request
     * @return mixed
     */
    public function loginValidation(Request $request);

    /**
     *  set the validation of the logout
     *
     * @param Request $request
     * @return mixed
     */
    public function logoutValidation(Request $request);

    /**
     * forget password validation
     * @param Request $request
     * @return mixed
     */
    public function forgetPasswordValidation(Request $request);

    /**
     * reset password validation
     * @param Request $request
     * @return mixed
     */
    public function resetPasswordValidation(Request $request);

    /**
     * @param Request $request
     * @return mixed
     */
    public function socialLoginValidation(Request $request);

    /**
     * @param Request $request
     * @return mixed
     */
    public function changePasswordValidation(Request $request);


    /**
     * @param Request $request
     * @return bool
     */
    public function notificationStatusValidation(Request $request);

    /**
     * @param Request $request
     * @return mixed
     */
    public function doctorUpdateDate(Request $request);


    /**
     * @param Request $request
     * @return mixed
     */
    public function setServices(Request $request);
}
