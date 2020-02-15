@extends('layouts.admin.admin-master')

@section('title', trans('lang.create_assistant'))

@section('styles')
    {!! Html::style('assets/css/admin/form.css') !!}
@stop


@section('content')
    <div class="page-content mt-70">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <h1 class="font-18 loon">{{trans('lang.create_assistant')}}</h1>
                        <hr>
                        {!! Form::open(['route' => 'assistants.store']) !!}
                            @include('admin.doctor.assistants.form', ['btn' => 'Add', 'classes' => 'btn-xs pull-right'])
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