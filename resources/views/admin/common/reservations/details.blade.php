@extends('layouts.admin.admin-master')

@section('title', trans('lang.reservation-details'))

@section('styles')
    {!! Html::style('assets/css/admin/colorbox.min.css') !!}
    <style>
        #cboxContent {
            border: 5px solid #000;
        }

        #cboxClose {
            top: -3px;
            background-color: #000;
            border: 2px solid #FFF;
            border-radius: 32px;
            color: #FFF;
            font-size: 21px;
            height: 28px;
            width: 28px;
            padding-bottom: 2px;
            margin-left: 0;
            right: -2px;
        }

        #cboxOverlay {
            background: #000;
        }

        .profile-info-name {
            width: 180px;
            text-align: left;
        }

        .profile-user-info-striped {
            font-size: 15px;
        }

        .profile-info-value {
            padding: 12px;
        }
    </style>
@stop

@section('content')
    <div class="page-content mt-70">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <h1 class="font-18 loon">{{ trans('lang.the-reservation-details') }}</h1>
                        <hr>
                        <div class="profile-user-info profile-user-info-striped">
                            <div class="profile-info-row">
                                <div class="profile-info-name"> {{ trans('lang.user_name') }} </div>

                                <div class="profile-info-value">
                                    <span class="editable"
                                          id="username">{{ $reservation->user->name ?? 'no name' }}</span>
                                </div>
                            </div>

                            <div class="profile-info-row">
                                <div class="profile-info-name"> {{ trans('lang.mobile') }} </div>

                                <div class="profile-info-value">
                                    <span class="editable"
                                          id="username">{{ $reservation->user->mobile ?? 'no mobile' }}</span>
                                </div>
                            </div>

                            <div class="profile-info-row">
                                <div class="profile-info-name"> {{ trans('lang.day') }} </div>

                                <div class="profile-info-value">
                                    <span class="editable">{{ $reservation->day }}</span>
                                </div>
                            </div>

                            @if($reservation->workingHour)
                                <div class="profile-info-row">
                                    <div class="profile-info-name">{{ trans('lang.time') }}</div>

                                    <div class="profile-info-value">
                                        <span class="editable"
                                              id="age">{{ \Carbon\Carbon::parse($reservation->workingHour->time)->format('h:i A')  }}</span>
                                    </div>
                                </div>
                            @endif

                            <div class="profile-info-row">
                                <div class="profile-info-name">{{ trans('lang.complaint') }}</div>

                                <div class="profile-info-value">
                                    <span class="editable"
                                          id="age">{{ $reservation->complaint ?? 'not available'  }}</span>
                                </div>
                            </div>

                            <div class="profile-info-row">
                                <div class="profile-info-name">{{ trans('lang.payment-method') }}</div>

                                <div class="profile-info-value">
                                    <span class="editable"
                                          id="age">
                                        @switch($reservation->payment_method)
                                            @case(0)
                                            {{ trans('lang.cash') }}
                                            @break
                                            @case(1)
                                            {{ trans('lang.online') }}
                                            @break
                                            @case(2)
                                            {{ trans('lang.installment') }}
                                            @break
                                            @default
                                            {{ trans('lang.error') }}
                                        @endswitch
                                    </span>
                                </div>
                            </div>


                            <div class="profile-info-row">
                                <div class="profile-info-name">{{ trans('lang.doctor') }}</div>

                                <div class="profile-info-value">
                                    <span class="editable"
                                          id="age">{{ $reservation->clinic->account->{app()->getLocale() . '_name'} ?? 'not found' }}</span>
                                </div>
                            </div>


                            <div class="profile-info-row">
                                <div class="profile-info-name">{{ trans('lang.clinic') }}</div>

                                <div class="profile-info-value">
                                    <span class="editable"
                                          id="age">{{ ((app()->getLocale() == 'en') ? (($reservation->clinic->province->{'en_name'} ?? '') . ' branch') : (($reservation->clinic->province->{'ar_name'} ?? '') . ' فرع'))  }}</span>
                                </div>
                            </div>

                            <div class="profile-info-row">
                                <div class="profile-info-name">{{ trans('lang.payment') }}</div>

                                <div class="profile-info-value">
                                    <div class="profile-user-info profile-user-info-striped">

                                        <div class="profile-info-row">
                                            <div class="profile-info-name"> {{ trans('lang.fees') }} </div>
                                            <div class="profile-info-value">
                                                    <span class="editable"
                                                          id="username">{{ ($payment->subtotal_fees . ' ' ?? '0 ') . $payment->currency }}</span>
                                            </div>
                                        </div>

                                        @if($payment->offer)
                                            <div class="profile-info-row">
                                                <div class="profile-info-name"> {{ trans('lang.offer') }} </div>
                                                <div class="profile-info-value">
                                                    <span class="editable"
                                                          id="username">{{($payment->offer . ' ' ?? '0 ') .  $payment->currency }}</span>
                                                </div>
                                            </div>
                                        @endif

                                        @if($payment->discount)
                                            <div class="profile-info-row">
                                                <div class="profile-info-name"> {{ trans('lang.discount') }} </div>
                                                <div class="profile-info-value">
                                                    <span class="editable red"
                                                          id="username"> - {{ ($payment->discount ?? '0 ') . '%' }}</span>
                                                </div>
                                            </div>
                                        @endif

                                        @if($payment->promo)
                                            <div class="profile-info-row">
                                                <div class="profile-info-name"> {{ trans('lang.promo') }} </div>
                                                <div class="profile-info-value">
                                                    <span class="editable red"
                                                          id="username"> - {{ ($payment->promo . ' ' ?? '0') . $payment->currency }}</span>
                                                </div>
                                            </div>
                                        @endif


                                        @if(count($payment->services) > 0)
                                            <div class="profile-info-row">
                                                <div class="profile-info-name"> {{ trans('lang.services') }} </div>
                                                <div class="profile-info-value">
                                                    <table class="table table-responsive">
                                                        @foreach($payment->services as $service)
                                                            <tr>
                                                                <td>{{ $service->name }}</td>
                                                                <td>{{ $service->price }}   {{ $payment->currency }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </table>
                                                </div>
                                            </div>
                                        @endif


                                        @if($payment->total_fees)
                                            <div class="profile-info-row">
                                                <div class="profile-info-name"> {{ trans('lang.total_fees') }} </div>
                                                <div class="profile-info-value">
                                                    <span class="editable"
                                                          id="username">{{ ($payment->total_fees . ' ' ?? '0 ') . $payment->currency }}</span>
                                                </div>
                                            </div>
                                        @endif

                                        @if($payment->transaction_id > 1)
                                            <div class="profile-info-row">
                                                <div
                                                    class="profile-info-name"> {{ trans('lang.transaction-id') }} </div>
                                                <div class="profile-info-value">
                                                    <span class="editable"
                                                          id="username">{{ ($payment->transaction_id ?? '0') }}</span>
                                                </div>
                                            </div>
                                        @endif


                                        @if($payment->cash_back)
                                            <div class="profile-info-row">
                                                <div
                                                    class="profile-info-name"> {{ trans('lang.transaction-id') }} </div>
                                                <div class="profile-info-value">
                                                    <span class="editable green"
                                                          id="username"> + {{ ($payment->cash_back . ' ' ?? '0 ' ) . $payment->currency }}</span>
                                                </div>
                                            </div>
                                        @endif

                                    </div>
                                </div>
                            </div>

                            <div class="profile-info-row">
                                <div class="profile-info-name">{{ trans('lang.status') }}</div>

                                <div class="profile-info-value">
                                    <span class="editable"
                                          id="age">
                                        @switch ($reservation->status)
                                            @case (1)
                                            {{ trans('lang.approved') }}
                                            @break
                                            @case (2)
                                            {{ trans('lang.canceled') }}
                                            @break
                                            @case (3)
                                            {{ trans('lang.attended') }}
                                            @break
                                            @case (4)
                                            {{ trans('lang.missed') }}
                                            @break
                                            @default
                                            {{ trans('lang.pending') }}
                                        @endswitch
                                    </span>
                                </div>
                            </div>

                            <div class="profile-info-row">
                                <div class="profile-info-name">{{ trans('lang.payment') }}</div>

                                <div class="profile-info-value">
                                    <span class="editable" id="age">
                                        <div class="btn-group control-icon">
                                            @if($reservation->transaction_id === -1 && $reservation->payment_method === 0)
                                                {{ trans('lang.not-paid') }}
                                                {{-- payment in case of cash--}}
                                                <a class="pay_cash ml-5" data-id="{{ $reservation->id }}"><i
                                                        class="ace-icon fas fa-dollar-sign bigger-120 delete"></i></a>
                                            @elseif($reservation->payment_method !== 0 && $reservation->status !== 3)
                                                {{ trans('lang.check-paid') }}
                                                <a class="pay_online ml-5" data-id="{{ $reservation->id }}"><i
                                                        class="ace-icon fas fa-credit-card bigger-120 delete"></i></a>
                                            @else
                                                {{ trans('lang.paid') }}
                                            @endif
                                        </div>
                                    </span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br><br><br><br>
@stop

@push('more-scripts')
    <script>
        var URL = "{{ url('/') }}";
        var token = "{{ csrf_token() }}";
        var role_doctor = "{{ $auth->role_id == $role_doctor }}";

        // pay in case of cash
        $(document).on('click', '.pay_cash', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            var status = "{{ $reservation->status }}";
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
    </script>
@endpush
