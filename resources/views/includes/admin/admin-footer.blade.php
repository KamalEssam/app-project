@php
    $logged_doctor_account = \App\Models\Account::where('id', $auth->account_id)->first();
@endphp
<div class="footer">
    <div class="footer-inner">
        <div class="row">
            <div class="col-md-10 col-md-offset-2">
                <div class="footer-content no-padding">
					<span class="blue bolder">Seena-app
                        Developed by <a href="https://rkanjel.com" target="_blank" class="loon">RK Anjel</a> &copy; {{ date('Y') }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    @if($auth->role_id == $role_doctor)
        <nav class="navbar-fixed-bottom" style="z-index: 1">
            <div class="container text-center">
                @if($auth->role_id == $role_doctor && in_array($auth->account->is_published,[0,2]) && $auth->is_active == 1)
                    <div class="row">
                        <div class="col-md-10 col-md-offset-2">
                            @if($auth->account->is_published == 0)
                                <h5>please publish your account in order to be seen by users <b> <a
                                            href="{{url('accounts/'.$auth->account->id.'/publish/true')  }}"
                                            class="btn btn-primary"
                                            title="
                                            please first
{{($auth->account->type == 0) ? '
- add image
- add clinic name
- add arabic and english bio
- add at least one clinic
- add at least one day working hours
- add at least one service
' :'
- add image
- add center name from edit profile
- add arabic and english bio for center
- add at least one clinic
- add at least one day working hours
- add at least one service
' }}thank you
"
                                            target="_self">publish</a>
                                    </b></h5>
                            @else
                                <h5> Your Account is waiting for Approval, we will contact you soon</h5>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </nav>
    @endif
</div>

<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
    <i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
</a>

@include('includes.admin.admin-scripts')

{{-- Notification Scripts --}}
@include('includes.admin.sidebar.notifications-scripts')
