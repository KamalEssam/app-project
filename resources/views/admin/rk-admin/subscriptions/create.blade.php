@extends('layouts.admin.admin-master')

@section('title', trans('lang.send_email'))

@section('styles')
    {!! Html::style('assets/css/admin/form.css') !!}
@stop


@section('content')
    <div class="page-content mt-70">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <h1 class="font-18 loon">{{trans('lang.send_email')}}</h1>
                        <hr>
                        {!! Form::open(['route' => 'subscriptions.store', 'files' => true ]) !!}
                        @include('admin.rk-admin.subscriptions.form', ['btn' => 'Send', 'classes' => 'btn-xs pull-right send-btn'])
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    {!! Html::script('assets/js/admin/events.js') !!}
    {!! Html::script('assets/js/admin/select2.min.js') !!}
@stop