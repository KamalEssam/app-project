<div class="page-content">
    <div class="page-header">
        <h1 class="text-center">{{ trans('lang.dashboard') }}</h1>
    </div>
    <div class="row">
        @php
            $today = Carbon\Carbon::now('Africa/Cairo')->format('Y-m-d');
            if ($auth->role_id == 1) {
                $width = ($auth->account->type == 1 && $auth->role_id == $role_doctor) ? '4' : '3';
            } else {
                $width = '3';
            }
        @endphp

        {{--   super and admin    --}}
        @if($auth->role_id == $role_rk_admin || $auth->role_id == $role_rk_super_admin)
            @include('admin.dashboard.super-and-admin-dashboard')
        @endif

        {{-- In case of the Doctors Profiles --}}
        {{--    Doctor    --}}
        @if($auth->role_id == $role_doctor)
            @include('admin.dashboard.doctor-dashboard', ['with' => $width])
        @endif
    </div>
</div>
