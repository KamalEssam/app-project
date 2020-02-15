<?php

namespace App\Http\Controllers\Admin;

use App\Events\UserGenerated;
use App\Http\Controllers\WebController;
use App\Http\Repositories\Web\AssistantRepository;
use App\Http\Repositories\Web\AuthRepository;
use App\Http\Repositories\Web\SettingRepository;
use App\Http\Requests\AssistantRequest;
use App\Http\Traits\MailTrait;
use App\Http\Traits\SmsTrait;
use DB;
use Event;
use Illuminate\Http\Request;

class AssistantController extends WebController
{
    use MailTrait, SmsTrait;

    /**
     *  show list of all assistants working for the doctor
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index()
    {
        // auth user
        $auth = auth()->user();
        // get the assistants that works for the $doctor_account
        $assistants = (new AssistantRepository())->getAssistantsByAccount($auth->account_id);
        return view('admin.doctor.assistants.index', compact('assistants'));
    }

    /**
     *  doctor adds the assistant in the database
     *
     * @param AssistantRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(AssistantRequest $request)
    {
        $auth_user = auth()->user();

        DB::beginTransaction();
        try {
            // create a new assistant
            $assistant = (new AuthRepository())->createUSer($request, self::ACTIVE);
        } catch (\Exception $e) {
            DB::rollback();
            self::logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.assistant_add_err'), 'assistants.index');
        }

        try {
            // generate a new unique id for this user and update all counters
            Event::fire(new UserGenerated(self::ROLE_ASSISTANT, $assistant));
        } catch (\Exception $e) {
            DB::rollback();
            self::logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.assistant_add_err'), 'assistants.index');
        }

        // get the first setting
        $setting = SettingRepository::getFirstSetting();
        if (!$setting) {
            DB::rollback();
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.setting-found'), 'assistants.index');
        }

        try {
            $assistant->unique_id = "RK_CO_" . (999 + $setting->assistant_counter);
            $assistant->is_active = 1;
            $assistant->created_by = $auth_user->id;
            $assistant->account_id = $auth_user->account_id;
            $assistant->update();

        } catch (\Exception $e) {
            DB::rollback();
            self::logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.assistant_add_err'), 'assistants.index');
        }
//        // send password message TODO remove where merge
//        // generate assistant password
//        $generated_password = self::generatePassword();
//        // update password
//        try {
//            $assistant->password = $generated_password;
//            $assistant->update();
//        } catch (\Exception $e) {
//            DB::rollback();
//            self::logErr($e->getMessage());
//            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.assistant_add_err'), 'assistants.index');
//        }
//        if (app()->getLocale() == 'en') {
//            $msg = $auth_user->name . ' has added you as an assistant, you can access your account with credentials username:' . ' ' . $assistant->mobile . '& password:' . ' ' . $generated_password;
//            $lang = self::LANG_EN;
//        } else {
//            $msg = 'لقد قام ' . $auth_user->name . 'باضافتك كمساعدة له فى التطبيق برجاء الدخول على صفحتكم عن طريق البيانات التالية اسم المستخدم: ' . ' ' . $assistant->mobile . '& كلمة المرور:' . ' ' . $generated_password;
//            $lang = self::LANG_AR;
//        }
//
//        // send SMS Message
//        try {
//            self::sendRklinicSmsMessage($assistant->mobile, $msg, $lang);
//        } catch (\Exception $e) {
//            DB::rollback();
//            self::logErr($e->getMessage());
//            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.assistant_add_err'), 'assistants.index');
//        }
        /*
                try {
                    $data = [
                        'user' => $assistant,
                        'subject' => 'Set your account password',
                        'view' => 'emails.setPassword',
                        'to' => $assistant->email,
                    ];
                    $this->sendMailTraitFun($data);
                } catch (\Exception $e) {
                    DB::rollback();
                    self::logErr($e->getMessage());
                    return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.set_password_email_err'), 'assistants.index');
                }*/
        DB::commit();
        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.assistant_add_ok'), 'assistants.index');
    }

    // used for ajax request to get data of assistant then bind it to Modal
    public function edit($id)
    {
        $assistant = AuthRepository::getUserByColumn('id', $id);
        if (!$assistant) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.assistant_not_found'), 'assistants.index');
        }
        return view('admin.doctor.assistants.edit', compact('assistant'));
    }


    /**
     * update the assistant data
     *
     * @param AssistantRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(AssistantRequest $request, $id)
    {
        $assistant = AuthRepository::getUserByColumn('id', $id);
        if (!$assistant) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.assistant_not_found'));
        }

        DB::beginTransaction();
        // update the assistant data
        try {
            unset($request['mobile']);
            (new AssistantRepository)->updateAssistant($assistant, $request);
        } catch (\Exception $e) {
            DB::rollback();
            self::logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.assistant_update_err'), 'assistants.index');
        }

        DB::commit();
        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.assistant_update_ok'), 'assistants.index');
    }

    /**
     *  delete the assistant
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        // TODO remember to remove the image
        return $this->deleteItem((new AuthRepository())->user, $id);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function resetPassword(Request $request)
    {
        $auth_user = auth()->user();

        // check if assistant exist
        $assistant = AuthRepository::getUserByColumn('id', $request->id);
        if (!$assistant) {
            return response()->json(['status' => false]);
        }
        // generate assistant password
        $generated_password = self::generatePassword();
        // update password
        DB::beginTransaction();
        try {
            $assistant->password = $generated_password;
            $assistant->update();
        } catch (\Exception $e) {
            DB::rollback();
            self::logErr($e->getMessage());
            return response()->json(['status' => false]);
        }
        if (app()->getLocale() == 'en') {
            $msg = 'Dr. ' . $auth_user->name . ' has changed your login password to:' . ' ' . $generated_password;
            $lang = self::LANG_EN;
        } else {
            $msg = 'قد قام د. ' . $auth_user->name . 'باعادة تعيين كلمة السر الخاصة بكم و هى :' . ' ' . $generated_password;
            $lang = self::LANG_AR;
        }

        // send SMS Message
        try {
            self::sendRklinicSmsMessage($assistant->mobile, $msg, $lang);
        } catch (\Exception $e) {
            DB::rollback();
            self::logErr($e->getMessage());
            return response()->json(['status' => false]);
        }
        DB::commit();

        return response()->json(['status' => true]);

    }

}
