<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\WebController;
use App\Http\Repositories\Web\AuthRepository;
use App\Http\Repositories\Web\DoctorDetailsRepository;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\SetPasswordRequest;
use App\Http\Traits\MailTrait;
use Carbon\Carbon;
use Hash;
use Illuminate\Http\Request;
use DB;

class AdminController extends WebController
{
    use MailTrait;

    /**
     * check if first time login to start tour
     */
    public function checkFirstTimeTour(Request $request)
    {
        $doctor_details_account = (new DoctorDetailsRepository)->getDoctorDetailsByAccountId();
        if (!$doctor_details_account) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.failed_get_account'));
        }

        if ($doctor_details_account[$request->column_name] == self::FALSE) {
            try {
                $doctor_details_account[$request->column_name] = self::TRUE;
                $doctor_details_account->update();
            } catch (\Exception $e) {
                DB::rollBack();
                return 'false';
            }
            DB::commit();
            return 'true';
        }
        return 'false';
    }

    /**
     *  change password route
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getChangePasswordForm()
    {
        return view('auth.passwords.change');
    }

    /**
     *  change password backend side
     *
     * @param ChangePasswordRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function postChangePassword(ChangePasswordRequest $request)
    {
        // check if old password match user password in database
        if (password_verify($request['old'], auth()->user()->password)) {
            // update user password to new one
            $user = (new AuthRepository())->setPassword(auth()->user(), $request['new']);
            if (!$user) {
                return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.failed-update-password'));
            }

            auth()->logout();
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.password-changed-successfully'), 'login');

        } else {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.old-password-not-match'));
        }
    }

    /**
     *  set password form
     *
     *  show set password form
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function getPasswordForm($id)
    {
        $user = AuthRepository::getUserByColumn('unique_id', $id);
        if (!$user) {
            return view('auth.passwords.congratulations', ['alert' => 'danger', 'msg' => 'user not found']);
        }

        if ($user->role_id == self::ROLE_USER) {

            if ($user->password != NULL) {
                // redirect with message that you already set password
                return view('auth.passwords.congratulations', ['alert' => 'danger', 'msg' => 'you already set your password']);
            }

            // redirect to create password page
            return view('auth.passwords.user_create', compact('user'));
        } else {
            if ($user->password != NULL) {
                if (auth()->check()) {
                    auth()->login($user);
                    return redirect('/admin');
                } else {
                    return redirect()->route('login');
                }
            }
            return view('auth.passwords.admin_create', compact('user'));
        }
    }

    /**
     * @param SetPasswordRequest $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     * @throws \Exception
     */
    public function setPassword(SetPasswordRequest $request, $id)
    {
        $user = AuthRepository::getUserByColumn('unique_id', $id);
        if (!$user) {
            return view('auth.passwords.congratulations', ['alert' => 'danger', 'msg' => 'user not found']);
        }

        if ($user->role_id == self::ROLE_USER) {
            $request2 = new Request();
            $request2['password'] = $request['password'];
            $request2['name'] = $request['name'];
            $request2['email'] = $request['email'];
            $user = (new AuthRepository())->updateUser($user, $request2);
            if (!$user) {
                return view('auth.passwords.congratulations', ['alert' => 'danger', 'msg' => trans('lang.failed-update-password')]);
            }

            return view('auth.passwords.congratulations', ['alert' => 'success', 'msg' => 'password set successfully']);

        } else {
            $user = (new AuthRepository())->setPassword($user, $request['password']);
            if (!$user) {
                return view('auth.passwords.congratulations', ['alert' => 'danger', 'msg' => trans('lang.failed-update-password')]);
            }

            auth()->login($user);
        }

        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.password-updated-successfully'), 'admin');
    }

    /**
     *  activate the user
     *
     * @param Request $request
     * @param $uid
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ActivateUser(Request $request, $uid)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);

        $user = AuthRepository::getUserByColumn('unique_id', $uid);

        if (!$user) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.user-not-found'));
        }

        // get the 
        $hash_modified = Carbon::parse($user->created_at)->addDays(2);
        if (Carbon::now() > $hash_modified) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.link-expired'));
        }

        if (!Hash::check($hash_modified, $request['token'])) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.invalid-token'));
        }

        // activate the user
        $user->is_active = self::ACTIVE;
        $user->save();


        session()->flash("success", trans('lang.user-activate-successfully'));

        return redirect()->back();
    }

    /**
     *  resend activation link
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendActivationLink()
    {
        $user = auth()->user();
        $user->created_at = Carbon::now("Africa/Cairo")->toDateTimeString();
        $user->save();

        if (!$user) {
            auth()->logout();
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.user-not-found'));
        }
        try {
            $data = [
                'user' => $user,
                'subject' => 'Activate Account',
                'view' => 'emails.activate-account',
                'to' => 'm.aman@rkanjel.com',
            ];
            $this->sendMailTraitFun($data);
            return $this->messageAndRedirect(self::STATUS_OK, trans('lang.activation_email_sent'));
        } catch (\Exception $e) {
            DB::rollback();
            self::logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.user-activation-failed'));
        }
    }
}
