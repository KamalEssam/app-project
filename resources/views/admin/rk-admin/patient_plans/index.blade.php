@extends('layouts.admin.admin-master')

@section('title',  trans('lang.manage_plans') )

@section('content')
    <div class="page-content">
        <div class="page-header">
            <div class="row">
                <div class="col-md-11">
                    <h1>{{trans('lang.manage_plans')}}</h1>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('patient-plans.create') }}" id="plan-add"
                       class="btn btn-sm btn-primary btn-block btn-add trigger-modal">{{ trans('lang.add') }}</a>
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
                                <th class="center">{{trans('lang.price')}}</th>
                                <th class="center">{{trans('lang.points')}}</th>
                                <th class="center">{{trans('lang.months_number')}}</th>
                                <th class="center">{{ trans('lang.description') }}</th>
                                <th class="center">{{trans('lang.controls')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($plans as $plan)
                                <tr>
                                    <td class="center">{{ $plan[app()->getLocale() . '_name']  }}</td>
                                    <td class="center">{{ $plan->price }} {{ trans('lang.l_e') }}</td>
                                    <td class="center">{{ $plan->points }}</td>
                                    <td class="center">{{ $plan->months }} </td>
                                    <td class="center">{{ $plan[app()->getLocale() . '_desc'] }}</td>
                                    <td class="center">
                                        <div class="btn-group control-icon">
                                            <a title="{{ trans('lang.edit_plan') }}"
                                               href="{{ route('patient-plans.edit', $plan->id) }}">
                                                <i class="ace-icon fa fa-edit bigger-120  edit"></i>
                                            </a>
                                        </div>
                                    </td>
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





