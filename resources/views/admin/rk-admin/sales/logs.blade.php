@extends('layouts.admin.admin-master')

@section('title',  trans('lang.manage_sales') )

@section('content')
    <div class="page-content">
        <div class="page-header">
            <div class="row">
                <div class="col-md-11">
                    <h1>{{trans('lang.manage_sales')}}</h1>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">

                <div class="clearfix">
                    <div class="pull-right tableTools-container"></div>
                </div>

                @if(count($logs) > 0)
                    <div class="table-responsive">
                        <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th class="center hidden">#</th>
                                <th class="center">{{trans('lang.sales_agent')}}</th>
                                <th class="center">{{trans('lang.user_account')}}</th>
                                <th class="center">{{trans('lang.date')}}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($logs as $log)
                                <tr>
                                    <td class="hidden"></td>
                                    <td class="center">{{ $log->user->name  }}</td>
                                    <td class="center">{{ $log->account[app()->getLocale() . '_name'] }}</td>
                                    <td class="center">{{ $log->created_at }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="row">
                        <div class="col-xs-12 text-center"><img class="no_data_image"
                                                                src="{{ asset('assets/images/no_data/no_sales.png') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 text-center"><p
                                    class="loon no_data">{{trans('lang.no_logs')}}</p></div>
                    </div>
                @endif
            </div>
        </div>
    </div>

@stop





