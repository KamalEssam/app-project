@extends('layouts.admin.admin-master')

@section('title',  trans('lang.manage_plans') )


@section('content')
    <div class="page-content">
        <div class="page-header">
            <div class="row">
                <div class="col-md-11">
                    <h1>{{trans('lang.manage_plans')}}</h1>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">

                <div class="clearfix">
                    <div class="pull-right tableTools-container"></div>
                </div>

                @if(count($plans) > 0)
                    <div class="table-responsive">
                        <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th class="center">{{trans('lang.name')}}</th>
                                <th class="center">{{trans('lang.price_of_day')}}</th>
                                <th class="center">{{ trans('lang.no_of_clinics') }}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($plans as $plan)
                                <tr>
                                    <td class="center">{{ $plan[app()->getLocale() . '_name']  }}</td>
                                    <td class="center">{{ $plan->price_of_day }} {{ trans('lang.l_e') }}</td>
                                    <td class="center">{{ $plan->no_of_clinics == 0 ? "unlimited " : $plan->no_of_clinics }} {{ trans('lang.clinics') }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else

                    <div class="row">
                        <div class="col-xs-12 text-center"><img class="no_data_image"
                                                                src="{{ asset('assets/images/no_data/no_plans.png') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 text-center"><p
                                    class="loon no_data">{{trans('lang.no_plans')}}</p></div>
                    </div>

                @endif
            </div>
        </div>
    </div>

@stop





