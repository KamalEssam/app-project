<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:48 PM
 */

namespace App\Http\Repositories\Web;


use App\Http\Controllers\WebController;
use App\Http\Interfaces\Web\ClinicInterface;
use App\Models\Clinic;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Collection;

class ClinicRepository extends ParentRepository implements ClinicInterface
{
    protected $clinic;

    public function __construct()
    {
        $this->clinic = DB::table('clinics');
    }

    /**
     *  get clinic belongs to doctor and ordered by (created_at)
     *
     * @param $account_id
     * @return mixed
     */
    public function getDoctorClinicsOrdered($account_id)
    {
        try {
            return Clinic::where('account_id', $account_id)->orderBy('created_at')->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return new Collection([]);
        }
    }


    /**
     *  get ids of account clinics
     *
     * @param $account_id
     * @return mixed
     */
    public function getIdsOfAccountClinics($account_id)
    {
        try {
            return Clinic::where('account_id', $account_id)->pluck('id')->toArray();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return new Collection([]);
        }
    }


    /**
     *  create new clinic (usually used by doctor)
     *
     * @param $request
     * @return mixed
     */
    public function createClinic($request)
    {
        try {
            return Clinic::create($request->all());
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get clinic by id
     *
     * @param $id
     * @return mixed
     */
    public static function getClinicById($id)
    {
        try {
            return Clinic::find($id);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  update the clinic data in case of Doctor And Assistant
     *
     * @param $clinic
     * @param $is_doctor
     * @param $request
     * @param $auth_id
     * @return mixed
     */
    public function updateClinicInDoctorAndAssistant($clinic, $is_doctor, $request, $auth_id)
    {
        try {
            if ($is_doctor == true) {
                $clinic->pattern = $request->pattern;
                $clinic->res_limit = $request->res_limit;
                $clinic->fees = $request->fees;
                $clinic->follow_up_fees = $request->follow_up_fees;
                $clinic->province_id = $request->province_id;
                $clinic->mobile = $request->mobile;

                if (!isset($request->vat_included)) {
                    $clinic->vat_included = 0;
                } elseif (isset($request->vat_included)) {
                    $clinic->vat_included = 1;
                }

                // if user us premium
                if (auth()->user()->is_premium == 1) {
                    $clinic->premium_fees = $request->premium_fees;
                    $clinic->premium_follow_up_fees = $request->premium_follow_up_fees;
                }

                if (auth()->user()->account->type == 0) {
                    $clinic->en_address = $request->en_address;
                    $clinic->ar_address = $request->ar_address;
                    $clinic->lat = $request->lat;
                    $clinic->lng = $request->lng;
                } else {
                    $clinic->en_name = $request->en_name;
                    $clinic->ar_name = $request->ar_name;
                    $clinic->speciality_id = $request->speciality_id;
                    $clinic->avg_reservation_time = $request->avg_reservation_time;
                    $clinic->reservation_deadline = $request->reservation_deadline;
                }
            } else {
                $clinic->avg_reservation_time = $request->avg_reservation_time;
                $clinic->reservation_deadline = $request->reservation_deadline;
            }
            $clinic->update();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }

        return $clinic;
    }

    /**
     * @param $account_id
     * @param int $except
     * @return mixed
     */
    public function getCurrentMinFees($account_id, $except = -1)
    {
        try {
            return $this->clinic->where('account_id', $account_id)
                ->where(function ($query) use ($except) {
                    if ($except != -1) {
                        $query->where('id', '!=', $except);
                    }
                })
                ->select(DB::raw('min(fees) as fees'), DB::raw('min(premium_fees) as premium_fees'))->first();

        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     * @param $account_id
     * @return mixed
     */
    public function getCurrentMinFeesWithPremium($account_id)
    {
        try {
            return $this->clinic->where('account_id', $account_id)
                ->select(DB::raw('min(fees) as fees'), DB::raw('min(premium_fees) as premium_fees'))->first();

        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }


    /**
     *  set status to premium request
     *
     * @param $branch_id
     * @param $value_fees
     * @param $value_fees_follow
     * @return mixed
     */
    public function updateClinicPremiumPrice($branch_id, $value_fees, $value_fees_follow)
    {
        try {
            $clinic = $this->clinic->where('id', $branch_id)->update([
                'premium_fees' => $value_fees,
                'premium_follow_up_fees' => $value_fees_follow,
            ]);
            return $clinic;
        } catch (\Exception $e) {
            \DB::rollBack();
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  update poly account clinics with lat and lang from
     *
     * @param $account_id
     * @param $lat
     * @param $lng
     */
    public function updatePolyClinicsWithLatAndLng($account_id, $lat, $lng)
    {
        $clinics = Clinic::where('account_id', $account_id)->get();
        foreach ($clinics as $clinic) {
            $clinic->update([
                'lat' => $lat,
                'lng' => $lng,
            ]);
        }
    }
}
