@php
    $clinics = DB::table('clinics')->where('account_id', $auth->account_id)
        ->select('clinics.*')
        ->where('clinics.pattern' , 0)
        ->get();
    if ($auth->role_id == $role_assistant) {
        $clinic = $auth->clinic;
    }
@endphp

{{-- Reservation Part --}}
<li {{ Request::is('reservations*') ? 'class=active' : '' }}>
    <a href="{{ url('reservations/today') }}">
        <i class="menu-icon fa fa-calendar-minus"></i>
        <span class="menu-text"> {{trans('lang.reservations')}} </span>
    </a>
    <b class="arrow"></b>
</li>

{{-- TODO :: queue is not active now --}}
{{--<li {{ Request::is('queue') ? 'class=active' : '' }}>--}}
{{--    <a href="{{route('queue.index')}}">--}}
{{--        <i class="menu-icon fas fa-users"></i>--}}
{{--        <span class="menu-text"> {{trans('lang.queue')}} </span>--}}
{{--    </a>--}}
{{--    <b class="arrow"></b>--}}
{{--</li>--}}


<li {{ Request::is('working-hours') ? 'class=active' : '' }}>
    <a href="{{ url('working-hours') }}">
        <i class="menu-icon far fa-clock"></i>
        <span class="menu-text"> {{trans('lang.working_hours')}} </span>
    </a>
    <b class="arrow"></b>
</li>

<li {{ Request::is('clinics/'. $clinic->id .'/'.'edit' ) ? 'class=active' : '' }}>
    <a href="{{ route('clinics.edit', $clinic->id)}}">
        <i class="menu-icon fa fa-hospital"></i>
        <span class="menu-text"> {{trans('lang.clinic')}} </span>
    </a>
    <b class="arrow"></b>
</li>

<li {{ Request::is('holiday/index' ) ? 'class=active' : '' }}>
    <a href="{{ route('holiday.index')}}">
        <i class="menu-icon far fa-clock"></i>
        <span class="menu-text"> {{trans('lang.holiday')}} </span>
    </a>
    <b class="arrow"></b>
</li>
