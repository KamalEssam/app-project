<li class="dropdown-header center">
    <i class="far fa-bell"></i>
    {{ trans('lang.notifications_list') }}
</li>

<li class="dropdown-content">
    <ul class="dropdown-menu dropdown-navbar">
        @if($auth->role_id == $role_assistant || $auth->role_id == $role_doctor)
            @php
                $notification_repo = new \App\Http\Repositories\Web\NotificationRepository();
            @endphp
            @if($notification_repo->getNotificationListWeb(1)->count() > 0)
                @foreach($notification_repo->getNotificationListWeb(1)->take(3) as $notification)
                    @php
                        if ($notification->sender_id) {
                            $sender = App\Models\User::where('id', $notification->sender_id)->first();
                        }
                    @endphp
                    <li style="background-color: {{ $notification->is_read == 0 ? '#c1ece57a' : '' }}">
                        <a href="{{ url('/') . $notification->url . $notification->id }}"
                           class="clearfix">
                            <img src=" {{  $sender->image}} "
                                 class="msg-photo notification-list-image"/>
                            <span class="msg-body">
					  <span class="msg-title">
                          {{ $notification[ app()->getLocale().'_message' ] }}
                      </span>
                        <span class="msg-time">
						<i class="far fa-clock"></i>
						<span>{{ \App\Http\Traits\DateTrait::readableDate($notification->created_at) }}</span>
						</span>
					</span>
                        </a>
                    </li>
                @endforeach
            @endif
        @else
            <h6 class="center">{{ trans('lang.no_notifications') }}</h6>
        @endif
    </ul>
</li>
<li class="dropdown-footer">
    <a href="{{ route('notifications.index') }}">
        {{ trans('lang.see_all_messages') }}
        <i class="ace-icon fa fa-arrow-right"></i>
    </a>
</li>
