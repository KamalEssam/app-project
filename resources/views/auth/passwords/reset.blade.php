@extends('layouts.frontend.auth')

@section('title', trans('lang.password_reset'))
@section('styles')
    {!! Html::style('/assets/css/auth/main.css') !!}
    {!! Html::style('/assets/css/auth/util.css') !!}
@endsection
@section('content')
    <div class="wrap-login100 p-l-50 p-r-50 p-t-40 p-b-50">
        <div class="row">
            <div class="auth-logo">
                <a href="/"><img src="{{ asset('assets/images/logo/logo-125.png') }}"></a>
            </div>
        </div>
        <span class="login100-form-title p-b-59 p-t-40 loon">{{trans('lang.reset_password')}}</span>

            {!! Form::open(['route' => 'resetPassword']) !!}
            {!! Form::hidden('token', $token) !!}
            {{ csrf_field() }}
            <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}"/>

                <div class="wrap-input100">
                    {{ Form::email('email', null, ['class'=>'input100' . ($errors->has('email') ? 'redborder' : '')  , 'id'=>'email', 'required' => 'required', 'placeholder'=>trans('lang.email')]) }}
                    <small class="text-danger">{{ $errors->first('email') }}</small>
                </div>
                <div class="wrap-input100">
                    {{ Form::password('password', ['class'=>'input100' . ($errors->has('password') ? 'redborder' : '')  , 'id'=>'password', 'required' => 'required', 'placeholder'=>trans('lang.password')]) }}
                    <small class="text-danger">{{ $errors->first('password') }}</small>
                </div>

                <div class="wrap-input100">
                    {{ Form::password('password_confirmation', ['class'=>'input100' . ($errors->has('password_confirmation') ? 'redborder' : '')  , 'id'=>'password_confirmation', 'required' => 'required', 'placeholder'=>trans('lang.confirm_password')]) }}
                    <small class="text-danger">{{ $errors->first('password_confirmation') }}</small>
                </div>

        <button type="submit"  id="login-btn" class="btn btn-custom btn-lg btn-block background-loon login100-form-btn">{{trans('lang.reset_password')}}</button>

        {!! Form::close() !!}
    </div>

@stop

{{--
<div>
    <div class="col-md-4 form-spaces login">
        <div class="row">
            <div class="auth-logo">
                <a href="/"><img src="{{ asset('assets/images/logo/logo-125.png') }}"></a>
            </div>
        </div>
        <div id="login">
            <h1 class="loon">{{trans('lang.reset_password')}}</h1>

            {!! Form::open(['route' => 'resetPassword']) !!}
            {!! Form::hidden('token', $token) !!}
            <div class="row">
                <div class="form-group">
                    {{ Form::email('email', null, ['class'=>'form-control ' . ($errors->has('email') ? 'redborder' : '')  , 'id'=>'email', 'required' => 'required', 'placeholder'=>trans('lang.email')]) }}
                    <small class="text-danger">{{ $errors->first('email') }}</small>
                </div>
            </div>
            <div class="row">
                <div class="form-group">
                    {{ Form::password('password', ['class'=>'form-control ' . ($errors->has('password') ? 'redborder' : '')  , 'id'=>'password', 'required' => 'required', 'placeholder'=>trans('lang.password')]) }}
                    <small class="text-danger">{{ $errors->first('password') }}</small>
                </div>
            </div>
            <div class="row">
                <div class="form-group">
                    {{ Form::password('password_confirmation', ['class'=>'form-control ' . ($errors->has('password_confirmation') ? 'redborder' : '')  , 'id'=>'password_confirmation', 'required' => 'required', 'placeholder'=>trans('lang.confirm_password')]) }}
                    <small class="text-danger">{{ $errors->first('password_confirmation') }}</small>
                </div>
            </div>
            <div class="row">
                <div class="form-group">

                    <button type="submit" class="btn btn-custom btn-lg btn-block background-loon">{{trans('lang.reset_password')}}</button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
--}}
