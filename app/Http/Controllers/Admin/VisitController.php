<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\WebController;
use App\Http\Repositories\Web\ReservationRepository;
use App\Http\Repositories\Web\VisitRepository;
use Illuminate\Http\Request;
use App\Models\Attachment;
use App\Models\Clinic;
use App\Models\Comment;
use App\Models\Reservation;
use App\Models\WorkingHour;
use App\Models\User;
use App\Models\Visit;
use DB;

class VisitController extends WebController
{
    private $visit;

    public function __construct(VisitRepository $visitRepository)
    {
        $this->visit = $visitRepository;
    }

    /**
     *  filter visits using date or name or both
     *  paginate 13 result
     *
     * @param string $date
     * @param string $name
     * @return mixed
     */
    public function getFilteredVisit($date = '', $name = '')
    {
        $visits = $this->visit->filterVisitByDateAndPatientName(auth()->user(), $date, $name);
        if ($visits) {
            return view('admin.common.visits.table-visits', compact('visits'));
        }
    }

    /**
     *  get list of doctor patients
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $visits = $this->visit->getAllVisitsForDoctorAccount(auth()->user()->account_id);
        return view('admin.common.visits.index', compact('visits'));
    }

    public function show($user_id)
    {
        // get collection of patient reservations
        $reservations = (new ReservationRepository())->getUSerReservationsByStatus($user_id, [self::R_STATUS_APPROVED, self::R_STATUS_ATTENDED]);
        if (!$reservations) {
            $this->messageAndRedirect(self::STATUS_ERR, trans('lang.reservation-not-found'));
        }
        return view('admin.common.visits.show', compact('reservations'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $reservation_id
     * @return \Illuminate\Http\Response
     */
    public function create($reservation_id)
    {
        // find clinic,reservation and time that belong to this visit
        // get reservation to create visit for it
        $reservation = Reservation::where('id', $reservation_id)->first();
        if (!$reservation) {
            abort(404, 'Reservation Not Found');
        }
        $user = User::where('id', $reservation->user_id)->first();
        if (!$user) {
            abort(404, 'User Not Found');
        }
        $attachments = Attachment::where('user_id', $user->id)->get();
        if (!$attachments) {
            abort(404, 'Attachments Not Found');
        }
        $clinic = Clinic::where('id', $reservation->clinic_id)->first();
        if (!$clinic) {
            abort(404, 'Clinic Not Found');
        }
        $working_hour = WorkingHour::where('id', $reservation->working_hour_id)->first();
        if (!$working_hour) {
            abort(404, 'Working Hour Not Found');
        }
        //return user,attachments,clinic,reservation and time that belong to this visit
        $visit = new \stdClass();
        $visit->user_name = $user->name;
        $visit->unique_id = $user->unique_id;
        $visit->clinic_name = $clinic[app()->getLocale() . '_address'];
        $visit->reservation_day = $reservation->day;
        $visit->reservation_id = $reservation->id;
        $visit->time = $working_hour->time;
        $visit->attachments = $attachments;

        return view('admin.common.visits.create', compact('visit'));

    }


    /**
     *  Store new visit by doctor
     *
     * @param Request $request
     * @param $reservation_id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(Request $request, $reservation_id)
    {
        $this->validate($request, [
            'diagnosis' => 'required',
        ]);
        // get reservation to create visit for it
        $reservationReo = (new ReservationRepository());
        $auth_user = auth()->user();

        $reservation = $reservationReo->getReservationById($reservation_id, [self::R_STATUS_APPROVED, self::R_STATUS_ATTENDED]);
        if (!$reservation) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.reservation-not-found'));
        }

        DB::beginTransaction();
        // set checkout date
        try {
            $reservation = $reservationReo->addReservationCheckInAndOut($reservation, $auth_user->id, 1);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.reservation_update_err'));
        }

        // get user to create visit for him

        // create Visit
        $request['user_id'] = $reservation->user_id;
        $request['clinic_id'] = $reservation->clinic_id;
        $request['reservation_id'] = $reservation->id;
        try {
            $visit = $this->visit->createVisit($request);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.visit_add_err'));
        }

        if (!$visit) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.visit_add_err'));
        }

        try {
            //create Comments to this visit
            if ($request->commnts) {
                $comments = $request->commnts;
                foreach ($comments as $i => $comment) {
                    if ($comments[$i] != null) {
                        $visit_comment = new Comment();
                        $visit_comment->comment = $comments[$i];
                        $visit_comment->visit_id = $visit->id;
                        $visit_comment->created_by = auth()->user()->id;
                        $visit->comments()->save($visit_comment);
                    }
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.comment-added-err'));
        }


        try {
            // attach medications to this visit
            if (isset($request->medications)) {
                $visit->medications()->attach($request->medications);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.medications_add_err'));
        }

        DB::commit();
        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.visit_add_ok'));
    }

    public function edit($id)
    {
        //find visit to update it
        $visit = Visit::find($id);
        if (!$visit) {
            abort(404, 'Visit Not Found');
        }
        //find medications that belong to this visit
        $medications = DB::table('medications')
            ->join('medication_visit', 'medications.id', '=', 'medication_visit.medication_id')
            ->join('visits', 'medication_visit.visit_id', '=', 'visits.id')
            ->where('visits.id', $visit->id)
            ->select('medications.*')
            ->get();
        if (!$medications) {
            abort(404, 'Medications Not Found');
        }
        //find comments that belong to this visit
        $comments = DB::table('comments')
            ->join('visits', 'comments.visit_id', '=', 'visits.id')
            ->where('visits.id', $visit->id)
            ->select('comments.*')
            ->get();
        if (!$comments) {
            abort(404, 'comments Not Found');
        }
        //find user that belong to this visit and his attachments
        $user = User::where('id', $visit->user_id)->first();
        if (!$user) {
            abort(404, 'User Not Found');
        }
        $attachments = Attachment::where('user_id', $user->id)->get();
        if (!$attachments) {
            abort(404, 'Attachments Not Found');
        }
        //find clinic,reservation and time that belong to this visit
        $clinic = Clinic::where('id', $visit->clinic_id)->first();
        if (!$clinic) {
            abort(404, 'Clinic Not Found');
        }
        $reservation = Reservation::where('id', $visit->reservation_id)->first();
        if (!$reservation) {
            abort(404, 'Reservation Not Found');
        }
        if ($reservation->working_hour_id) {
            $working_hour = WorkingHour::where('id', $reservation->working_hour_id)->first();
            if (!$working_hour) {
                abort(404, 'WorkingHour Not Found');
            }
            $visit->time = $working_hour->time;
        }
        //return user,attachments,clinic,reservation and time that belong to this visit
        $visit->user_name = $user->name;
        $visit->unique_id = $user->unique_id;
        $visit->clinic_name = $clinic[app()->getLocale() . '_address'];
        $visit->reservation_day = $reservation->day;
        $visit->attachments = $attachments;

        return view('admin.common.visits.edit', compact('visit', 'medications', 'comments'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\SettingsRequest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!$request->isMethod('PATCH')) {
            abort(404, 'UnAuthorized');
        } else {
            //find visit to update it
            $visit = Visit::find($id);
            if (!$visit) {
                abort(404, 'Visit Not Found');
            }
            //update Visit
            $visit->update($request->all());
            $visit->updated_by = auth()->user()->id;
            $visit->update();

            if ($request->visit_comments) {
                $comments = $request->visit_comments;

                foreach ($visit->comments as $comment) {
                    $comment->delete();
                }
                //dd($request->all());
                foreach ($comments as $i => $comment) {

                    if ($comments[$i] != NULL) {
                        $visit_comment = new Comment();
                        $visit_comment->comment = $comments[$i];
                        $visit_comment->visit_id = $visit->id;
                        $visit_comment->created_by = auth()->user()->id;
                        $visit_comment->updated_by = auth()->user()->id;
                        $visit->comments()->save($visit_comment);
                    }
                }
            }
            // attach permissions to this user
            if (isset($request->medications)) {
                $visit->medications()->sync($request->medications);
            }
            // return success message ...
            Flashy::message('Visit updated successfully');
            return redirect()->route('visits.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //find visit
        $visit = Visit::find($id);
        if (!$visit) {
            abort(404, 'Visit Not Found');
        }
        //delete visit
        $visit->delete();
        return response()->json(['msg' => true], 200);
    }

}
