<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\WebController;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends WebController
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function logout()
    {
        auth()->logout();
        return redirect('/');
    }

    /**
     * Validate the user login request.
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            'login' => 'required|string',
            'password' => 'required|string',
        ]);
    }

    public function username()
    {
        // request()->merge([$field => $login]);
        return request()->input('email');
    }

    /**
     * Handle a login request to the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws mixed
     */
    public function login(Request $request)
    {
        if (auth()->user()) {
            $this->logout();
        }
        //  $this->validateLogin($request);
        $this->validateLogin($request);

        $login = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';

        if (auth()->attempt([$login => request('login'), 'password' => request('password')])) {

            // store number of member logged in
            auth()->user()->increment('login_counter');
            return redirect('/');
        }
        session()->flash('error', 'password or username is wrong');
        return redirect()->back();
    }
}
