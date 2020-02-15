<!DOCTYPE html>
<html lang="en">
<head>
    @include('includes.admin.admin-header')
</head>
<body class="no-skin {{ (app()->getLocale() == 'ar') ? 'rtl' : '' }}">
@include('includes.admin.admin-navbar')

<div class="main-container ace-save-state" id="main-container">

    {{-- start toggle menu--}}
    <button type="button" class="navbar-toggle menu-toggler pull-left" id="menu-toggler" data-target="#sidebar">
        <span class="sr-only">Toggle sidebar</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </button>
    {{-- end toggle menu  --}}

    @include('includes.admin.admin-sidebar')
    <div class="main-content">

        <div class="main-content-inner">
            <div class="toggle-custom sidebar-collapse hidden-sm hidden-xs" id="sidebar-collapse">
                <i class="ace-icon fas fa-angle-left ace-save-state"></i>
            </div>
            <div class="flash-msg" id="flash-msg"></div>

            @if(Session::has('success'))
                <div class="alert alert-success text-center">
                    <button class="close" data-dismiss="alert">
                        <i class="ace-icon fa fa-times"></i>
                    </button>
                    <strong>{{ Session::get('success') }} <i class="fa fa-smile-o fa-lg"></i></strong>
                </div>
            @endif

            @if(Session::has('error'))
                <div class="alert alert-danger text-center">
                    <button class="close" data-dismiss="alert">
                        <i class="ace-icon fa fa-times"></i>
                    </button>
                    <strong>Sorry {{ Session::get('error') }} !</strong>
                </div>
            @endif
            {{-- check if user if aactive or not --}}
            @if($auth->is_active == 0)
                <div class="container renew-container">
                    <div class="row">
                        <div class="col-md-8 col-xs-offset-2">
                            <div class="suspended-card">
                                <div class="suspended-container">
                                    <strong class="loon center">Not Active</strong>
                                    <hr>
                                    <p class="center">An Activation request has been sent to admin please wait</p><br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                @yield('content')
            @endif
        </div>
        @include('includes.admin.admin-notification')
    </div>
    @if($auth->role_id == $role_doctor && $auth->is_premium == 0)
        @php  $services = (new \App\Http\Repositories\Web\DoctorServiceRepository())->getDoctorServices($auth->account_id);  @endphp
        Get all the services
        @php  $branches = \App\Models\Clinic::where('account_id',$auth->account_id)->get();  @endphp

        <div id="modal_premium" data-iziModal-title="{{ trans('lang.become_premium') }}"
             data-iziModal-subtitle="{{ trans('lang.add_premium_price') }}" data-iziModal-icon="icon-home">
            <div class="row" style="padding: 40px">
                @if(count($services) == 0)
                    <h3 class="text-center">you have to add at least one service</h3>
                @elseif(count($branches) == 0)
                    <h3 class="text-center">you have to add at least one branch</h3>
                @else
                    <form id="modal-form" action="{{ route('premium-requests.doctor-premium') }}" method="POST">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        Get all the services
                        @if(count($services) > 0)
                            <h3 class="text-center">services premium prices</h3>
                            @foreach($services as $service)
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <div class="row">
                                            <div class="col-md-6 label-form">
                                                <label
                                                    for="service_id">{{ $service['name'] }}</label>
                                            </div>
                                            <div class="col-md-6 form-input">
                                                {{ Form::hidden('service_id[]',$service->id)}}
                                                {{ Form::number('service_value[]' ,$service->premium_price , ['class'=>' form-control' . ($errors->has('height') ? 'redborder' : '') ,'min' => 0,'max' => ( ($service->price > 0) ? ($service->price - 1) : 0  ),'step' => '0.01','placeholder'=>trans('lang.premium_price'),'required']) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <h3 class="text-center">please add some services</h3>
                        @endif

                        @if(count($branches) > 0)
                            <h3 class="text-center">Branches premium fees</h3>
                            @foreach($branches as $branch)
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <div class="row">
                                            <div class="col-md-6 label-form">
                                                <label
                                                    for="branch_id">{{ ($auth->account->type == 0) ?  $branch[app()->getLocale() . '_address'] : $branch[app()->getLocale() . '_name'] }}</label>
                                            </div>
                                            {{ Form::hidden('branch_id[]',$branch->id)}}
                                            <div class="col-md-3 form-input">
                                                {{ Form::number('fees[]', $branch->premium_fees, ['class'=>' form-control' . ($errors->has('height') ? 'redborder' : '') ,'min' => 0,'max' => ( ($branch->fees > 0) ? ($branch->fees - 1) : 0  ),'step' => '0.01','placeholder'=>trans('lang.fees'),'required']) }}
                                            </div>
                                            <div class="col-md-3 form-input">
                                                {{ Form::number('follow_fees[]', $branch->premium_follow_up_fees , ['class'=>' form-control' . ($errors->has('height') ? 'redborder' : '') ,'min' => 0,'step' => '0.01','placeholder'=>trans('lang.follow_up_fees')]) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <h3 class="text-center">please add some branches</h3>
                        @endif

                        <div class="row">
                            <input type="submit" value="{{ trans('lang.save') }}"
                                   class="btn-modal-form-submit btn btn-primary btn-lg pull-right" id="submit-patient">
                        </div>
                    </form>
                @endif
                <div class="container" id="loading" style="display: none; !important;">
                    <div id="overlay" class="open">
                        <div class="display-loading open"></div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @include('includes.admin.admin-footer')
</div>

</body>
</html>




