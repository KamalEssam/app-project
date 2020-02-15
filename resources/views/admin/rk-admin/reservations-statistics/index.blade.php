@extends('layouts.admin.admin-master')

@section('title',  trans('lang.total_reservations') )

@section('content')
    <div class="page-content">
        <div class="page-header">
            <div class="row">
                <div class="col-md-10">
                    <h1>{{trans('lang.total_reservations')}}</h1>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">

                <div class="clearfix">
                    <div class="pull-right tableTools-container"></div>
                </div>

                @if(count($reservations_statistics) > 0)
                    <div class="table-responsive">
                        <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th class="center hidden">#</th>
                                <th class="center">{{trans('lang.name')}}</th>
                                <th class="center">{{trans('lang.email')}}</th>
                                <th class="center">{{trans('lang.mobile')}}</th>
                                <th class="center">{{trans('lang.reservations')}}</th>
                                <th class="center">{{ trans('lang.controls') }}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($reservations_statistics as $reservation_statistic)
                                <tr>
                                    <td class="center hidden"></td>
                                    <td class="center">{{ $reservation_statistic->account_name }}</td>
                                    <td class="center">{{ $reservation_statistic->email }}</td>
                                    <td class="center"> {{ $reservation_statistic->mobile }}</td>
                                    <td class="center">{{ $reservation_statistic->count }}</td>
                                    <td class="center">
                                        <div class="btn-group control-icon">
                                            <a href="{{ route('account.reservations',['id' => $reservation_statistic->id]) }}"><i
                                                    class="ace-icon fa fa-eye show"></i></a>
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
                                                                src="{{ asset('assets/images/no_data/no_insurance.png') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 text-center"><p
                                class="loon no_data">{{trans('lang.no_insurance_companies')}}</p>
                        </div>
                    </div>

                @endif
            </div>
        </div>
    </div>

    <div id="modal_import" data-iziModal-title="{{ trans('lang.import') }}"
         data-iziModal-subtitle="{{ trans('lang.import_insurance_company') }}" data-iziModal-icon="icon-home">
        <div class="row" style="padding: 40px">
            <form id="modal-form" action="{{ route('insurance_company.imports') }}" method="POST"
                  enctype="multipart/form-data">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group col-md-12">
                    <div class="row">
                        <div class="col-md-12 label-form">
                            <label for="leads">{{ trans('lang.leads') }}<span class="astric">*</span></label>
                        </div>
                        <div class="col-md-12 form-input">
                            {{ Form::file('insurance_companies', NULL, ['class'=>'form-control ' . ($errors->has('leads') ? 'redborder' : ''),'required'=>'required' , 'id'=>'insurance_company.imports']) }}
                            <small class="text-danger">{{ $errors->first('leads') }}</small>
                            <br>
                            <p class="help-block">download sample <a
                                    href="{{ asset('assets/samples/insurance_sample.xlsx') }}">download</a></p>
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
