<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\WebController;
use App\Http\Repositories\Web\DoctorDetailsRepository;
use App\Http\Repositories\Web\SpecialityRepository;
use Illuminate\Http\Request;

class SponsoredController extends WebController
{
    private $specialityRepository;

    public function __construct(SpecialityRepository $specialityRepository)
    {
        $this->specialityRepository = $specialityRepository;
    }

    /**
     *  show all specialities with sponsored
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $specialities = $this->specialityRepository->getAllSpecialitiesWithSponsored();
        return view('admin.rk-admin.sponsord_doctors.index', compact('specialities'));
    }

    /**
     *   add doctor to featured doctors in speciality
     *
     * @param $id
     * @return mixed
     */
    public function add($id)
    {
        if ($id == null) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.no_specialities'));
        }

        // get speciality and number of sponsored doctors
        $speciality = $this->specialityRepository->getSpecialityWithSponsord($id);

        if (!$speciality) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.no_specialities'));
        }

        // check if not of doctors is less than five
        if ($speciality->doctors < 5) {

            $speciality_id = $speciality->id;
            $not_sponsor = 5 - $speciality->doctors;
            $doctors = \DB::table('accounts')
                ->join('doctor_details', 'accounts.id', 'doctor_details.account_id')
                ->join('users', 'accounts.id', 'users.account_id')
                ->whereNotIn('accounts.id',
                    \DB::table('doctor_details')
                        ->where('speciality_id', $speciality_id)
                        ->where('featured_rank', '!=', 0)
                        ->pluck('account_id')
                )
                ->where('users.role_id', self::ROLE_DOCTOR)
                ->where('accounts.is_published', 1)
                ->where('doctor_details.speciality_id', $speciality_id)
                ->pluck(app()->getLocale() . '_name', 'accounts.id');

            return view('admin.rk-admin.sponsord_doctors.add', compact('speciality_id', 'not_sponsor', 'doctors'));
        }
        return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.sponsored-doctors-completed'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $accounts = $request['doctor_id'];

        foreach ($accounts as $account) {
            (new DoctorDetailsRepository())->updateColumn($account, 'featured_rank', 5);
        }
        return redirect()->route('sponsored.doctors', $request['speciality_id']);
    }

    public function doctors($id)
    {
        if ($id == null) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.no_specialities'));
        }

        // get speciality and number of sponsored doctors
        $doctors = $this->specialityRepository->getSponsoredDoctorsBySpecialityId($id);

        if (!$doctors) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.no_specialities'));
        }

        return view('admin.rk-admin.sponsord_doctors.view', compact('doctors', 'id'));
    }

    /**
     *  remove sponsored doctor from this speciality
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function remove($id)
    {
        try {
            $this->specialityRepository->removeSponsorDoctor($id);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return response()->json(['msg' => false], 200);
        }
        return response()->json(['msg' => true], 200);
    }

    /**
     *  change doctor rank
     *
     * @param $id
     * @param $staus
     * @return \Illuminate\Http\RedirectResponse
     */
    public function rank($id, $staus)
    {
        (new DoctorDetailsRepository())->updateRank($id, $staus);
        return redirect()->back();
    }
}
