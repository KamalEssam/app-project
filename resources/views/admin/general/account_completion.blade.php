@extends('layouts.admin.guest')
@section('title', trans('lang.complete_account_data'))
@section('content')
    <div class="container">
        <div class="text-center mt-15">
            <p class="lead capitalized">welcome {{ $auth->name }} <a href="{{ route('logout') }}">logout</a></p>
        </div>
        <div class="row">
            <section>
                <h3 class="text-center">please complete the following steps first</h3>
                <div class="wizard">
                    <div class="wizard-inner">
                        <div class="connecting-line poly"></div>
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation"
                                class="poly_card {{  ($steps[2]['completed'] == true) ? ($active_number == 1 ? 'active ': ' ')  : ' disabled'  }}">
                                <a href="#step1" data-toggle="tab" aria-controls="step1" role="tab"
                                   title="Step 1 add clinics">
                            <span class="round-tab">
                               <i class="fas fa-briefcase-medical"></i>
                            </span>
                                </a>
                            </li>
                            <li role="presentation"
                                class="poly_card {{  ($steps[1]['completed'] == true || $active_number == 2) ? ($active_number == 2 ? 'active ': ' ')  : ' disabled'  }}">
                                <a href="#step2" data-toggle="tab" aria-controls="step2" role="tab"
                                   title="Step 2 add working hours">
                            <span class="round-tab">
                                <i class="far fa-clock"></i>
                            </span>
                                </a>
                            </li>
                            <li role="presentation"
                                class="poly_card {{  ($steps[0]['completed'] == true) ? ($active_number == 3 ? 'active ': ' ')  : ' disabled'  }}">
                                <a href="#complete" data-toggle="tab" aria-controls="complete" role="tab"
                                   title="Complete some extra data">
                            <span class="round-tab">
                                <i class="far fa-file-alt"></i>
                            </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-content">
                        <div
                            class="tab-pane {{  $active_number == 1 ? 'active ' : (($steps[2]['completed'] == true) ? '' : 'disabled') }}"
                            role="tabpanel" id="step1">
                            @php $current_clinics = \App\Models\Clinic::where('account_id',$auth->account_id)->get()  @endphp
                            @if(count($current_clinics) > 0)
                                <div class="text-center">
                                    <h3 style="color: #35c7e0;">Added Clinics</h3>
                                    <ul class="list-unstyled">
                                        @foreach($current_clinics as $current_clinic)
                                            <li>#{{ ++$loop->index }} - {{ $current_clinic->en_address }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <ul class="list-inline pull-right">
                                <li>
                                    @if( $current_clinics->count() > 0)
                                        <button type="button" class="btn btn-primary next-step">continue <i
                                                class="fas fa-arrow-right"></i></button>
                                    @endif
                                </li>
                            </ul>
                            <h3 class="text-center">add clinic</h3>
                            <div class="row">
                                <div class="col-md-8 col-md-offset-2">
                                    <div class="panel panel-primary">
                                        <div class="panel-body">
                                            {!! Form::open(['route' => 'clinics.store']) !!}
                                            @include('admin.doctor.clinics.form', ['btn' => 'Add', 'classes' => 'btn-sm pull-right'])
                                            {!! Form::close() !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div
                            class="tab-pane {{ $active_number == 2 ? 'active ' : (($steps[1]['completed'] == true) ? '' : 'disabled')  }}"
                            role="tabpanel" id="step2">
                            {{-- Choose Clinic from DropDown List --}}
                            <div class="row">
                                <div class="col-md-6 col-md-offset-3">
                                    {{ Form::select('clinic_id', \App\Models\Clinic::where('account_id', $auth->account_id)->pluck( app()->getLocale() . '_address' ,'id') ,(isset($_GET['clinic']) && is_numeric($_GET['clinic'])) ? $_GET['clinic'] : null, ['class'=>'form-control ' . ($errors->has('clinic_id') ? 'redborder' : '') , 'required'=>'required', 'id'=>'select_clinic','placeholder' => 'select clinic']) }}
                                </div>
                            </div>
                            <ul class="list-inline pull-right">
                                <li>
                                    <button type="button" class="btn btn-default prev-step"><i
                                            class="fas fa-arrow-left"></i> Previous
                                    </button>
                                </li>
                                <li>
                                    @if((isset($clinic, $upcoming_workingHours) && count($upcoming_workingHours) > 0) || ((isset($days) && count($days) > 0)))
                                        <button type="button" class="btn btn-primary btn-info-full next-step">
                                            continue <i class="fas fa-arrow-right"></i>
                                        </button>
                                    @endif
                                </li>
                            </ul>
                            @if(isset($clinic))
                                <div class="page-content mt-70">
                                    <div class="row">
                                        <div class="col-md-8 col-md-offset-2">
                                            <div class="panel panel-primary">
                                                <div class="panel-body">
                                                    <h1 class="font-18 loon">{{trans('lang.create_working_hour')}}</h1>
                                                    <hr>
                                                    {!! Form::open(['route' => 'working-hours.store', 'files' => true,  'class' => 'wh-form']) !!}
                                                    @include('admin.assistant.working-hours.form', ['btn' => trans('lang.save'), 'classes' => 'btn-xs pull-right add-btn add-working-hours'])
                                                    {!! Form::close() !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-center mt-70">
                                    Please Select Clinic From Above First
                                </div>
                            @endif
                        </div>
                        <div
                            class="tab-pane {{ $active_number == 3 ? 'active ' :  (($steps[0]['completed'] == true) ? '' : 'disabled') }}"
                            role="tabpanel"
                            id="complete">
                            @php
                                $profile = (new \App\Http\Repositories\Web\UserRepository())->getUserById($auth->id);
                                // get doctor data if not admin or super admin
                                if ($auth->role_id != $role_rk_admin && $auth->role_id != $role_rk_super_admin) {
                                    $profile = (new \App\Http\Repositories\Web\UserRepository())->getDoctorAccountData($profile, 1);
                                }
                            @endphp
                            <ul class="list-inline pull-right">
                                <li>
                                    <button type="button" class="btn btn-default prev-step"><i
                                            class="fas fa-arrow-left"></i> Previous
                                    </button>
                                </li>
                            </ul>
                            <div class="page-content mt-70">
                                <div class="row">
                                    <div class="col-md-8 col-md-offset-2">
                                        <div class="panel panel-primary">
                                            <div class="panel-body">
                                                <h1 class="font-18 loon">{{trans('lang.edit_profile').' : '. $profile->name  }}</h1>
                                                <hr>
                                                {!! Form::model($profile, ['route' => ['profile.update', $profile->id], 'method' => 'PATCH', 'files' => true]) !!}
                                                @include('admin.profile.form', ['btn' => 'Save', 'classes' => 'btn-sm pull-right'])
                                                {!! Form::close() !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@stop
@push('more-scripts')
    <script>
        $(document).ready(function () {
            //Initialize tooltips
            $('.nav-tabs > li a[title]').tooltip();

            //Wizard
            $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
                var $target = $(e.target);
                if ($target.parent().hasClass('disabled')) {
                    return false;
                }
            });

            $(".next-step").click(function (e) {
                var $active = $('.wizard .nav-tabs li.active');
                $active.next().removeClass('disabled');
                nextTab($active);
            });

            $(".prev-step").click(function (e) {

                var $active = $('.wizard .nav-tabs li.active');
                prevTab($active);

            });
        });

        function nextTab(elem) {
            $(elem).next().find('a[data-toggle="tab"]').click();
        }

        function prevTab(elem) {
            $(elem).prev().find('a[data-toggle="tab"]').click();
        }

        $(document).on('change', '#select_clinic', function (e) {
            window.location = URL + '/account/complete-steps?clinic=' + $(this).val();
        });
    </script>
@endpush
