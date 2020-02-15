@extends('layouts.admin.admin-master')

@section('title', trans('lang.create_offer'))

@section('styles')
    {!! Html::style('assets/css/admin/form.css') !!}
    {!! Html::style('assets/css/admin/jquery-ui.min.css') !!}
    <style>
        .chosen-container-single .chosen-search:after {
            content: none !important;
        }
    </style>
@stop

@section('content')
    <div class="page-content mt-70">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <h1 class="font-18 loon">{{trans('lang.create_offer')}}</h1>
                        <hr>
                        {!! Form::open(['route' => 'offers.store','files' => true]) !!}
                        @include('admin.rk-admin.offers.form', ['btn' => 'Add', 'classes' => 'btn-xs pull-right add_offer '])
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
