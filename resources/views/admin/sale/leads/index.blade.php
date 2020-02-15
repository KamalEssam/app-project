@extends('layouts.admin.admin-master')

@section('title',  trans('lang.manage_leads') )

@section('content')
    <div class="page-content">
        <div class="page-header">
            <div class="row">
                <div class="col-md-9">
                    <h1>{{trans('lang.manage_leads')}}</h1>
                </div>
                <div class="col-md-3">
                    <div class="row">
                        <div class="col-md-4">
                            <a href="{{ route('leads.exports') }}"
                               class="btn btn-sm btn-warning btn-block btn-export">{{ trans('lang.export') }}</a>
                        </div>

                        <div class="col-md-4">
                            <a href="#" data-iziModal-open="#modal_import"
                               class="btn btn-sm btn-warning btn-block btn-import">{{ trans('lang.import') }}</a>
                        </div>

                        <div class="col-md-4">
                            <a href="{{ route('leads.create') }}"
                               class="btn btn-sm btn-primary btn-block btn-add">{{ trans('lang.add') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">

                <div class="clearfix">
                    <div class="pull-right tableTools-container"></div>
                </div>

                @if(count($leads) > 0)
                    <div class="table-responsive">
                        <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th class="center">{{trans('lang.name')}}</th>
                                <th class="center">{{trans('lang.mobile')}}</th>
                                <th class="center">{{ trans('lang.controls') }}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($leads as $lead)
                                <tr>
                                    <td class="center">{{$lead->name  }}</td>
                                    <td class="center">{{$lead->mobile  }}</td>
                                    <td class="center">
                                        <div class="btn-group control-icon">
                                            <a href="{{ route('leads.edit', $lead->id)  }}"><i
                                                    class="ace-icon fa fa-edit bigger-120  edit"
                                                    title="{{ trans('lang.edit_lead') }}"
                                                    data-id=""></i></a>
                                            <a title="{{ trans('lang.delete_lead') }}"
                                               href="#"><i class="ace-icon fa fa-trash-alt bigger-120 delete ajax-btn"
                                                           data-id="{{ $lead->id }} "
                                                           data-link="{{ route('leads.destroy', $lead->id) }}"
                                                           data-type="DELETE"></i></a>
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
                                                                src="{{ asset('assets/images/no_data/no_leads.png') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 text-center"><p
                                class="loon no_data">{{trans('lang.no_leads')}}</p></div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="modal_import" data-iziModal-title="{{ trans('lang.import') }}"
         data-iziModal-subtitle="{{ trans('lang.import_leads') }}" data-iziModal-icon="icon-home">
        <div class="row" style="padding: 40px">
            <form id="modal-form" action="{{ route('leads.imports') }}" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group col-md-12">
                    <div class="row">
                        <div class="col-md-12 label-form">
                            <label for="leads">{{ trans('lang.leads') }}<span class="astric">*</span></label>
                        </div>
                        <div class="col-md-12 form-input">
                            {{ Form::file('leads', NULL, ['class'=>'form-control ' . ($errors->has('leads') ? 'redborder' : ''),'required'=>'required'  , 'id'=>'leads']) }}
                            <small class="text-danger">{{ $errors->first('leads') }}</small>
                            <br>
                            <p class="help-block">download sample <a href="{{ asset('assets/samples/leads_sample.xlsx') }}">download</a></p>
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
