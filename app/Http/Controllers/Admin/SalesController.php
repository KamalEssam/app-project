<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\WebController;
use App\Http\Repositories\Web\AuthRepository;
use App\Http\Repositories\Web\DoctorDetailsRepository;
use App\Http\Repositories\Web\SalesRepository;
use App\Http\Repositories\Web\UserRepository;
use App\Http\Requests\SalesRequest;
use App\Http\Traits\MailTrait;
use App\Models\AccountService;
use App\Models\DoctorDetail;
use App\Models\LogAccount;
use DB;
use Illuminate\Http\Request;

class SalesController extends WebController
{
    use MailTrait;

    protected $authRepository;

    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    /**
     * Display a listing of all sales agents in the application
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sales = $this->authRepository->getSalesAgents();
        return view('admin.rk-admin.sales.index', compact('sales'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SalesRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(SalesRequest $request)
    {
        DB::beginTransaction();
        //create user for the account
        try {
            // create new user
            $user = (new AuthRepository())->createSale($request, self::ACTIVE);
        } catch (\Exception $e) {
            DB::rollback();
            self::logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.user-not-found'), 'sales.index');
        }

        // update user unique id
        $user = (new AuthRepository())->updateUserColumn($user, 'unique_id', 'sales_' . ($user->id + 1000));

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
                return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.set_password_email_err'), 'sales.index');
            }
        }
        // in case all is ok
        DB::commit();
        // account added successfully
        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.sale_added_ok'), 'sales.index');
    }


    /**
     * Show the form for editing account data
     * @param  int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        $sale = AuthRepository::getUserByColumn('id', $id);
        return view('admin.rk-admin.sales.edit', compact('sale'));
    }

    /**
     * Update the account data
     *
     * @param SalesRequest $request
     * @param  int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(SalesRequest $request, $id)
    {
        try {
            $sales = AuthRepository::getUserByColumn('id', $id);
        } catch (\Exception $e) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.user-not-found'), 'sales.index');
        }

        $this->authRepository->updateUser($sales, $request);

        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.account_update_ok'), 'sales.index');
    }

    /**
     * Remove the specified user
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        return $this->deleteItem($this->authRepository->user, $id);
    }

    /**
     *  get logs of
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function logs()
    {
        // get all logs for sales accounts
        $logs = (new SalesRepository())->getSalesAddedAccounts();
        return view('admin.rk-admin.sales.logs', compact('logs'));

    }

    /**
     *  return complete list of this sale agent added accounts
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function accounts(Request $request)
    {
        $status = 0;
        if ($request->has('status')) {
            $status = $request->get('status');
        }
        $accounts = (new SalesRepository())->getCurrentSalesAddedAccounts(auth()->user()->id, $status);

        return view('admin.sale.accounts.index', compact('accounts'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function accountSteps(Request $request)
    {
        $user_id = $request->get('user_id');
        $user = (new UserRepository())->getUserById($user_id);
        $status = array();

        // image
        if ($user->getOriginal('image') == 'default.png') {
            $status[] = false;
        } else {
            $status[] = true;
        }

        // Bio
        $account_details = DoctorDetail::where('account_id', $user->account_id)->first();

        if (
            $account_details->ar_bio == '' ||
            $account_details->en_bio == '' ||
            $account_details->en_bio == 'No Data To Show' ||
            $account_details->ar_bio == 'لا توجد بيانات للعرض'
        ) {
            $status[] = false;
        } else {
            $status[] = true;
        }

        // active
        if ($user->login_counter == 0) {
            $status[] = false;
        } else {
            $status[] = true;
        }

        // services
        $services = AccountService::where('account_id', $user->account_id)->get();

        if (count($services) == 0) {
            $status[] = false;
        } else {
            $status[] = true;
        }

        return response()->json($status);
    }

}
