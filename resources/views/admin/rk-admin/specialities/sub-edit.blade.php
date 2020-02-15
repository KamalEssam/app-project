@extends('layouts.admin.admin-master')
@section('title', trans('lang.add_sub_speciality'))
@section('styles')
    {!! Html::style('assets/css/admin/form.css') !!}
@stop

@section('content')
    <div class="page-content mt-70">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <h1 class="font-18 loon">{{trans('lang.edit_sub_speciality')}}
                            : {{ $speciality->{ app()->getLocale() . '_name' } }} </h1>
                        <hr>
                        {!! Form::model($speciality, ['route' => ['specialities.sub-update', $speciality->id], 'method' => 'PATCH']) !!}
                        <div class="row">
                            <div class="form-group col-md-12">
                                <div class="row">
                                    <div class="col-md-12 label-form">
                                        <label for="en_name">{{ trans('lang.en_name') }}<span
                                                class="astric">*</span></label>
                                    </div>
                                    <div class="col-md-12 form-input">
                                        {{ Form::text('en_name', NULL, ['class'=>'form-control ' . ($errors->has('en_name') ? 'redborder' : ''),'pattern' => $english_regex,'title' => trans('lang.only_english') , 'required'=>'required'  , 'id'=>'en_name']) }}
                                        <small class="text-danger">{{ $errors->first('en_name') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <div class="row">
                                    <div class="col-md-12 label-form">
                                        <label for="ar_name">{{ trans('lang.ar_name') }}<span
                                                class="astric">*</span></label>
                                    </div>
                                    <div class="col-md-12 form-input">
                                        {{ Form::text('ar_name', NULL, ['class'=>'form-control ' . ($errors->has('ar_name') ? 'redborder' : ''),'pattern' => $arabic_regex,'title' => trans('lang.only_arabic') , 'required'=>'required'  , 'id'=>'ar_name' ]) }}
                                        <small class="text-danger">{{ $errors->first('ar_name') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{ Form::hidden('speciality_id',$speciality->speciality_id) }}
                        {{  Form::submit('Add' , ['class' => 'btn-loon btn btn-xs pull-right mt-20']) }}
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
