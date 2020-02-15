<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 10/2/18
 * Time: 11:57 AM
 */

namespace App\Http\Repositories\Api;

use App\Http\Controllers\ApiController;
use App\Http\Interfaces\Api\PatientInterface;
use App\Models\Clinic;
use App\Models\User;
use DB;

class PatientRepository implements PatientInterface
{

    /**
     * get all patients related to this doctor
     * @param $doctor
     * @param $request
     * @return mixed
     */
    public function getPatientsRelatedToThisDoctor($doctor, $request)
    {
        $offset = (isset($request->offset) && !empty($request->offset)) ? $request->offset : 0;
        $limit = (isset($request->limit) && !empty($request->limit)) ? $request->limit : 10;
        try {
            if (isset($request->keyword) && !empty($request->keyword)) {
                return User::
                where('role_id', ApiController::ROLE_USER)
                    ->where(function ($query) use ($request) {
                        // In case of Search
                        if (isset($request->keyword) && !empty($request->keyword)) {
                            $query->where('unique_id', 'like', '%' . $request['keyword'] . '%')->orWhere('name', 'like', '%' . $request['keyword'] . '%');
                        }
                    })
                    ->select('id', 'unique_id', 'name', 'image')
                    ->distinct()
                    ->offset($offset)
                    ->limit($limit)
                    ->get();
            }
            return User::join('account_user', 'users.id', 'account_user.user_id')
                ->where('users.role_id', ApiController::ROLE_USER)
                ->where('account_user.account_id', $doctor->account_id)
                ->select('users.id', 'users.unique_id', 'users.name', 'users.image', 'users.is_premium', 'users.expiry_date')
                ->distinct()
                ->offset($offset)
                ->limit($limit)
                ->get();

        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * get all clinics related to this doctor
     * @param $doctor_id
     * @return mixed
     */
    public function getClinicsRelatedToDoctor($doctor_id)
    {
        return Clinic::where('created_by', $doctor_id)->get()->pluck('id');
    }
}
