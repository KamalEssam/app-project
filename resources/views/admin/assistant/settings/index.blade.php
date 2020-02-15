@extends('layouts.admin.admin-master')

@section('title',  trans('lang.manage_settings') )


@section('content')
    <div class="page-content">
        <div class="page-header">
            <div class="row">
                <div class="col-md-11">
                    <h1>{{trans('lang.manage_settings')}}</h1>
                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">

                <div class="clearfix">
                    <div class="pull-right tableTools-container"></div>
                </div>

                @if(count($setting) > 0)
                    <div class="table-responsive">
                        <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th class="center">{{trans('lang.name')}}</th>
                                <th class="center">{{trans('lang.bio')}}</th>
                                <th class="center">{{trans('specialities')}}</th>
                                <th class="center">{{trans('lang.website')}}</th>
                                <th class="center">{{trans('lang.controls')}}</th>
                            </tr>
                            </thead>

                            <tbody>
                            <tr>
                                <td class="center">{{ $setting[App::getLocale() . '_name'] }}</td>
                                <td class="center">{{ $setting[App::getLocale() . '_bio']  }}</td>
                                <td class="center">{{ $setting[App::getLocale() . '_speciality'] }}</td>
                                <td class="center">{{ $setting->website }}</td>
                                <td class="center">
                                    <div class="btn-group control-icon">
                                        <a href="{{ route('clinic-settings.edit', $setting->id)  }}"><i
                                                    class="ace-icon fa fa-edit bigger-120  edit" data-id=""></i></a>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="row">
                        <div class="col-xs-12 text-center"><img class="no_data_image"
                                                                src="{{ asset('assets/images/no_data/no_settings.png') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 text-center"><p
                                    class="loon no_data">{{trans('lang.no_settings')}}</p></div>
                    </div>
                @endif
            </div>
        </div>
    </div>

@stop




