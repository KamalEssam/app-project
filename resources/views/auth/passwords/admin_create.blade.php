@extends('layouts.login.login-page-layout')

@section('title',  trans('lang.create_password'))

@section('content')
    <div class="page-content">
        <div class="page-header">
            <div class="row">
                <div class="col-md-12">
                    <h1 class="text-center">{{ trans('lang.create_password') }}</h1>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="panel panel-primary">
                    <div class="panel-body">
                        {!! Form::open(['route' => ['setPassword' , $user->unique_id], 'class' => 'form']) !!}

                        <div class="row">
                            <div class="form-group col-md-12 has-float-label">
                                {{ Form::password('password', ['class'=>'form-control ' . ($errors->has('password') ? 'redborder' : '')  , 'id'=>'password', 'required' => 'required', 'placeholder'=>'Password']) }}
                                <small class="text-danger">{{ $errors->first('password') }}</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12 has-float-label">
                                {{ Form::password('password_confirmation', ['class'=>'form-control ' . ($errors->has('password_confirmation') ? 'redborder' : '')  , 'id'=>'password_confirmation', 'required' => 'required', 'placeholder'=>'Confirm Password']) }}
                                <small class="text-danger">{{ $errors->first('password_confirmation') }}</small>
                            </div>
                        </div>

                        {{ Form::hidden('role_id',$user->role_id) }}

                        {{  Form::submit('Save' , ['class' => 'btn btn-success btn-block btn-sm']) }}

                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')

@stop