<div id="navbar" class="navbar navbar-default  ace-save-state">
    <div class="navbar-container ace-save-state" id="navbar-container" style="position: relative;">
        <button type="button" class="navbar-toggle menu-toggler pull-left" id="menu-toggler" data-target="#sidebar">
            <span class="sr-only">Toggle sidebar</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        @php
            if ($auth->account_id != null) {
                $setting = (new \App\Http\Repositories\Web\DoctorDetailsRepository())->getDoctorDetailsByAccountId();
            }
            $rk_setting = \App\Http\Repositories\Web\SettingRepository::getFirstSetting();
        @endphp
        <div id="navbar" class="navbar navbar-default ace-save-state">
            {{--  premium text   --}}
            @if($auth->role_id == $role_doctor && $auth->is_premium == 0)
                <p class="premium-padge" data-iziModal-open="#modal_premium" title="become premium">become premium
                </p>
            @endif

            <div class="navbar-container ace-save-state" id="navbar-container">
                <div class="navbar-buttons navbar-header" role="navigation">
                    <ul class="nav ace-nav">
                        <li class="dropdown-modal">
                            <a data-toggle="dropdown" class="dropdown-toggle background-none notification-counter-click"
                               href="#">
                                <i class="ace-icon fas fa-bell icon-animated-vertical white font-20"></i>
                            </a>
                            <ul class="dropdown-menu-right dropdown-navbar dropdown-menu dropdown-caret dropdown-close"
                                id="notifications-list">
                                @include('includes.admin.notifications-list')
                            </ul>
                        </li>
                        <li class="light-blue dropdown-modal">
                            <a data-toggle="dropdown" href="#" class="dropdown-toggle">
                                <img class="nav-user-photo"
                                     src="{{ asset($auth->image) }}"/>
                                <span class="user-info">
									<small class="font-12 bolder">{{ trans('lang.hello').',' . $auth->name }}</small>

								</span>

                                <i class="ace-icon fas fa-sort-down font-18"></i>
                            </a>

                            <ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
                                <li>
                                    @if( $auth->role_id == $role_rk_admin || $auth->role_id == $role_rk_super_admin)
                                        <a href="{{ route('rk-settings.edit' , [ 'id' => $rk_setting->id ]) }}">
                                            <i class="ace-icon fa-fw fas fa-cog"></i>
                                            {{ trans('lang.settings') }}
                                        </a>
                                    @endif
                                </li>

                                <li>
                                    <a href="{{ route('profile.index') }}">
                                        <i class="ace-icon  fa-fw fas fa-user"></i>
                                        {{ trans('lang.profile') }}
                                    </a>
                                </li>

                                {{--   General Settings that includes language and notifications --}}
                                <li>
                                    <a href="{{ route('general-settings.edit') }}">
                                        <i class="ace-icon fa-fw fas fa-cogs"></i>
                                        {{ trans('lang.settings_general') }}
                                    </a>
                                </li>


                                <li>
                                    <a href="{{ route('get-change-password') }}">
                                        <i class="ace-icon fa-fw fas fa-unlock-alt"></i>
                                        {{ trans('lang.change_password') }}
                                    </a>
                                </li>

                                <li class="divider"></li>

                                <li>
                                    <a href="{{ route('logout') }}">
                                        <i class="ace-icon fa-fw fas fa-power-off"></i>
                                        {{ trans('lang.logout') }}
                                    </a>
                                </li>
                            </ul>
                        </li>

                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('more-scripts')
    <script>
        $('.notification-counter-click').on('click', function () {
            $('.notification-counter').hide();
            $.ajax({
                url: URL + '/notifications/last-click',
                type: 'POST',
                data: {_token: token}
            }).done(function (data) {
                if (data.status == true) {
                    $('#notifications-list').load(URL + '/notifications/list');
                    $('.notification-counter').load(URL + '/notifications/counter');
                }
            });
        });
    </script>
@endpush

