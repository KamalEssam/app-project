<?php

namespace App\Http\Controllers\Admin;

use App\Events\UserGenerated;
use App\Http\Controllers\WebController;
use App\Http\Repositories\Web\AuthRepository;
use App\Http\Repositories\Web\SettingRepository;
use App\Http\Requests\PatientRequest;
use App\Http\Traits\SmsTrait;
use App\Models\RegisteredDoctors;
use App\Models\User;
use DB;
use Illuminate\Http\Request;
use Flashy;
use Super;
use Event;

class PatientController extends WebController
{
    use SmsTrait;

    /**
     *  get list of user according to the account
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $auth_user = auth()->user();
        $patients = User::where('role_id', self::ROLE_USER)
            ->whereIn('id',
                DB::table('account_user')
                    ->where('account_id', $auth_user->account_id)
                    ->pluck('user_id')
                    ->toArray())
            ->where(function ($query) {
                // in case of debug mode Dont show Test Users
                if (debug_mode() == true) {
                    $query->whereNotIn('id', get_test_users('patient'));
                }
            })
            ->get();
        return view('admin.common.patients.index', compact('patients'));
    }


    public function edit($id)
    {
        $profile = User::find($id);
        if (!$profile) {
            abort(404, 'User not found');
        }
        if ($profile->mobile) {
            $mobiles = $profile->mobile;
            $profile->mobile = explode(",", $mobiles);
        }
        return view('admin.profile.edit', compact('profile'));
    }

    public function update(PatientRequest $request, $id)
    {
        if (!$request->isMethod('PATCH')) {
            abort('405');
        }
        try {
            $auth_user = auth()->user();
            if (!$auth_user) {
                abort(404, 'User not found');
            }
            $profile = User::find($id);
            if (!$profile) {
                abort(404, 'User not found');
            }
            $profile->update($request->all());
            $profile->updated_by = $auth_user->id;
            $profile->update();

            if ($request->mobiles) {
                $mobiles = $request->mobiles;
                // remove null values from mobiles array
                foreach ($mobiles as $i => $mobile) {
                    if (!$mobile) {
                        unset($mobiles[$i]);
                    }
                }
                $profile->mobile = implode(",", $mobiles);
                $profile->update();
            }

            if ($request->image) {
                if ($profile->image != 'default.png') {
                    $is_deleted = Super::deleteFile('assets/images/profiles' . $profile->image);
                    if (!$is_deleted) {
                        abort('500');
                    }
                }
                $image = Super::uploadFile($request->file('image'), 'assets/images/profiles');
                if (!$image) {
                    abort('500');
                }
                $profile->image = $image;
                $profile->update();
            }

            Flashy::message('Profile updated successfully');
            return redirect()->route('profile.index');

        } catch (\Exception $ex) {
            abort(500);
        }

    }

    /**
     * store from model in reservations form
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function storeInReservation(Request $request)
    {
        $auth_user = auth()->user();

        DB::beginTransaction();
        // create a new patient
        $patient = (new AuthRepository())->createUSer($request, !self::ACTIVE);
        if (!$patient) {
            DB::rollBack();
            return response()->json(['status' => false, 'code' => 0], 200);
        }

        try {
            // generate a new unique id for this user and update all counters
            Event::fire(new UserGenerated(self::ROLE_USER, $patient));
        } catch (\Exception $e) {
            DB::rollBack();
            $this->logErr($e->getMessage());
            return response()->json(['status' => false, 'code' => 0], 200);
        }

        $setting = SettingRepository::getFirstSetting();
        if (!$setting) {
            DB::rollBack();
            return response()->json(['status' => false, 'code' => 0], 200);
        }
        try {
            $patient->unique_id = (999 + $setting->user_counter);
            $patient->is_active = 1;
            $patient->created_by = $auth_user->id;
            $patient->account_id = $auth_user->account_id;
            $patient->update();
        } catch (\Exception $e) {
            DB::rollBack();
            self::logErr($e->getMessage());
            return response()->json(['status' => false, 'code' => 0], 200);
        }

        if ($patient->password == NULL) {

            $link = route('getPasswordForm', ['id' => $patient->unique_id]);
            if (app()->getLocale() == 'en') {
                $msg = 'you have been added by clinic dr ' . $auth_user->account['en_name'] . ', you can now review your visits please complete registration steps ' . $link;
                $lang = self::LANG_EN;
            } else {
                $msg = ' تم اضافتك من قبل عيادة الدكتور' . $auth_user->account['ar_name'] . ' يمكنك الان متابعة زياراتك برجاء استكمال التسجبل ' . $link;
                $lang = self::LANG_AR;
            }

            // send SMS Message
            try {
                self::sendRklinicSmsMessage($patient->mobile, $msg, $lang);
            } catch (\Exception $e) {
                DB::rollBack();
                self::logErr($e->getMessage());
                return response()->json(['status' => false, 'code' => 0], 200);
            }
        }
        // Activate the current Doctor
        try {
            RegisteredDoctors::create([
                'account_id' => $auth_user->account_id,
                'user_id' => $patient->id,
                'active' => 1,
            ]);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return response()->json(['status' => false, 'code' => 0], 200);
        }

        DB::commit();
        return response()->json(['status' => true, 'user' => $patient, 'code' => 0], 200);
    }

//    /*****************************Ajax Routs*************************************************
    /*
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateMobile(Request $request)
    {
        // check if patient already exists by mobile
        if ($request->id) {
            $patient = User::where('mobile', $request->mobile)->where('id', '<>', $request->id)->first();
        } else {
            $patient = User::where('mobile', $request->mobile)->first();
        }

        if (!$patient) {
            return response()->json(['status' => true, 'code' => 404], 200);
        }
        return response()->json(['status' => false, 'msg' => 'mobile is already token', 'code' => 0], 200);
    }

    /**
     * get list of all patients in the application for Super Admin
     *
     */
    public function allPatients()
    {
        $patients = User::where('role_id', self::ROLE_USER)
            ->where(function ($query) {
                // in case of debug mode Dont show Test Users
                if (debug_mode() == true) {
                    $query->whereNotIn('id', get_test_users('patient'));
                }
            })->get();

        return view('admin.rk-admin.patients.index', compact('patients'));
    }
}
