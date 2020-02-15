@extends('layouts.admin.admin-master')

@section('title',  trans('lang.manage_cashback') )

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-md-4">
                <h1>{{trans('lang.manage_financial')}}</h1>
            </div>
            <div class="col-md-3">
                <select name="year" id="year" class="form-control">
                    @php $currentYear = date('Y'); $currentMonth = date('m'); @endphp
                    @for ($i = 2018; $i <= $currentYear; $i++)
                        <option value="{{$i}}" {{$_REQUEST['year'] == $i ? 'selected' : ''}}>{{$i}}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <select name="month" id="month" class="form-control">
                    @for ($i = 1; $i <= 12; $i++)
                        @php $timestamp = mktime(0, 0, 0, $i);
                                    $label = date('F', $timestamp);
                        @endphp
                        <option
                            value="{{ sprintf('%02d', $i)  }}" {{$_REQUEST['month'] == $i ? 'selected' : ''}}>{{ $label }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2 text-center">
                <a class="btn btn-primary" href="#" onclick="location.reload();">{{ trans('lang.refresh') }}</a>
            </div>
        </div>
    </div>
    <div class="page-content">
        <div class="col-md-6 col-md-offset-3 mt-40">

            @if ($results->total_reservations > 0)
                {{--   General Reservations Numbers     --}}
                <div class="panel panel-default">
                    <div class="panel-heading">{{ trans('lang.reservations') }}</div>
                    <div class="panel-body">
                        <table class="table table-responsive table-bordered">
                            <thead class="text-center">
                            <tr>
                                <td>
                                    {{ trans('lang.total_reservations') }}
                                </td>
                                <td>
                                    {{ trans('lang.cash_reservations') }}
                                </td>
                                <td>
                                    {{ trans('lang.online_reservations') }}
                                </td>
                            </tr>
                            </thead>
                            <tbody class="text-center">
                            <tr>
                                <td>{{ $results->total_reservations }}</td>
                                <td>{{ $results->cash_reservations }}</td>
                                <td>{{ $results->online_reservations }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>


                {{--   Paid Reservations Numbers     --}}
                <div class="panel panel-default">
                    <div class="panel-heading">{{ trans('lang.paid_reservations') }}</div>
                    <div class="panel-body">
                        <table class="table table-responsive table-bordered">
                            <thead class="text-center">
                            <tr>
                                <td>
                                    {{ trans('lang.total_reservations') }}
                                </td>
                                <td>
                                    {{ trans('lang.paid_cash_reservations') }}
                                </td>
                                <td>
                                    {{ trans('lang.paid_online_reservations') }}
                                </td>
                            </tr>
                            </thead>
                            <tbody class="text-center">
                            <tr>
                                <td>{{ $results->cash_reservations_paid + $results->online_reservations_paid }}</td>
                                <td>{{ $results->cash_reservations_paid }}</td>
                                <td>{{ $results->online_reservations_paid }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{--   Cash Reservations Numbers     --}}
                <div class="panel panel-default">
                    <div class="panel-heading">{{ trans('lang.cash_reservations') }}</div>
                    <div class="panel-body">
                        <table class="table table-responsive table-bordered">
                            <thead class="text-center">
                            <tr>
                                <td>
                                    {{ trans('lang.expected_cash') }}
                                </td>
                                <td>
                                    {{ trans('lang.actual_cash_income') }}
                                </td>
                            </tr>
                            </thead>
                            <tbody class="text-center">
                            <tr>
                                <td>{{ $results->expected_cash_income }} EGP</td>
                                <td>{{ $results->actual_cash_income }} EGP</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>


                {{--   Online  Reservations Numbers     --}}
                <div class="panel panel-default">
                    <div class="panel-heading">{{ trans('lang.online_reservation') }}</div>
                    <div class="panel-body">
                        <table class="table table-responsive table-bordered">
                            <thead class="text-center">
                            <tr>
                                <td>
                                    {{ trans('lang.expected_cash') }}
                                </td>
                                <td>
                                    {{ trans('lang.actual_cash_income') }}
                                </td>
                            </tr>
                            </thead>
                            <tbody class="text-center">
                            <tr>
                                <td>{{ $results->expected_online_income }} EGP</td>
                                <td>{{ $results->actual_online_income }} EGP</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>


                {{--   Online  Reservations Numbers     --}}
                <div class="panel panel-default">
                    <div class="panel-heading">{{ trans('lang.online_share_division') }}</div>
                    <div class="panel-body">
                        <table class="table table-responsive table-bordered">
                            <thead class="text-center">
                            <tr>
                                <td>
                                    {{ trans('lang.total_fees') }}
                                </td>
                                <td>
                                    {{ trans('lang.doctor_income') }}
                                </td>

                                <td>
                                    {{ trans('lang.seena_income') }}
                                </td>

                                <td>
                                    {{ trans('lang.patient_income') }}
                                </td>
                            </tr>
                            </thead>
                            <tbody class="text-center">
                            <tr>
                                <td>{{ $results->cash_back->doctor_income + $results->cash_back->seena_income + $results->cash_back->patients_income }}
                                    EGP
                                </td>
                                <td>{{ $results->cash_back->doctor_income }} EGP</td>
                                <td>{{ $results->cash_back->seena_income }} EGP</td>
                                <td>{{ $results->cash_back->patients_income }} EGP</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($results->is_paid && $results->actual_online_income > 0)
                    <div class="alert alert-success text-center">
                        {{ trans('lang.is_paid') }}
                    </div>
                @else
                    <div class="alert alert-danger text-center">
                        {{ trans('lang.is_not_paid') }}
                    </div>
                @endif
            @else
                <div class="text-center">
                    <div class="alert alert-danger">There Is No Reservations In This Month</div>
                </div>
            @endif

        </div>
    </div>
@stop

@push('more-scripts')
    <script>
        $(document).ready(function () {
            $('#month').on('change', function () {
                let month = $(this).children("option:selected").val();
                let year = $('#year').children("option:selected").val();
                location.href = "{{route('financial.report')}}" + "?year=" + year + "&month=" + month;
            });

            $('#year').on('change', function () {
                let year = $(this).children("option:selected").val();
                let month = $('#month').children("option:selected").val();
                location.href = "{{route('financial.report')}}" + "?year=" + year + "&month=" + month;
            });
        });
    </script>
@endpush
