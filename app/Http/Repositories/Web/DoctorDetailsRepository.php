<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:48 PM
 */

namespace App\Http\Repositories\Web;


use App\Http\Interfaces\Web\DoctorDetailInterface;
use App\Models\DoctorDetail;

class DoctorDetailsRepository extends ParentRepository implements DoctorDetailInterface
{
    protected $doctor_details;

    public function __construct()
    {
        $this->doctor_details = new DoctorDetail();
    }

    /**
     *  get the doctor details by account_id
     *
     * @param string $account_id
     * @return mixed
     */
    public function getDoctorDetailsByAccountId($account_id = '')
    {
        try {
            if (empty($account_id)) {
                return $this->doctor_details->where('account_id', auth()->user()->account_id)->first();
            }
            return $this->doctor_details->where('account_id', $account_id)->first();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  create speciality id
     *
     * @param $account_id
     * @param $speciality_id
     * @return mixed
     */
    public function createDoctorDetail($account_id, $speciality_id)
    {
        try {
            $this->doctor_details->create([
                'account_id' => $account_id,
                'speciality_id' => $speciality_id,
                'en_bio' => 'No Data To Show',
                'ar_bio' => 'لا توجد بيانات للعرض',
            ]);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  update min fees AND premium min fee
     *
     * @param int $except
     * @return mixed
     */
    public function updateMinFees($except = -1)
    {

        $doctorDetails = (new DoctorDetailsRepository())->getDoctorDetailsByAccountId();
        // update min fees
        // get all clinics
        try {
            $clinics = (new ClinicRepository())->getCurrentMinFees(auth()->user()->account_id, $except);
            if ($clinics) {
                $fees = $clinics->fees;
                $premium_fees = $clinics->premium_fees;
                $doctorDetails->update([
                    'min_fees' => $fees,
                    'min_premium_fees' => $premium_fees
                ]);

                return $doctorDetails;
            }
            return false;
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }


    /**
     * @param $doctorDetails
     * @param $ar_bio
     * @param $en_bio
     * @return bool
     */
    public function updateBio($doctorDetails, $ar_bio, $en_bio)
    {
        try {
            $doctorDetails->update([
                'ar_bio' => $ar_bio,
                'en_bio' => $en_bio,
            ]);

            return $doctorDetails;
        } catch (\Exception $e) {
            return false;
        }
    }


    /**
     * @param $account_id
     * @param $column
     * @param $value
     * @return bool
     */
    public function updateColumn($account_id, $column, $value)
    {
        try {
            $this->doctor_details->where('account_id', $account_id)->update([
                $column => $value
            ]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updateRank($account_id, $status)
    {
        if ($status) {
            $this->doctor_details->where('account_id', $account_id)->increment('featured_rank', 1);
        } else {
            $this->doctor_details->where('account_id', $account_id)->decrement('featured_rank', 1);
        }

        return true;
    }

}
