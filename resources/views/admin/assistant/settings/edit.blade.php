@extends('layouts.admin.admin-master')

@section('title', trans('lang.edit_settings'))

@section('styles')
    {!! Html::style('assets/css/admin/form.css') !!}
@stop


@section('content')
    <div class="page-content mt-70">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <h1 class="font-18 loon">{{ trans('lang.edit_settings') }}</h1>
                        <hr>
                        {!! Form::model($setting, ['route' => ['clinic-settings.update', $setting->id], 'method' => 'PATCH', 'files' => true]) !!}
                        @include('admin.assistant.settings.form', ['btn' => 'Save', 'classes' => 'btn-xs pull-right'])
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script>
        $(document).ready(function () {
            $('.generate-password').on('click', function (e) {
                e.preventDefault();
                var randomstring = Math.random().toString(36).slice(-8);
                $('.generated-passowrd').text(randomstring);
            });
        });
    </script>
@stop