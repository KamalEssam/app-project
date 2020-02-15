@extends('layouts.admin.admin-master')

@section('title',  trans('lang.manage_notifications') )


@section('content')
    <div class="page-content">
        <div class="page-header">
            <div class="row">
                <div class="col-md-11">
                    <h1>{{ trans('lang.notifications') }}</h1>
                </div>
            </div>
        </div>
        <div class="row" id="notification-list">
            @include('admin.notifications.notification-box')
        </div>
        @if(count($notifications))
            <div class="load-more text-center mt-20">
                <a href="#" class="btn btn-loon font-11" id="btn-load-more" data-offset="0">
                    <i class="fas fa-spinner loon mr-5"></i>{{ trans('lang.load_more') }}</a>
            </div>
        @else
            <div class="empty">{{trans('lang.no_notifications')}}</div>
        @endif
    </div>
@stop

@section('scripts')
    <script>
        $(document).ready(function () {

            offset = {{ count($notifications) }};   // check this later

            $(document).on('click', '#btn-load-more', function (e) {

                e.preventDefault();
                //var offset = $(this).data('offset');
                var multicast = $(this).data('multicast');
                //console.log($('.main-content-inner').height());
                $.ajax({
                    url: URL + '/notifications-list-load-more',
                    type: 'GET',
                    data: {_token: token, offset: offset, multicast: multicast}
                }).done(function (data) {

                    $('#notification-list').append(data.data);
                    offset += 10;

                    if (data.count === 0) {
                        $('#btn-load-more').hide();
                    }
                });

            });

        });
    </script>
@stop
