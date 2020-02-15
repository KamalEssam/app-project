<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\WebController;
use App\Http\Repositories\Web\AccountRepository;
use App\Http\Repositories\Web\AuthRepository;
use App\Http\Requests\BrandRequest;
use App\Http\Traits\MailTrait;
use DB;

class BrandController extends WebController
{
    use MailTrait;

    protected $authRepository;

    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    /**
     * Display a listing of all brands in the application
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $brands = $this->authRepository->getBrands();
        return view('admin.rk-admin.market-place.brands.index', compact('brands'));
    }

    /**
     *  Store a newly created brand in database.
     *
     * @param BrandRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(BrandRequest $request)
    {
        DB::beginTransaction();
        //create user for the account
        try {
            // create new user
            $user = (new AuthRepository())->createBrand($request, self::ACTIVE);
        } catch (\Exception $e) {
            DB::rollback();
            self::logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.user-not-found'), 'brands.index');
        }

        // update user unique id


        $request['unique_id'] = 'BRAND_' . ($user->id + 1000);

        //create account after user to fire event first to get account counter
        try {
            $account = (new AccountRepository())->createAccount($request);
        } catch (\Exception $e) {
            DB::rollBack();
            self::logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.account_add_err'), 'accounts.index');
        }

        $user->account_id = $account->id;
        $user->unique_id = $account->unique_id;

        $user->update();

        // if password of user not found send message to set password
        if ($user->password == NULL) {
            // include try and catch in sending email in case something went wrong
            try {
                $data = [
                    'user' => $user,
                    'subject' => 'Set your account password',
                    'view' => 'emails.setPassword',
                    'to' => $user->email,
                ];
                $this->sendMailTraitFun($data);
            } catch (\Exception $e) {
                DB::rollback();
                self::logErr($e->getMessage());
                return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.set_password_email_err'), 'brands.index');
            }
        }
        // in case all is ok
        DB::commit();
        // account added successfully
        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.brand_added_ok'), 'brands.index');
    }

    /**
     * Remove the specified user
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        return $this->deleteItem($this->authRepository->user, $id);
    }
}
