@extends('layouts.admin.admin-master')

@section('title',  trans('lang.manage_services') )

@section('content')
    <div class="page-content">
        <div class="page-header">
            <div class="row">
                <div class="col-md-10">
                    <h1>{{trans('lang.manage_services')}}</h1>
                </div>
                <div class="col-md-2">

{{--                    <div class="row">--}}
{{--                        <div class="col-md-6">--}}
{{--                            <a href="{{ route('services.create') }}"--}}
{{--                               class="btn btn-sm btn-primary btn-block btn-add">{{ trans('lang.add') }}</a>--}}
{{--                        </div>--}}
{{--                    </div>--}}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">

                <div class="clearfix">
                    <div class="pull-right tableTools-container"></div>
                </div>

                @if(count($services) > 0)
                    <div class="table-responsive">
                        <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th class="center">{{trans('lang.name')}}</th>
                                <th class="center">{{trans('lang.controls')}}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($services as $service)
                                <tr>
                                    <td class="center">{{ $service[app()->getLocale() . '_name'] }}</td>
                                    <td class="center">
                                        <div class="btn-group control-icon">
                                            <a href="{{route('services.edit', $service->id)}}"
                                               title="{{ trans('lang.edit_service') }}"><i
                                                    class="ace-icon fa fa-edit bigger-120  edit" data-id=""></i></a>

                                            @can('super-only', $auth)
                                                <a href="#"><i
                                                        class="ace-icon fa fa-trash-alt bigger-120 delete ajax-btn"
                                                        data-id="{{$service->id}} "
                                                        data-link="{{route('services.destroy', $service->id)}}"
                                                        data-type="DELETE"></i></a>
                                            @endcan
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
                                                                src="{{ asset('assets/images/no_data/no_services.png') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 text-center"><p
                                class="loon no_data">{{trans('lang.no_services')}}</p></div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="modal_import" data-iziModal-title="{{ trans('lang.import') }}"
         data-iziModal-subtitle="{{ trans('lang.import_services') }}" data-iziModal-icon="icon-home">
        <div class="row" style="padding: 40px">
            <form id="modal-form" action="{{ route('services.imports') }}" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group col-md-12">
                    <div class="row">
                        <div class="col-md-12 label-form">
                            <label for="services">{{ trans('lang.services') }}<span class="astric">*</span></label>
                        </div>
                        <div class="col-md-12 form-input">
                            {{ Form::file('services', NULL, ['class'=>'form-control ' . ($errors->has('leads') ? 'redborder' : ''),'required'=>'required'  , 'id'=>'services']) }}
                            <small class="text-danger">{{ $errors->first('leads') }}</small>
                            <br>
                            <p class="help-block">download sample <a
                                    href="{{ asset('assets/samples/services_sample.xlsx') }}">download</a></p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <input type="submit" value="{{ trans('lang.save') }}"
                           class="btn-modal-form-submit btn btn-primary btn-lg pull-right" id="submit-patient">
                </div>
            </form>
        </div>
    </div>

@stop





