@extends('layouts.admin.admin-master')

@section('title', trans('lang.create_category'))

@section('content')
    <div class="page-content mt-70">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <h1 class="font-18 loon">{{trans('lang.create_category')}}</h1>
                        <hr>
                        {!! Form::open(['route' => 'category.store', 'files' => true]) !!}
                        @include('admin.rk-admin.market-place.categories.form', ['btn' => 'Add', 'classes' => 'btn-xs pull-right'])
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
