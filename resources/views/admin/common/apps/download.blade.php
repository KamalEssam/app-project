@extends('layouts.admin.admin-master')

@section('title',  trans('lang.apps') )


@section('content')
    <div class="page-content">
        <div class="page-header">
            <div class="row">
                <div class="col-md-12 center">
                    <h1>{{ trans('lang.download_our_apps') }}</h1>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <div class="clearfix">
                    <div class="pull-right tableTools-container"></div>
                </div>
                <div class="row">
                    @if( $app )
                        <div class="row">
                            <div class="col-md-6 col-md-offset-3 mt-10">
                            <p class="center"> {{ trans('lang.download_text') }} </p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mt-50 relative shadow-box ">
                                    <img src="{{ asset('assets/images/logo/android-logo.png') }}" class="absolute android-logo mb-25">
                                    <a target="_blank" href="{{ $app->android }}" class="btn-loon absolute android-download-btn bolder">{{ trans('lang.download_now') }}</a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mt-50 relative shadow-box">
                                <img src="{{ asset('assets/images/logo/ios-logo.png') }}" class="absolute ios-logo mb-25">
                                <a target="_blank" href="{{ $app->ios }}" class="btn-loon absolute ios-download-btn bolder">{{ trans('lang.download_now') }}</a>
                            </div>
                        </div>
                    @else
                        <div class="row">
                            <div class="col-md-8 col-md-offset-2">
                                <p class="center font-18 grey"> {{ trans('lang.download_paragraph') }}</p>
                            </div>
                        </div>
                        <div class="row">
                        <div class="mt-30" id="patient-info">
                                <div class="col-md-5 ml-29felmaya">
                                    <div class="panel panel-default panel-no-border">
                                        <div class="panel-heading bolder">{{ trans('lang.requirements') }}</div>
                                        <div class="panel-body">

                                            <ul>
                                                <li class="grey mb-10"> {{ trans('lang.logo_req') }}</li>
                                                <li class="grey mb-10">{{ trans('lang.color_req') }}</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@stop