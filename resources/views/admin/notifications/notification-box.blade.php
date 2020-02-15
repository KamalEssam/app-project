<div class="col-xs-6 col-xs-offset-3">
    <div id="append-load-more">
        <div class="clearfix">
            <div class="pull-right tableTools-container"></div>
        </div>

        @php
            \Log::info($notifications);
        @endphp

        @foreach( $notifications as $notification )
            <a href="{{ url('/') . $notification->url . $notification->id }}" class="grey">
                <div class="notice" style="background-color: {{ $notification->is_read == 0 ? '#c1ece57a' : '' }}">
                    <img src="{{ $notification->sender->image }}"
                         class="mr-10 notification-list-image">
                    {{ $notification[app()->getLocale() . '_message'] }}
                </div>
            </a>
        @endforeach
    </div>
</div>




