<?php
/*  get clinic name*/
$clinic_id = ($auth->role_id == \App\Http\Controllers\WebController::ROLE_ASSISTANT) ? $auth->clinic_id : Request::get('clinic');
$clinic = \App\Http\Repositories\Web\ClinicRepository::getClinicById($clinic_id);
?>

@if(count($reservations) > 0)
    <div class="table-responsive">
        <table id="dynamic-table" class="table table-striped table-bordered {{--table-hover--}}">
            <thead>
            <tr>
                <th class="center" style="width: 50px !important;">{{ trans('lang.image') }}</th>
                <th class="center">{{trans('lang.name')}}</th>

                @if(Request::segment(3) != "today")
                    <th class="center">{{trans('lang.date')}}</th>
                @endif

                <th class="center">{{trans('lang.type')}}</th>
                @if($auth->role_id == $role_doctor && $auth->account->type == \App\Http\Controllers\WebController::ACCOUNT_TYPE_SINGLE)
                    <th class="center">{{ trans('lang.time_queue') }}</th>
                @endif
                @if($auth->role_id == $role_doctor || $auth->role_id == $role_assistant)
                    {{-- payment section --}}
                    <th class="center">{{ trans('lang.payment') }}</th>
                    <th class="center">{{ trans('lang.fees') }}</th>
                    {{-- payment section --}}
                    @if($clinic->pattern == 0)
                        {{-- in case of intervals show user appointment --}}
                        <th class="center">{{ trans('lang.time') }}</th>
                    @endif
                    @if(Request::segment(2) != "attended" || Request::segment(2) != "canceled" || Request::segment(2) != "missed")
                        <th class="center">{{trans('lang.controls')}}</th>
                    @endif
                @endif

            </tr>
            </thead>

            <tbody id="table" class="t-content">
            @foreach($reservations as $reservation)
                @php
                    $user  =  \App\Models\User::find($reservation->user_id);
                    $visit =  \App\Models\Visit::where('reservation_id',$reservation->id)->where('user_id',$reservation->user_id)->first();
                    $visit_user =  \App\Models\Visit::where('user_id',$reservation->user_id)->first();
                @endphp

                <tr>

                    <td class="center">
                        <div class="premium-container">
                            @if($user->image)
                                <img src="{{ $user->image }}" class="premium-image"
                                     style="">
                                @if ($user->is_premium == 1)
                                    <img src="{{ asset('assets/images/premium.png') }}" alt=""
                                         class="premium-icon">
                                @endif
                            @else
                                {{ trans('lang.n/a') }}
                            @endif
                        </div>
                    </td>

                    <td class="center">
                        @if($visit_user)
                            <a title={{trans('lang.view_history')}} href="{{route('visits.show' , [$user->id])}}">{{ ($user->name)}}</a>
                        @else
                            {{ ($user->name)}}
                        @endif
                    </td>

                    @if(Request::segment(3) != "today")
                        <td class="center">{{ ($reservation->day) }}</td>
                    @endif
                    <td class="center">
                        @if($reservation->type == 0)
                            <p>{{ trans('lang.check_up') }}</p>
                        @elseif($reservation->type == 1)
                            <p>{{ trans('lang.follow_up') }}</p>
                        @endif
                    </td>

                    @if($auth->role_id == $role_doctor && $auth->account->type == \App\Http\Controllers\WebController::ACCOUNT_TYPE_SINGLE)
                        <td class="center">{{ $reservation->time ? $reservation->time : $reservation->queue }}</td>
                    @endif

                    @if($auth->role_id == $role_doctor || $auth->role_id == $role_assistant)
                        {{-- Payments  --}}
                        <td class="center">
                            @if($reservation->transaction_id == -1)
                                {{-- in case of not paid --}}
                                <p>NotPaid</p>
                            @else
                                <p>Paid</p>
                            @endif
                        </td>

                        {{-- Payments amount of money --}}
                        <td class="center">
                            @php
                                $paid = (new \App\Http\Repositories\Api\ReservationRepository())->getReservationFeesAfterReservation($reservation->id);
                            @endphp
                            @if (is_object($paid))
                                <p>{{ $paid->total_fees }} EGP</p>
                            @else
                                <p>not set</p>
                            @endif
                        </td>

                        {{--get reservation time--}}
                        @if($clinic->pattern == 0)
                            {{-- in case of intervals show user appointment --}}
                            <th class="center">{{ Super::getProperty(\App\Http\Traits\DateTrait::getDateByFormat($reservation->time,'h:i:a')) }}</th>
                        @endif
                        @if(Request::segment(2) != "attended" || Request::segment(2) != "canceled" || Request::segment(2) != "missed")
                            <td class="center control-icon overflow-hidden" id="change_status">
                                @if($auth->account->type == \App\Http\Controllers\WebController::ACCOUNT_TYPE_POLY && $reservation->status == \App\Http\Controllers\WebController::R_STATUS_APPROVED)
                                    @include('admin.common.reservations.change-status')
                                @endif
                                @if($reservation->transaction_id == -1 && $reservation->payment_method == 0 && $reservation->status != \App\Http\Controllers\WebController::R_STATUS_CANCELED)
                                    {{-- payment in case of cash--}}
                                    <a class="pay_cash" data-id="{{ $reservation->id }}"><i
                                            class="ml-10 fas fa-dollar-sign bigger-120 loon"></i></a>
                                @endif
                            </td>
                        @endif
                    @endif
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="row mt-100">
        <div class="col-xs-2 ml-38felmaya mb-20"><img src="{{ asset('assets/images/no_data/no_reservations.png') }}">
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 col-xs-6 ml-35felmaya"><p class="loon no_data">{{trans('lang.no_reservations')}}</p></div>
    </div>
@endif


@push('more-scripts')
    {!! Html::script('assets/js/admin/jquery.dataTables.min.js') !!}
    {!! Html::script('assets/js/admin/jquery.dataTables.bootstrap.min.js') !!}
    {!! Html::script('assets/js/admin/smart-tables.js') !!}
@endpush
