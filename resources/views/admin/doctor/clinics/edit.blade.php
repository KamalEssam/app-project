@extends('layouts.admin.admin-master')

@section('title', trans('lang.edit_clinic'))

@section('styles')
    {!! Html::style('assets/css/admin/form.css') !!}
@stop

@section('content')
    <div class="page-content mt-70">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-primary">
                    <div class="panel-body">
                        {!! Form::model($clinic, ['route' => ['clinics.update', $clinic->id], 'method' => 'PATCH']) !!}
                        @if(auth()->user()->role_id == $role_doctor)
                            <h1 class="font-18 loon">{{($auth->account->type == 0) ? (trans('lang.edit_clinic') .' : '. Super::min_address($clinic[app()->getLocale() . '_address'],35)) : (trans('lang.edit_clinic_poly') .' : '. $clinic[app()->getLocale() . '_name']) }}</h1>
                            <hr>
                            @include('admin.doctor.clinics.form', ['btn' => 'Save', 'classes' => 'btn-xs pull-right'])
                        @else
                            @include('admin.doctor.assistants.assistant-edit-form', ['btn' => 'Save', 'classes' => 'btn-xs pull-right'])
                        @endif
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
