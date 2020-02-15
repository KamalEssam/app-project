@extends('layouts.admin.admin-master')
@section('title', trans('lang.create_insurance_company'))
@section('styles')
    {!! Html::style('assets/css/admin/form.css') !!}
@stop
@section('content')
    <div class="page-content mt-70">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <h1 class="font-18 loon">{{trans('lang.create_insurance_company')}}</h1>
                        <hr>
                        {!! Form::open(['route' => 'insurance_company.store']) !!}
                        @include('admin.rk-admin.insurance_companies.form', ['btn' => 'Add', 'classes' => 'btn-xs pull-right'])
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
