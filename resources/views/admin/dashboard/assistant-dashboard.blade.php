@php
    $clinic = \App\Models\Clinic::where('id', auth()->user()->clinic_id)->first();
        $approved_reservations = \App\Models\Reservation::
        join('working_hours','reservations.working_hour_id', 'working_hours.id')
                                               ->where('reservations.clinic_id', auth()->user()->clinic_id)
                                               ->where('reservations.status', \App\Http\Controllers\WebController::R_STATUS_APPROVED)
                                               ->where('reservations.day', \App\Http\Traits\DateTrait::getDateByFormat(\App\Http\Traits\DateTrait::getToday(), 'Y-m-d'))
                                               ->orderBy('reservations.created_at')
                                               ->take(5)
                                               ->get();
/******************************************************queue********************************************************/
        // get current queue information
        $queue = (new App\Http\Repositories\Web\ClinicQueueRepository)->getClinicQueueByClinic(auth()->user()->clinic_id);
         /*******************************************current queue ****************************/
         if($queue){
   $current_queue_reservation =  (new App\Http\Repositories\Web\ReservationRepository)->getReservationByStatusAndClinic([\App\Http\Controllers\WebController::R_STATUS_APPROVED, \App\Http\Controllers\WebController::R_STATUS_ATTENDED, \App\Http\Controllers\WebController::R_STATUS_MISSED], $clinic->id, $queue->queue);

        if ($current_queue_reservation) {
            $current_queue_patient =  (new App\Http\Repositories\Web\AuthRepository())->getUserByColumn('id', $current_queue_reservation->user_id);
        }
       /*******************************************next queue ****************************/
       $next_queue_reservation =  (new App\Http\Repositories\Web\ReservationRepository)->getNextReservationInQueue(\App\Http\Controllers\WebController::R_STATUS_APPROVED, $clinic->id, $queue->queue);
        if ($next_queue_reservation) {
            $next_queue_patient =  (new App\Http\Repositories\Web\AuthRepository())->getUserByColumn('id', $next_queue_reservation->user_id);
        }
}

@endphp
<div class="page-content">
    <div class="page-header">
        <h1 class="text-center">{{ trans('lang.dashboard') }}</h1>
    </div>
    <div id="assistant-dashboard">
        <div class="infobox-container">
            <div class="row">
                <div class="col-sm-6 col-sm-offset-3 ">
                    <div class="infobox infobox-green">
                        <div class="infobox-icon">
                            <i class="dash-icon fa fa-calendar-alt"></i>
                        </div>

                        <div class="infobox-data">
                            <span class="infobox-data-number">{{ \App\Models\Reservation::
                                             where('clinic_id', auth()->user()->clinic_id)
                                           ->where('status', \App\Http\Controllers\WebController::R_STATUS_MISSED)
                                           ->where('day', \App\Http\Traits\DateTrait::getDateByFormat(\App\Http\Traits\DateTrait::getToday(), 'Y-m-d'))
                                           ->count() }}</span>
                            <div class="infobox-content">Today's Missed</div>
                        </div>
                    </div>

                    <div class="infobox infobox-blue">
                        <div class="infobox-icon">
                            <i class="dash-icon fas fa-users"></i>
                        </div>

                        <div class="infobox-data">
                            <span class="infobox-data-number">{{ \App\Models\Reservation::
                                             where('reservations.clinic_id', auth()->user()->clinic_id)
                                           ->where('reservations.status', \App\Http\Controllers\WebController::R_STATUS_APPROVED)
                                           ->where('day', \App\Http\Traits\DateTrait::getDateByFormat(\App\Http\Traits\DateTrait::getToday(), 'Y-m-d'))
                                           ->count() }}</span>
                            <div class="infobox-content">Queue Count</div>
                        </div>
                    </div>

                    <div class="infobox infobox-pink">
                        <div class="infobox-icon">
                            <i class="dash-icon fa fa-calendar-alt"></i>
                        </div>

                        <div class="infobox-data">
                            <span class="infobox-data-number">{{ \App\Models\Reservation::
                                             where('reservations.clinic_id', auth()->user()->clinic_id)
                                           ->where('reservations.status', \App\Http\Controllers\WebController::R_STATUS_CANCELED)
                                           ->where('day', \App\Http\Traits\DateTrait::getDateByFormat(\App\Http\Traits\DateTrait::getToday(), 'Y-m-d'))
                                           ->count() }}</span>
                            <div class="infobox-content">Today's Canceled</div>
                        </div>
                    </div>
                </div>
            </div>

            @if($approved_reservations->count() > 0)
                <div class="hr hr32 hr-dotted"></div>

                <div class="row">
                    <div class="col-sm-6 col-sm-offset-3">
                        <div class="widget-box transparent">
                            <div class="widget-header widget-header-flat">
                                <h4 class="widget-title lighter loon">
                                    <i class="ace-icon fa fa-star orange"></i>
                                    {{ trans('lang.last_reservations') }}
                                </h4>
                            </div>
                            <div class="widget-body">
                                <div class="widget-main no-padding">
                                    <table class="table table-bordered table-striped">
                                        <thead class="thin-border-bottom">
                                        <tr>
                                            <th class="center grey font-18">{{ trans('lang.name') }}</th>
                                            <th class="center grey font-18">{{ trans('lang.type') }}</th>
                                            <th class="center grey font-18">{{ trans('lang.status') }}</th>
                                            @if($clinic->pattern == 0)
                                                {{-- in case of intervals show user appointment --}}
                                                <th class="center grey font-18">{{ trans('lang.time') }}</th>
                                            @else
                                                {{-- show the user number in the queue --}}
                                                <th class="center grey font-18">{{ trans('lang.queue') }}</th>
                                            @endif
                                        </tr>
                                        </thead>

                                        <tbody>
                                        @foreach($approved_reservations as $reservation)
                                            @php
                                                /*get user who related to reservation*/
                                                    $user  =  \App\Models\User::where('id',$reservation->user_id)->first();
                                            @endphp
                                            <tr>
                                                <td class="center loon font-18">{{ ($user->name)}}</td>
                                                <td class="center loon font-18">{{ ($reservation->type == \App\Http\Controllers\WebController::TYPE_CHECK_UP) ? trans('lang.check_up') : trans('lang.follow_up')}}</td>
                                                <td class="center loon font-18" id="status{{ $reservation->id }}">
                                                    @include('admin.common.reservations.status')
                                                </td>
                                                @if($clinic->pattern == \App\Http\Controllers\WebController::PATTERN_INTERVAL)
                                                    {{-- in case of intervals show user appointment --}}
                                                    <th class="center loon font-18">{{ Super::getProperty(\App\Http\Traits\DateTrait::getDateByFormat($reservation->time,'h:i:a')) }}</th>
                                                @else                                            {{-- show the user number in the queue --}}
                                                <th class="center loon font-18">{{ Super::getProperty($reservation->queue) }}</th>
                                                @endif

                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div><!-- /.widget-main -->
                            </div><!-- /.widget-body -->
                        </div><!-- /.widget-box -->
                    </div><!-- /.col -->

                </div>
            @endif

            @if($queue)
                <div class="hr hr32 hr-dotted"></div>
                <div class="row">
                    <div class="col-sm-6 col-sm-offset-3">
                        <div class="widget-header widget-header-flat">
                            <h4 class="widget-title lighter loon">
                                <i class="ace-icon fa fa-star orange"></i>
                                {{ trans('lang.queue') }}
                            </h4>
                        </div>
                        <div class="tabbable">
                            <ul class="nav nav-tabs" id="myTab">
                                <li class="active">
                                    <a data-toggle="tab" href="#queue-in" class="loon font-18">
                                        {{ trans('lang.current_queue') }}
                                    </a>
                                </li>

                                <li>
                                    <a data-toggle="tab" href="#queue-next" class="loon font-18">
                                        {{ trans('lang.next_queue') }}
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <div id="queue-in" class="tab-pane fade in active">
                                    @if(isset($current_queue_patient) && isset($current_queue_reservation) && $queue)
                                        <div class="row mt-70" id="patient-info">
                                            <div class="col-md-8 col-md-offset-2">
                                                <div class="panel panel-default panel-no-border">
                                                    <div class="panel-heading bolder">{{ trans('lang.patient_info') }}</div>
                                                    <div class="panel-body">
                                                        <div class="mb-10" id="id">
                                                            <span class="font-18 bolder"> {{ trans('lang.id') . " " .":". " " }}</span>
                                                            <span class="grey"
                                                                  id="id-data">{{ Super::getProperty( $current_queue_patient->unique_id ) }}</span>
                                                        </div>
                                                        <div class="mb-10" id="name">
                                                            <span class="font-18 bolder"> {{ trans('lang.name') . " " .":". " " }}</span>
                                                            <span class="grey"
                                                                  id="name-data">{{ Super::getProperty( $current_queue_patient->name ) }}</span>
                                                        </div>
                                                        <div class="mb-10" id="mobile">
                                                            <span class="font-18 bolder"> {{ trans('lang.mobile') . " " .":". " " }}</span>
                                                            <span class="grey"
                                                                  id="mobile-data">{{ Super::getProperty( $current_queue_patient->mobile ) }}</span>
                                                        </div>
                                                        <div class="mb-10" id="email">
                                                            <span class="font-18 bolder"> {{ trans('lang.email') . " " .":". " " }}</span>
                                                            <span class="grey"
                                                                  id="email-data">{{ Super::getProperty( $current_queue_patient->email ) }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <p class="empty">{{ trans('no_queue') }}</p>
                                    @endif
                                </div>

                                <div id="queue-next" class="tab-pane fade">
                                    @if(isset($next_queue_patient) && isset($next_queue_reservation) && $queue)
                                        <div class="row mt-70" id="patient-info">
                                            <div class="col-md-8 col-md-offset-2">
                                                <div class="panel panel-default panel-no-border">
                                                    <div class="panel-heading bolder">{{ trans('lang.patient_info') }}</div>
                                                    <div class="panel-body">
                                                        <div class="mb-10" id="id">
                                                            <span class="font-18 bolder"> {{ trans('lang.id') . " " .":". " " }}</span>
                                                            <span class="grey"
                                                                  id="id-data">{{ Super::getProperty( $next_queue_patient->unique_id ) }}</span>
                                                        </div>
                                                        <div class="mb-10" id="name">
                                                            <span class="font-18 bolder"> {{ trans('lang.name') . " " .":". " " }}</span>
                                                            <span class="grey"
                                                                  id="name-data">{{ Super::getProperty( $next_queue_patient->name ) }}</span>
                                                        </div>
                                                        <div class="mb-10" id="mobile">
                                                            <span class="font-18 bolder"> {{ trans('lang.mobile') . " " .":". " " }}</span>
                                                            <span class="grey"
                                                                  id="mobile-data">{{ Super::getProperty( $next_queue_patient->mobile ) }}</span>
                                                        </div>
                                                        <div class="mb-10" id="email">
                                                            <span class="font-18 bolder"> {{ trans('lang.email') . " " .":". " " }}</span>
                                                            <span class="grey"
                                                                  id="email-data">{{ Super::getProperty( $next_queue_patient->email ) }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            @endif
        </div>
    </div>
</div>