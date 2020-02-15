@extends('layouts.admin.admin-master')

@section('title', 'Change Password')

@section('styles')
    {!! Html::style('assets/css/admin/form.css') !!}
@stop


@section('content')
    <div class="page-content mt-70">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <div class="col-md-12">
                            <h1 class="font-18 loon">{{ trans('lang.change_password') }}</h1>
                            <hr>
                        </div>
                        {!! Form::open(['route' => 'post-change-password', 'class' => 'form']) !!}
                        @include('auth.passwords.change-form', ['btn' => 'Save', 'classes' => 'btn-xs pull-right'])
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')

@stop