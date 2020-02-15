@extends('layouts.admin.admin-master')

@section('title', trans('lang.edit_policy') )

@section('styles')
    {{-- {!! Html::style('css/admin/events.css') !!} --}}
    {!! Html::style('assets/css/admin/select2.min.css') !!}
    {!! Html::style('assets/css/admin/form.css') !!}
@stop


@section('content')

    {{-- {{ dd(Super::doctorSelect()) }} --}}

    <div class="page-content mt-70">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <h1 class="font-18 loon">{{ trans('lang.edit_policy') . $policy[app()->getLocale() . '_name'] }}</h1>
                        <hr>
                        {!! Form::model($policy, ['route' => ['policies.update', $policy->id], 'files' => true, 'method' => 'PATCH']) !!}
                        @include('admin.rk-admin.policies.form', ['btn' => 'Save', 'classes' => 'btn-xs pull-right'])
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