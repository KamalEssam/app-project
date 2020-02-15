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
            <div class="row">
                <div class="col-md-12">
                    <h1>{{ trans('lang.total_reservations') }}</h1>
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
                            <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th class="hidden"></th>
                                    <th class="center" style="width: 50px !important;">{{ trans('lang.image') }}</th>
                                    <th class="center">{{ trans('lang.name') }}</th>
                                    <th class="center">{{ trans('lang.email') }}</th>
                                    <th class="center">{{ trans('lang.date') }}</th>
                                    <!-- reservation type (checkup, follow up)-->
                                    <th class="center">{{ trans('lang.status') }}</th>
                                    <th class="center">{{ trans('lang.payment') }}</th>
                                    <!-- show controls for assistant-->
                                    <th class="center">{{ trans('lang.fees') }}</th>
                                </tr>
                                </thead>

                                <tbody id="table" class="t-content">
                                @foreach($reservations as $reservation)
                                    <tr>
                                        <td class="hidden"></td>
                                        <td class="center">
                                            <div class="premium-container">
                                                @if($reservation->user->image)
                                                    <img src="{{ $reservation->user->image }}" class="premium-image"
                                                         style="">
                                                    @if ($reservation->user->is_premium == 1)
                                                        <img src="{{ asset('assets/images/premium.png') }}" alt=""
                                                             class="premium-icon">
                                                    @endif
                                                @else
                                                    {{ trans('lang.n/a') }}
                                                @endif
                                            </div>
                                        </td>
                                        <td class="center">
                                            {{ ($reservation->user->name)}}
                                        </td>
                                        <td class="center control-icon">{{ ($reservation->user->email)}}</td>
                                        <td class="center">{{ ($reservation->day) }}</td>{{--// if assistant she can update reservation status from here if page(today or all)--}}
                                        <td class="center" id="status{{ $reservation->id }}">
                                            @include('admin.common.reservations.status')
                                        </td>
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
                                                <p>{{ $paid->total_fees }} EPG</p>
                                            @else
                                                <p>not set</p>
                                            @endif
                                        </td>

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
@endpush
