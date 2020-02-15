@extends('layouts.admin.admin-master')

@section('title', trans('lang.manage_queue'))

@section('content')
    <div class="page-content mt-70">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <h1 class="font-18 loon">{{trans('lang.manage_queue')}}</h1>
                        <hr>
                        <div id="box-area">
                            @include('admin.assistant.queues.queue-box')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('scripts')
    <script>
        /*
        *  start the queue if it is not started yet
        * */
        $(document).on('click', '.start-queue', function (event) {
            event.preventDefault();
            $.ajax({
                type: 'GET',
                url: URL + '/queue/start',
                data: {_token: token},
            }).done(function (data) {
                $('#box-area').html(data);
            });
        });

        /*
        *  get the next patient in the queue
        *
        * */
        $(document).on('click', '.next-queue', function (e) {
            e.preventDefault();

            // get the queue status
            let queueStaus = $('#queue_status').data('status');
            if (queueStaus == 1) {
                // get reservation status
                var status = $(this).parent().children().val();
                // if status miss go to next
                if (status == 4) {
                    nextQueue(status)
                }
                // if status attended check if doctor add visit or not
                else if (status == 3) {
                    $.ajax({
                        type: 'POST',
                        url: URL + '/queue/check-visit',
                        data: {_token: token, status: status},
                    }).done(function (data) {
                        if (data === 'false') {
                            swal({
                                title: "Warning",
                                text: "You can't go to next reservation because doctor doesn't create visit",
                                type: "warning",
                            })
                        } else if (data === 'true') {
                            nextQueue(status)
                        }
                    });
                }
            } else {
                swal("Queue Paused!", "Please Resume The Queue First");
            }
        });

        function nextQueue(status) {
            $.ajax({
                type: 'POST',
                url: URL + '/queue/next',
                data: {_token: token, status: status},
            }).done(function (data) {
                console.log(data);
                $('#box-area').html(data);
            });
        }
    </script>
@stop
