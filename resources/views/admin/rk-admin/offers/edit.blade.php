@extends('layouts.admin.admin-master')

@section('title', trans('lang.edit_offer'))

@section('styles')
    {!! Html::style('assets/css/admin/form.css') !!}
@stop

@section('content')
    <div class="page-content mt-70">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <h1 class="font-18 loon">{{trans('lang.edit_offer') }}</h1>
                        <hr>
                        {!! Form::model($offer, ['route' => ['offers.update', $offer->id], 'method' => 'PATCH','files' => true]) !!}
                        @include('admin.rk-admin.offers.form', ['btn' => 'Save', 'classes' => 'btn-xs pull-right add_offer '])
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
