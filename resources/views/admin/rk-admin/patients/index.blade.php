@extends('layouts.admin.admin-master')

@section('title',  trans('lang.manage_patients') )


@section('content')
    <div class="page-content">
        <div class="page-header">
            <div class="row">
                <div class="col-md-11">
                    <h1>{{ trans('lang.manage_patients') }}</h1>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">

                <div class="clearfix">
                    <div class="pull-right tableTools-container"></div>
                </div>

                @if(count($patients) > 0)
                    <div class="table-responsive">
                        <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th class="center" style="width: 50px !important;">{{ trans('lang.image') }}</th>
                                <th class="center">{{ trans('lang.name') }}</th>
                                <th class="center">{{ trans('lang.email') }}</th>
                                <th class="center">{{ trans('lang.mobile') }}</th>
                                {{--<th class="center">{{ trans('lang.controls') }}</th>--}}
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($patients as $patient)
                                <tr>
                                    <td class="center">
                                        <div class="premium-container">
                                            @if($patient->image)
                                                <img src="{{ $patient->image }}" class="premium-image">
                                            @else
                                                <img src="{{ asset('assets/images/default.png')}}"
                                                     class="premium-image">
                                            @endif
                                            @if ($patient->is_premium == 1)
                                                <img src="{{ asset('assets/images/premium.png') }}" alt=""
                                                     class="premium-icon">
                                            @endif
                                        </div>
                                    </td>
                                    <td class="center">
                                        {{ Super::getProperty( $patient->name)  }}
                                    </td>
                                    <td class="center">{{ Super::getProperty( $patient->email ) }}</td>
                                    <td class="center">{{ Super::getProperty( $patient->mobile ) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else

                    <div class="row">
                        <div class="col-xs-12 text-center"><img class="no_data_image"
                                                                src="{{ asset('assets/images/no_data/no_patients.png') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 text-center"><p
                                class="loon no_data">{{trans('lang.no_patients')}}</p></div>
                    </div>
                @endif
            </div>
        </div>
    </div>

@stop




