@extends('layouts.admin.admin-master')

@section('title', trans('lang.edit_offer_category'))

@section('styles')
    {!! Html::style('assets/css/admin/form.css') !!}
@stop

@section('content')
    <div class="page-content mt-70">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <h1 class="font-18 loon">{{trans('lang.edit_offer_category') }}</h1>
                        <hr>
                        {!! Form::model($offerCategory, ['route' => ['offer_categories.update', $offerCategory->id], 'method' => 'PATCH','files' => true]) !!}
                            @include('admin.rk-admin.offer_categories.form', ['btn' => 'Save', 'classes' => 'btn-xs pull-right'])
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
