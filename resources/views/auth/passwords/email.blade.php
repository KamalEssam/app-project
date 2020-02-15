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
        <span class="login100-form-title p-b-59 p-t-40 loon">
						{{ trans('lang.forget_password') }}
					</span>
        <form class="login-form" action="{{route('sendResetEmail')}}" method="post" id="login100-form">
            {{ csrf_field() }}
            <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}"/>
            <div class="wrap-input100">
                <input type="text" name="email" id="login" class="input100"
                       placeholder="somebody@example.com">
                <small class="red">{{  $errors->has('email') ? $errors->first('email') : '' }}</small>
            </div>
            <input type="submit" id="login-btn"
                   class="btn btn-custom btn-lg btn-block background-loon login100-form-btn"
                   value={{trans('lang.send_confirmation_email')}}>
        </form>
    </div>
@stop

{{--<div>--}}
{{--<div class="col-md-4 form-spaces login">--}}
{{--<div id="login">--}}
{{--<h1 class="loon">Forget Password</h1>--}}

{{--<form class="login-form" action="{{route('sendResetEmail')}}" method="post">--}}

{{--{{ csrf_field() }}--}}
{{--<input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />--}}

{{--<div class="form-group">--}}
{{--<input type="email" name="email" class="form-control" placeholder="{{trans('lang.email')}}" >--}}
{{--<small class="red">{{  $errors->has('email') ? $errors->first('email') : '' }}</small>--}}
{{--</div>--}}

{{--<div class="form-check">--}}
{{--<button type="submit" class="btn btn-custom btn-lg btn-block background-loon">{{trans('lang.send_confirmation_email')}}</button>--}}
{{--</div>--}}
{{--</form>--}}

{{--@if(Session::has('status'))--}}
{{--<div class="mt-100 text-center alert alert-success flash">--}}
{{--{{  Session::get('status') }}--}}
{{--</div>--}}
{{--@endif--}}
{{--<div class="row">--}}
{{--<div class="auth-logo">--}}
{{--<a href="/"><img src="{{ asset('assets/images/logo/logo.png') }}"></a>--}}
{{--</div>--}}
{{--</div>--}}
{{--</div>--}}
{{--</div>--}}
{{--</div>--}}
