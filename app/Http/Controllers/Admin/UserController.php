<?php

namespace App\Http\Controllers\Admin;

use App\Http\Repositories\Web\UserRepository;
use App\Rules\ArabicText;
use App\Rules\EnglishText;
use Illuminate\Http\Request;
use App\Http\Controllers\WebController;
use DB;

class UserController extends WebController
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * get profile data
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {

        $profile = $this->userRepository->getUserById(auth()->user()->id);

        // get doctor data if not admin or super admin
        if ($profile->role_id != self::ROLE_RK_ADMIN && $profile->role_id != self::ROLE_RK_SUPER_ADMIN && $profile->role_id != self::ROLE_RK_SALES) {
            $profile = $this->userRepository->getDoctorAccountData($profile, self::METHOD_INDEX);
        }
        return view('admin.profile.index', compact('profile'));
    }

    /**
     * edit profile data
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $profile = $this->userRepository->getUserById($id);
        // get doctor data if not admin or super admin
        if (auth()->user()->role_id != self::ROLE_RK_ADMIN && auth()->user()->role_id != self::ROLE_RK_SUPER_ADMIN) {
            $profile = $this->userRepository->getDoctorAccountData($profile, self::METHOD_EDIT);
        }
        return view('admin.profile.edit', compact('profile'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function update(Request $request, $id)
    {
        $profile = $this->userRepository->getUserById($id);
        // update profile
        $updated_profile = $this->userRepository->updateProfileData($profile, $request);
        if ($updated_profile === false) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.profile-updated-failed'));
        }
        // if doctor update his details
        if ($profile->role_id == self::ROLE_DOCTOR) {
            $this->userRepository->updateDoctorDetails($profile, $request);
        }
        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.profile_updated_successfully'), 'profile.index');
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function uploadUserCroppedImage(Request $request)
    {
        if ($request->image) {

            $file = str_replace(array('data:image/jpeg;base64,', 'data:image/jpg;base64,', 'data:image/png;base64,', ' '), array('', '', '', '+'), $request->image);
            $data = base64_decode($file);
            $image_path = 'assets/images/profiles/' . auth()->user()->unique_id . '/';
            // Check Path (if not exists => create it)
            if (!file_exists(getcwd() . '/' . $image_path)) {
                mkdir(getcwd() . '/' . $image_path, 0777, true);
            }

            $imageName = str_random(6) . '_' . time() . '.' . 'png'; // file name based on time

            DB::beginTransaction();
            try {
                file_put_contents($image_path . $imageName, $data);
                auth()->user()->update([
                    'image' => $imageName
                ]);
                DB::commit();
                return response()->json(['status' => true], 200);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['status' => false], 200);
            }
        }
    }
}
