<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 10/2/18
 * Time: 11:57 AM
 */

namespace App\Http\Repositories\Api;

use App\Http\Controllers\ApiController;
use App\Http\Interfaces\Api\ProfileInterface;
use App\Http\Traits\FileTrait;
use App\Models\User;
use DB;


class ProfileRepository implements ProfileInterface
{

    use FileTrait;

    protected function profileCompleted($user)
    {
        $profile_real_data = DB::table('users')->where('id', $user->id)->select('email', 'mobile', 'gender', 'name', 'unique_id', 'address', 'birthday', 'weight', 'height', 'image')->first();

        $completed = 0;
        $data = [$profile_real_data->gender => '15', $profile_real_data->email => '15', $profile_real_data->mobile => '20', $profile_real_data->address => '20', $profile_real_data->birthday => '10', $profile_real_data->weight => '10', $profile_real_data->height => '10'];

        foreach ($data as $i => $item) {
            if ($i != null) {
                $completed += $item;
            }
        }
        return $completed;
    }

    /**
     * get user data
     * @param $user
     * @return mixed
     */
    public function getProfile($user)
    {
        $profile = $this->getProfileCustomData($user);
        try {
            // add completed to profile
            $completed = $this->profileCompleted($user);;
            $profile->completed = $completed;
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }

        return $profile;
    }

    /**
     * edit user data
     * @param $user
     * @param $request
     * @return mixed
     * @throws \Exception
     */
    public function editProfile($user, $request)
    {
        try {
            if ($request->mobile) {
                unset($request['mobile']);
            }
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }
        DB::beginTransaction();
        try {
            $user->update($request->all());
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiController::catchExceptions($e->getMessage());
        }
        DB::commit();
        $updated_profile = $this->getProfileCustomData($user);

        // add completed to profile
        try {
            $updated_profile->completed = $this->profileCompleted($user);
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }
        return $updated_profile;
    }

    /**
     * set profile image
     * @param $user
     * @param $image
     * @return mixed
     * @throws \Exception
     */
    public function setImage($user, $image)
    {
        $old_image = $user->getOriginal('image');
        $image_path = 'assets/images/profiles/' . $user->unique_id . '/';

        // Check Path (if not exists => create it)
        if (!file_exists(getcwd() . '/' . $image_path)) {
            mkdir(getcwd() . '/' . $image_path, 0777, true);
        }
        // delete the image
        if ($old_image != 'default.png') {
            $is_deleted = FileTrait::deleteFile($image_path . $old_image);
            if (!$is_deleted) {
                return false;
            }
        }
        $image = FileTrait::uploadFile64($image, $image_path);
        if (!$image) {
            return false;
        }

        DB::beginTransaction();
        try {
            $user->image = $image;
            $user->update();
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiController::catchExceptions($e->getMessage());
        }

        DB::commit();
        return $image;
    }

    /**
     * get user data with custom data
     * @param $user
     * @return mixed
     */
    public function getProfileCustomData($user)
    {
        try {
            $profile = User::where('id', $user->id)
                ->select(
                    'id',
                    'email',
                    'mobile',
                    'gender',
                    'name',
                    'unique_id',
                    'address',
                    'birthday',
                    'weight',
                    'height',
                    'image',
                    'is_premium',
                    'is_active',
                    'points',
                    'cash_back',
                    'expiry_date'
                )
                ->withCount('myFavouriteDoctors as followed_doctors')
                ->first();
            if (!$profile) {
                return false;
            }
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }
        if (is_null($profile->birthday)) {
            $profile->birthday = " ";
        }
        return $profile;
    }
}
