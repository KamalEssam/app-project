@extends('layouts.admin.admin-master')

@section('title', trans('lang.edit_visit'))

@section('styles')
    {!! Html::style('assets/css/admin/select2.min.css') !!}
    {!! Html::style('assets/css/admin/form.css') !!}
@stop

@section('content')
    <div class="page-content mt-70">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-3">
                                <h1 class="font-18 loon">{{trans('lang.current_queue')}}</h1>
                            </div>
                            @php
                                if(isset($reservation)){
                                    $visit = (new \App\Http\Repositories\Web\VisitRepository())->getVisitByReservationId($reservation->id);
                                }
                            @endphp
                            @if( $queue )
                                @if( $reservation )
                                    @if(!$visit)
                                        @if($patient->visits)
                                            <div class="col-md-3 col-md-offset-6 mt-15">
                                                <a class="pull-right btn-xs pull-right btn-loon"
                                                   href="{{route('visits.show' , [$patient->id])}}">
                                                    {{ trans('lang.show') }}
                                                </a>
                                            </div>
                                        @endif
                                    @endif
                                @endif
                            @endif
                        </div>
                        <hr>
                        @if( $queue )
                            @if( $reservation )
                                @if(!$visit)
                                    @if( $auth->role_id == $role_doctor )
                                        {!! Form::open(['route' => ['visits.store', $reservation->id],'method' => 'POST']) !!}
                                        @include('admin.doctor.queues.form', ['btn' => 'Add', 'classes' => 'btn-xs pull-right'])
                                        {!! Form::close() !!}
                                    @endif
                                @else
                                    <div class="row mt-100">
                                        <div class="col-xs-2 ml-38felmaya mb-20"><img src="{{ asset('assets/images/no_data/no_queue.png') }}"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 col-xs-6 ml-35felmaya"><p class="loon no_data">{{trans('lang.no_queue')}}</p></div>
                                    </div>
                                @endif
                            @else
                                <div class="row mt-100">
                                    <div class="col-xs-2 ml-38felmaya mb-20"><img src="{{ asset('assets/images/no_data/no_queue.png') }}"></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 col-xs-6 ml-35felmaya"><p class="loon no_data">{{trans('lang.no_queue')}}</p></div>
                                </div>
                            @endif
                        @else
                            <div class="row">
                                <div class="col-xs-12 text-center"><img class="no_data_image"
                                                                        src="{{ asset('assets/images/no_data/no_queue.png') }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 text-center"><p
                                            class="loon no_data">{{trans('lang.no_queue')}}</p></div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

