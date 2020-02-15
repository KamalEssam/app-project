<?php
$clinics = DB::table('clinics')->where('account_id', $auth->account_id)->get();
?>
<li {{ Request::is('clinics*') ? 'class=active' : '' }} id="clinics-tab">
    <a href="{{route('clinics.index')}}">
        <i class="menu-icon fa fa-hospital"></i>
        <span
            class="menu-text"> {{ ($auth->account->type == 0) ? trans('lang.clinics') : trans('lang.clinics_poly')}}  </span>
    </a>
    <b class="arrow"></b>
</li>

@if($auth->account->type != 1)
    <li {{ Request::is('assistants*') ? 'class=active' : '' }}>
        <a href="{{route('assistants.index')}}">
            <i class="menu-icon fa fa-user-md"></i>
            <span class="menu-text"> {{trans('lang.assistants')}} </span>
        </a>
        <b class="arrow"></b>
    </li>
@endif



<li class="{{ Request::is('reservations/today') ? 'active open' : ''}}">
    <a href="#" class="dropdown-toggle">
        <i class="menu-icon fa fa-calendar"></i>
        <span class="menu-text"> {{ trans('lang.reservations') }}   </span>
        @if($clinics->count() > 0)
            <b class="arrow fa fa-angle-down"></b>
        @endif
    </a>

    @if($clinics->count() > 0)
        <b class="arrow"></b>

        <ul class="submenu nav-hide">
            @foreach( $clinics as $clinic)
                <li class="{{ isset($_GET['clinic']) && $_GET['clinic'] == $clinic->id  ? 'active' : '' }}">
                    <a href="{{ url('reservations/today?clinic='. $clinic->id) }}">
                        <i class="menu-icon fa fa-bullseye"></i>
                        <span
                            class="menu-text"> {{ ($auth->account->type == 0) ?  Super::min_address($clinic->{app()->getLocale() .'_address'}) : $clinic->{app()->getLocale() . '_name'} }} </span>
                    </a>
                    <b class="arrow"></b>
                </li>
            @endforeach
        </ul>
    @endif
</li>


{{--  TODO :: queue commented until we decide to use it again--}}
{{--@if($auth->account->type != 1)--}}
{{--    <li class="{{ Request::is('queue/doctor') ? 'active open' : ''}}">--}}
{{--        <a href="#" class="dropdown-toggle">--}}
{{--            <i class="menu-icon fa fa-calendar"></i>--}}
{{--            <span class="menu-text"> {{ trans('lang.queue') }}   </span>--}}

{{--            @if($clinics->count() > 0)--}}
{{--                <b class="arrow fa fa-angle-down"></b>--}}
{{--            @endif    </a>--}}

{{--        @if($clinics->count() > 0)--}}
{{--            <b class="arrow"></b>--}}
{{--            <ul class="submenu nav-hide">--}}
{{--                @foreach( $clinics as $clinic)--}}
{{--                    <li class="{{ isset($_GET['clinic']) && $_GET['clinic'] == $clinic->id  ? 'active' : '' }}">--}}
{{--                        <a href="{{ url('queue/doctor?clinic='. $clinic->id) }}">--}}
{{--                            <i class="menu-icon fa fa-bullseye"></i>--}}
{{--                            <span class="menu-text"> {{ Super::min_address($clinic->en_address) }} </span>--}}
{{--                        </a>--}}
{{--                        <b class="arrow"></b>--}}
{{--                    </li>--}}
{{--                @endforeach--}}
{{--            </ul>--}}
{{--        @endif--}}
{{--    </li>--}}
{{--@endif--}}


<li class="{{ Request::is('working-hours') ? 'active open' : ''}}">
    <a href="#" class="dropdown-toggle">
        <i class="menu-icon far fa-clock"></i>
        <span class="menu-text"> {{ trans('lang.working_hours') }}   </span>
        @if($clinics->count() > 0)
            <b class="arrow fa fa-angle-down"></b>
        @endif    </a>

    @if($clinics->count() > 0)
        <b class="arrow"></b>
        <ul class="submenu nav-hide">
            @foreach( $clinics as $clinic)
                <li class="{{ isset($_GET['clinic']) && $_GET['clinic'] == $clinic->id  ? 'active' : '' }}">
                    <a href="{{ url('working-hours?clinic='. $clinic->id) }}">
                        <i class="menu-icon fa fa-bullseye"></i>
                        <span
                            class="menu-text"> {{ ($auth->account->type == 0) ?  Super::min_address($clinic->{app()->getLocale() .'_address'}) : $clinic->{app()->getLocale() . '_name'} }} </span>
                    </a>
                    <b class="arrow"></b>
                </li>
            @endforeach
        </ul>
    @endif
</li>

@if($auth->account->type == 1)
    <li {{ Request::is('holiday/index' ) ? 'class=active' : '' }}>
        <a href="{{ route('holiday.index')}}">
            <i class="menu-icon far fa-clock"></i>
            <span class="menu-text"> {{trans('lang.holiday')}} </span>
        </a>
        <b class="arrow"></b>
    </li>
@endif

<li {{ Request::is('doctor-services*') ? 'class=active' : '' }}>
    <a href="{{route('doctor-services.index')}}">
        <i class="menu-icon fas fa-medkit"></i>
        <span class="menu-text"> {{trans('lang.services')}}  </span>
    </a>
    <b class="arrow"></b>
</li>


<li {{ Request::is('gallery*') ? 'class=active' : '' }}>
    <a href="{{route('gallery.index')}}">
        <i class="menu-icon fas fa-image"></i>
        <span class="menu-text"> {{trans('lang.gallery')}}  </span>
    </a>
    <b class="arrow"></b>
</li>


{{--  Doctor Finintials --}}
<li {{ Request::is('financial*') ? 'class=active' : '' }}>
    <a href="{{route('financial.report')  . '?year=' . date('Y') . '&month=' . date('m')}}">
        <i class="menu-icon fas fa-money-bill-alt"></i>
        <span class="menu-text"> {{trans('lang.financial')}}  </span>
    </a>
    <b class="arrow"></b>
</li>
