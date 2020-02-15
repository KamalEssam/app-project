@extends('layouts.admin.admin-master')

@section('title', trans('lang.manage_reservations'))

@section('styles')
    <style>
        .control-icon {
            width: 200px !important;
        }
    </style>
@stop

@section('content')
    <div class="page-content">
        <div class="page-header">
            <!-- top bar -->
            <div class="row">
                <div class="col-md-3 col-xs-12 res-style">
                    <h1>{{trans('lang.manage_reservations')}}</h1>
                </div>

                <div class="col-md-3 col-xs-12 res-style">
                    {{ Form::select('reservationStatus', ['all' => 'all','approved' => 'Confirmed', 'today' => 'Today', 'canceled' => 'Canceled','attended' => 'Attended','missed' => 'Missed',] , Request::segment(2),[ 'class'=>'form-control', 'id'=>'reservationStatus']) }}
                </div>

                <!-- start search -->
                <div class="col-md-2 col-xs-12 res-style" style="position: relative">
                    <!--if today don't show the reservation date search -->
                    <input type="text" value=""
                           class="day day-media form-control date"
                           placeholder="{{ trans('lang.search_by_reservation_date') }}" name="search"
                           id="search">
                </div>

                <!-- This code is to search by user name -->
                <div class="col-md-3 col-xs-12 res-style align-right">
                    <input type="text" class="search form-control day-media "
                           autocomplete="off"
                           placeholder="{{ trans('lang.search_by_username') }}"
                           name="search">
                </div>

                <!-- add reservation | only assistant can make reservations-->
                <div class="col-md-1 col-xs-12 res-style align-right p-0">
                    <a href="{{ route('reservations.create' ,['clinic'=>  Request::get('clinic')]) }}"
                       class="btn btn-sm btn-primary btn-admin-media "><i
                            class="fa fa-plus"></i> {{trans('lang.add_reservation')}}</a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <div class="clearfix">
                    <div class="pull-right tableTools-container"></div>
                </div>

                <div id="table-reservations">
                    @if(count($reservations) > 0)
                        <div class="table-responsive">
                            <table id="{{ $auth->role_id == $role_doctor ? 'dynamic-table' : '' }}"
                                   class="table table-striped table-bordered {{ $auth->role_id == $role_doctor ? 'table-hover' : '' }}">
                                <thead>
                                <tr>
                                    <th class="center" style="width: 50px !important;">{{ trans('lang.image') }}</th>
                                    <th class="center">{{ trans('lang.name') }}</th>

                                    <!-- if today don't show reservation sate-->
                                    @if(Request::segment(2) != "today")
                                        <th class="center">{{ trans('lang.date') }}</th>
                                    @endif

                                <!-- reservation type (checkup, follow up)-->
                                    <th class="center">{{ trans('lang.type') }}</th>

                                    <!-- show status when reservation today or all and for assistant only-->
                                    @if($auth->role_id == $role_doctor || $auth->role_id == $role_assistant)
                                    <!-- show controls for assistant-->
                                        {{-- payment section --}}
                                        <th class="center">{{ trans('lang.payment') }}</th>
                                        <th class="center">{{ trans('lang.fees') }}</th>

                                        @if($clinic->pattern == 0)
                                            {{-- in case of intervals show user appointment --}}
                                            <th class="center">{{ trans('lang.time') }}</th>
                                        @endif

                                        @if(Request::segment(2) == "all" || Request::segment(2) == "today" ||
                                    Request::segment(2) == "pending" || Request::segment(2) == "approved")
                                            <th class="center">{{ trans('lang.controls') }}</th>
                                        @endif
                                    @endif

                                </tr>
                                </thead>

                                <tbody id="table" class="t-content">
                                @foreach($reservations as $reservation)

                                    @php
                                        /*get user who related to reservation*/
                                            $user  =  \App\Models\User::where('id',$reservation->user_id)->first();
                                            /*get history of reservation*/
                                            $visit =  \App\Models\Visit::where('reservation_id',$reservation->id)->where('user_id',$reservation->user_id)->first();
                                            /*  for know if user have any history*/
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
                                            {{--  if user have any history make it link  --}}
                                            @if($visit_user)
                                                <a title={{trans('lang.view_history')}} href="{{route('visits.show' , [$user->id])}}">{{ ($user->name)}}</a>
                                            @else
                                                {{ ($user->name)}}
                                            @endif
                                        </td>

                                        {{--if not today get reservation date--}}
                                        @if(Request::segment(2) != "today")
                                            <td class="center">{{ ($reservation->day) }}</td>
                                        @endif
                                        <td class="center control-icon">
                                            {{--show reservation type--}}
                                            @if($reservation->type == 0)
                                                {{ trans('lang.check_up') }}
                                            @elseif($reservation->type == 1)
                                                {{ trans('lang.follow_up') }}
                                            @endif
                                            {{-- if doctor ( he only show reservation type not status so he see add and
                                            edit visit in type tab )--}}
                                            {{--                                            @if($auth->role_id == $role_doctor)--}}
                                            {{--                                                @if($reservation->status == \App\Http\Controllers\WebController::R_STATUS_ATTENDED)--}}
                                            {{--                                                    @if($visit)--}}
                                            {{--                                                        <a title={{trans('lang.edit_visit')}} href="{{route('visits.edit' , [$visit->id])}}"><i--}}
                                            {{--                                                                class="ml-10 ace-icon fa fa-edit bigger-120  edit">--}}
                                            {{--                                                            </i></a>--}}
                                            {{--                                                    @else--}}
                                            {{--                                                        <a title={{trans('lang.add_visit')}} href="{{route('visits.create', [$reservation->id])}}"><i--}}
                                            {{--                                                                class="ml-10 ace-icon fas fa-plus bigger-120 add "></i></a>--}}
                                            {{--                                                    @endif--}}
                                            {{--                                                @endif--}}
                                            {{--                                            @endif--}}
                                        </td>

                                        {{--show reservation status and can edit or add visit--}}

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
                                                <p>{{ $paid->total_fees ?? 0 }} EPG</p>
                                            @else
                                                <p>not set</p>
                                            @endif
                                        </td>
                                        @if($clinic->pattern == 0)
                                            {{-- in case of intervals show user appointment --}}
                                            <th class="center">{{ Super::getProperty(\App\Http\Traits\DateTrait::getDateByFormat($reservation->time,'h:i:a')) }}</th>
                                        @endif

                                        @if(Request::segment(2) == "all" || Request::segment(2) == "today" || Request::segment(2) == "approved" )
                                            {{--Controls--}}
                                            <td class="center control-icon overflow-hidden"
                                                id="change_status">
                                                <div class="btn-group control-icon">
                                                    @if($auth->role_id  == $role_assistant ||
                                                    ($auth->account->type == \App\Http\Controllers\WebController::ACCOUNT_TYPE_POLY && $reservation->status == \App\Http\Controllers\WebController::R_STATUS_APPROVED))
                                                        @include('admin.common.reservations.change-status')
                                                    @endif
                                                    @if($reservation->transaction_id == -1 && $reservation->payment_method == 0)
                                                        {{-- payment in case of cash--}}
                                                        <a class="pay_cash" data-id="{{ $reservation->id }}"><i
                                                                class="ace-icon fas fa-dollar-sign bigger-120 delete"></i></a>
                                                    @elseif($reservation->payment_method != 0 && $reservation->status != 3)
                                                        <a class="pay_online" data-id="{{ $reservation->id }}"><i
                                                                class="ace-icon fas fa-credit-card bigger-120 delete"></i></a>
                                                    @endif
                                                    <a href="{{ route('reservations.details',['id' => $reservation->id]) }}"><i
                                                            class="ace-icon fas fa-info bigger-120 edit"></i></a>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="row">
                            <div class="col-xs-12 text-center"><img class="no_data_image"
                                                                    src="{{ asset('assets/images/no_data/no_reservations.png') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 text-center"><p
                                    class="loon no_data">{{trans('lang.no_reservations')}}</p></div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@stop

@push('more-scripts')
    {!! Html::script('assets/js/admin/jquery.dataTables.min.js') !!}
    {!! Html::script('assets/js/admin/jquery.dataTables.bootstrap.min.js') !!}
    {!! Html::script('assets/js/admin/smart-tables.js') !!}

    <script>
        var URL = "{{ url('/') }}";
        var token = "{{ csrf_token() }}";
        var role_doctor = "{{ $auth->role_id == $role_doctor }}";

        function generate_search_link(status, day, name, clinic_id = '') {
            if (clinic_id !== '') {
                return URL + '/reservation/table-reservations/' + status + '/' + day + '/' + name + '?clinic=' + clinic_id;
            }
            return URL + '/reservation/table-reservations/' + status + '/' + day + '/' + name;
        }

        // fir search by day or name
        $('.search').on('keyup', function () {
            const day = $('.day').val() == "" ? 0 : $('.day').val();
            const name = $('.search').val();
            const status = "{{ $status }}";
            var clinic_id = '';
            if (role_doctor) {
                clinic_id = "{{ isset($_GET['clinic']) ? $_GET['clinic'] : '' }}";
            }
            $('#table-reservations').load(generate_search_link(status, day, name, clinic_id));
        });

        // for search dy day only
        $('.day').on('change', function () {
            var day = $('.day').val() == "" ? 0 : $('.day').val();
            var name = $('.search').val();
            var status = "{{ $status }}";
            if (role_doctor) {
                clinic = "{{ isset($_GET['clinic']) ? $_GET['clinic'] : '' }}";
                $('#table-reservations').load(URL + '/reservation/table-reservations/' + status + '/' + day + '/' + name + '?clinic=' + clinic);
            } else {
                $('#table-reservations').load(URL + '/reservation/table-reservations/' + status + '/' + day + '/' + name);
            }
        });

        let lastSelected = $(".select-save option:selected");
        $(document).on('change', '.select-save', function (e) {
            let clinic;
            // status as word like ( approved-pending)
            var status = "{{ $status }}";
            var id = $(this).data('id');
            var status_number = $(this).val();
            var routeStatus = "{{ Request::segment(2) }}";
            var is_doctor = "{{ ($auth->role_id == $role_doctor ) ? 'true' : 'false' }}";     // check if the login is doctor or assistant
            if (is_doctor === 'true') {
                clinic = "{{ isset($_GET['clinic']) ? $_GET['clinic'] : ''}}";
                // $('#table-reservations').load(URL + '/reservation/table-reservations/' + status + '?clinic=' + clinic);
            } else {
                clinic = "{{ $auth->clinic_id }}";
            }

            if (status_number == 2) {
                swal({
                    title: "Are you sure?",
                    text: "Your will not be able to recover this status!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false,
                    closeOnCancel: false
                }, function (isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            url: URL + '/reservation/set-status',
                            type: 'PUT',
                            data: {_token: token, id: id, status: status_number}
                        }).done(function (data) {
                            if (data == 'true') {
                                swal({
                                    title: "Done",
                                    text: "",
                                    type: "success",
                                });
                            }
                            //  window.location = URL + '/reservations/' + status + '?clinic=' + clinic;
                            $('#table-reservations').load(URL + '/reservation/table-reservations/' + routeStatus + '?clinic=' + clinic);
                            $('#status' + id).load(URL + '/reservation/get-status/' + id);
                            $('#change-status').load(URL + '/reservation/change-status/');
                        });
                    } else {
                        lastSelected.prop("selected", true);
                        swal.close();
                    }
                });
            } else {
                $.ajax({
                    url: URL + '/reservation/set-status',
                    type: 'PUT',
                    data: {_token: token, id: id, status: $(this).val()}
                }).done(function (data) {
                    // if assistant approved same time twice
                    if (data === 'true') {
                        iziToast.success({
                            theme: 'light',
                            icon: 'icon-person',
                            title: 'Reservation',
                            message: 'Reservation status changes successfully',
                            position: 'bottomRight',
                            progressBarColor: 'rgb(45, 48, 51)',
                        });
                    } else if (data == 'false') {
                        iziToast.error({
                            theme: 'light',
                            icon: 'icon-person',
                            title: 'Error',
                            message: 'Failed to change reservation status',
                            position: 'bottomRight',
                            progressBarColor: 'rgb(45, 48, 51)',
                        });
                    }
                    $('#table-reservations').load(URL + '/reservation/table-reservations/' + routeStatus + '?clinic=' + clinic);
                    $('#status' + id).load(URL + '/reservation/get-status/' + id);
                    $('#change-status').load(URL + '/reservation/change-status/');
                });
            }
        });
        // pay in case of cash
        $(document).on('click', '.pay_cash', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            var status = "{{ $status }}";
            swal({
                title: "Are you sure?",
                text: "Are You Sure that This Patient Already Paid It's Fee!",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes",
                closeOnConfirm: false
            }, function () {
                $.ajax({
                    url: URL + '/reservation/set-reservation-paid',
                    type: 'POST',
                    data: {_token: token, id: id}
                }).done(function (data) {
                    if (data === 'true') {
                        swal({
                            title: "Done",
                            text: "user set paid successfully",
                            type: "success",
                        });
                    } else {
                        swal({
                            title: "Failure",
                            text: "we could not set user paid",
                            type: "fail",
                        });
                    }
                    location.reload();
                });
            });
        });

        // confirm payment in case of Online
        $(document).on('click', '.pay_online', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            swal({
                title: "Confirm Payment!",
                text: "Enter Transaction Id To Confirm Payment:",
                type: "input",
                showCancelButton: true,
                closeOnConfirm: false,
                inputPlaceholder: "transaction id"
            }, function (inputValue) {
                if (inputValue === false) return false;
                if (inputValue === "") {
                    swal.showInputError("Transaction Is Required!");
                    return false
                }
                $.ajax({
                    url: URL + '/reservation/check-transaction',
                    type: 'POST',
                    data: {
                        _token: token,
                        id: id,
                        transaction: inputValue
                    }
                }).done(function (data) {
                    if (data === 'true') {
                        swal({
                            title: "Done",
                            text: "Reservation Is Paid",
                            type: "success",
                        }, function () {
                            location.reload();
                        });
                    } else {
                        swal({
                            title: "Failure",
                            text: "this reservation is not paid",
                            type: "warning",
                        });
                    }
                });
            });
        });


        $(document).on('change', '#reservationStatus', function (e) {
            let clinic;
            let status = $(this).val(); // get selected status
            var is_doctor = "{{ ($auth->role_id == $role_doctor ) ? 'true' : 'false' }}";     // check if the login is doctor or assistant
            if (status) {
                if (is_doctor === 'true') {
                    clinic = "{{ isset($_GET['clinic']) ? $_GET['clinic'] : ''}}";
                    // $('#table-reservations').load(URL + '/reservation/table-reservations/' + status + '?clinic=' + clinic);
                } else {
                    clinic = "{{ $auth->clinic_id }}";
                }
                window.location = URL + '/reservations/' + status + '?clinic=' + clinic;
            }
            return false;
        });

    </script>
@endpush
