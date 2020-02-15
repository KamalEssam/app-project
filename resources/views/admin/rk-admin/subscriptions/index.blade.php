@extends('layouts.admin.admin-master')

@section('title',  trans('lang.manage_subscribers') )


@section('content')
    <div class="page-content">
        <div class="page-header">
            <div class="row">
                <div class="col-md-12">
                    <h1>{{trans('lang.manage_subscribers')}}</h1>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">

                <div class="clearfix">
                    <div class="pull-right tableTools-container"></div>
                </div>

                @if(count($subscribers) > 0)
                    <div class="table-responsive">
                        <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th class="center">{{ trans('lang.id') }}</th>
                                <th class="center">{{ trans('lang.email') }}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($subscribers as $subscriber)
                                <tr>
                                    <td class="center">{{ $subscriber->id  }}</td>
                                    <td class="center">{{ $subscriber->email }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="row">
                        <div class="col-xs-12 text-center"><img class="no_data_image"
                                                                src="{{ asset('assets/images/no_data/no_subscription.png') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 text-center"><p
                                    class="loon no_data">{{trans('lang.no_subscribers')}}</p></div>
                    </div>
                @endif
            </div>
        </div>
    </div>

@stop
