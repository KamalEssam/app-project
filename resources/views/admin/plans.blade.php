@extends('layouts.home.master')

<!-- comment -->
@section('title', trans('lang.plans'))


@section('content')
    <div class="container">
        <div class="row">
            @foreach($plans as $plan)
                <div class="col-xs-4">
                    <div class="project project-radius project-primary">
                        <div class="shape">
                            <div class="shape-text">
                                {{ $plan->days }}{{ trans('lang.days') }}
                            </div>
                        </div>
                        <div class="project-content">
                            <h3 class="lead">
                                {{ $plan[App::getLocale() . '_name'] }}
                            </h3>
                            <p>
                                {{ $plan->is_unlimited == 1 ? "&infin;" : $plan->no_of_clinics }} {{ trans('lang.clinics') }}
                            </p>
                            <p>
                                {{ $plan->is_unlimited == 1 ? "&infin;" : $plan->no_of_assistants }} {{ trans('lang.assistants') }}
                            </p>
                            <p>
                                {{ $plan->price . ' '}}{{ trans('lang.l_e') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach

        </div><!--/row-->
    </div><!--/container -->
@stop
