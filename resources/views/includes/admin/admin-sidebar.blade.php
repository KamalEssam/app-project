<script type="text/javascript">
    try {
        ace.settings.loadState('main-container')
    } catch (e) {
    }
</script>

<div id="sidebar" class="sidebar responsive ace-save-state">
    <script type="text/javascript">
        try {
            ace.settings.loadState('sidebar')
        } catch (e) {
        }
    </script>
    <?php
    $same_accounts = \App\Models\User::where('account_id', $auth->account_id)->where('role_id', $role_doctor)->first();
    if ($same_accounts) {
        $setting = \App\Models\DoctorDetail::where('account_id', $same_accounts->account_id)->orderBy('created_at')->first();
    }
    $rk_setting = \App\Models\Setting::first();
    ?>

    <div class="sidebar-wrapper">
        <ul class="nav nav-list nav-pulled-top">
            <li class="hidden-sm hidden-xs" id="sidebar-logo">
                <a href="{{ route('admin') }}" class="navbar-brand">
                    <img src="{{ asset('assets/images/logo/logo-height-65.png') }}">
                </a>
            </li>

            <div class="space-32"></div>

            <li class="{{ Request::is('/') ? 'active dashboard-space' : 'dashboard-space' }}">
                <a href="{{route('admin')}}">
                    <i class="menu-icon fa fa-chart-pie"></i>
                    <span class="menu-text"> {{ trans('lang.dashboard') }} </span>
                </a>
                <b class="arrow"></b>
            </li>
            @switch($auth->role_id)
                @case(1) {{--// specially for Doctor--}}
                @include('includes.admin.sidebar.doctor')
                @include('includes.admin.sidebar.doctor-assistant')
                @break
                @case(2) {{--// for doctor and assistant--}}
                @include('includes.admin.sidebar.assistant')
                @include('includes.admin.sidebar.doctor-assistant')
                @break
                {{--// for Rk admin and rk super admin--}}
                @case(4)
                @case(5)
                @include('includes.admin.sidebar.rk-admin')
                @break
                @case(6)
                @include('includes.admin.sidebar.sales')
                @break
                @default
            @endswitch


        </ul><!-- /.nav-list -->
        <ul class="nav nav-list bottom-list">
            <ul class="dropdown-menu profile-dropmenu" aria-labelledby="profile-dropmenu">
                <li>{{ $auth->name }}</li>
                <li class="divider" style="padding: 1px !important;"></li>
                <li><a href="{{ route('profile.index') }}">{{ trans('lang.profile') }}</a></li>
                <li>
                    @if( $auth->role_id == $role_doctor || $auth->role_id == $role_assistant )
                        <a href="{{ route('clinic-settings.edit' , ['id'=> $setting->id ]) }}">
                            {{ trans('lang.settings') }}
                        </a>
                    @elseif( $auth->role_id == $role_rk_admin || $auth->role_id == $role_rk_super_admin)
                        <a href="{{ route('rk-settings.edit' , [ 'id' => $rk_setting->id ]) }}">
                            {{ trans('lang.settings') }}
                        </a>
                    @endif
                    <b class="arrow"></b>
                </li>
                <li><a href="{{ url('/manger/changepassword') }}">{{ trans('lang.change_password') }}</a></li>
                <li><a href="{{ route('logout') }}">{{ trans('lang.logout') }}</a></li>
            </ul>
            </li>
        </ul>

    </div>
</div>
