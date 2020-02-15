<li {{ Request::is('patients*') ? 'class=active' : '' }}>
    <a href="{{ route('patients.index') }}">
        <i class="menu-icon fa fa-users"></i>
        <span class="menu-text"> {{ trans('lang.patients') }} </span>
    </a>
    <b class="arrow"></b>
</li>

{{--<li {{ Request::is('admin/visits*') ? 'class=active' : '' }}>--}}
    {{--<a href="{{route('visits.index')}}">--}}
        {{--<i class="menu-icon fa fa-history"></i>--}}
        {{--<span class="menu-text"> {{trans('lang.visits_history')}}  </span>--}}
    {{--</a>--}}
    {{--<b class="arrow"></b>--}}
{{--</li>--}}