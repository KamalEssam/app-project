<div id="queue-data">
    @if(isset($reservation))
        @if($queue)
            <div class="row mt-70" id="queue-box">
                <div class="col-md-6 col-md-offset-3">
                    <div class="dash-box-queue dash-box-color-queue" style="height: 190px;">
                        <div class="dash-box-icon-queue">
                            <i class="icon fa fa-user"></i>
                        </div>
                        <a href="#" data-status="{{ $queue['queue_status'] }}" data-queue="{{$queue['id']}}"
                           id="queue_status"
                           style="position: absolute;right: 33px;font-size: 32px; @if($queue['queue_status']  == 1) color:#009cbb @else color:#ff4747 @endif; "><i
                                class="fa {{ $queue['queue_status']  == 1 ? 'fa-pause-circle' : 'fa-play-circle' }}"></i></a>
                        <div class="dash-box-body-queue center">
                            <span class="dash-box-count-queue">{{ trans('lang.patient_no')  }}</span>
                            <span class="dash-box-title-queue font-30"
                                  id="queue_no">{{ $reservation->queue }}</span>
                        </div>
                        <div class="dash-box-action-queue">
                            <select class="select-next">
                                <option value="3" data-status="3" selected>
                                    {{ trans('lang.attended') }}
                                </option>
                                <option value="4" data-status="4">
                                    {{ trans('lang.missed') }}
                                </option>
                            </select>
                            <a href="#" class="next-queue ">{{ trans('lang.next') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            @if(isset($patient))
                <div class="row mt-70" id="patient-info">
                    <div class="col-md-8 col-md-offset-2">
                        <div class="panel panel-default panel-no-border">
                            <div class="panel-heading bolder">{{ trans('lang.patient_info') }}</div>
                            <div class="panel-body">
                                <div class="mb-10" id="name">
                                    <span class="font-18 bolder"> {{ trans('lang.name') . " " .":". " " }}</span>
                                    <span class="grey"
                                          id="name-data">{{ Super::getProperty( $patient->name ) }}</span>
                                </div>
                                <div class="mb-10" id="email">
                                    <span class="font-18 bolder"> {{ trans('lang.email') . " " .":". " " }}</span>
                                    <span class="grey"
                                          id="email-data">{{ Super::getProperty( $patient->email ) }}</span>
                                </div>
                                <div class="mb-10" id="address">
                                    <span class="font-18 bolder"> {{ trans('lang.address') . " " .":". " " }}</span>
                                    <span class="grey"
                                          id="address-data">{{ Super::getProperty( $patient->address ) }}</span>
                                </div>
                                <div class="mb-10" id="mobile">
                                    <span class="font-18 bolder"> {{ trans('lang.mobile') . " " .":". " " }}</span>
                                    <span class="grey"
                                          id="mobile-data">{{ Super::getProperty( $patient->mobile ) }}</span>
                                </div>
                                <div class="mb-10" id="birthday">
                                    <span class="font-18 bolder"> {{ trans('lang.birthday') . " " .":". " " }}</span>
                                    <span class="grey"
                                          id="birthday-data">{{ Super::getProperty( $patient->birthday ) }}</span>
                                </div>
                                <div class="mb-10" id="gender">
                                    <span class="font-18 bolder"> {{ trans('lang.gender') . " " .":". " " }}</span>
                                    <span class="grey"
                                          id="gender-data">{{  Request::is( $patient->gender == 0) ? trans('lang.male') : trans('lang.female') }}</span>
                                </div>
                                <div class="mb-10" id="id">
                                    <span class="font-18 bolder"> {{ trans('lang.id') . " " .":". " " }}</span>
                                    <span class="grey"
                                          id="id-data">{{ Super::getProperty( $patient->unique_id ) }}</span>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                @if($patient->visits)
                    <div class="row">
                        <div class="col-md-4 col-md-offset-4">
                            <a href="{{route('visits.show' , [$patient->id])}}"
                               class="btn-loon btn-xs  ml-60">{{ trans('lang.view_history') }}</a>
                        </div>
                    </div>
                @endif

            @endif
        @else
            <div class="empty" id="queue_not_started">
                <p> {{ trans('lang.queue_not_started') .',' }} <a href="{{ route('queue.start') }}" class="start-queue"
                                                                  id="start-queue">
                        {{ trans('lang.lets_start') }}
                    </a>
                </p>
            </div>
        @endif
    @else
        <div class="row">
            <div class="col-xs-12 text-center"><img class="no_data_image"
                                                    src="{{ asset('assets/images/no_data/no_queue.png') }}"></div>
        </div>
        <div class="row">
            <div class="col-xs-12 text-center"><p
                    class="loon no_data">{{trans('lang.no_queue')}}</p></div>
        </div>

    @endif
</div>
@push('more-scripts')
    <script>
        $(document).ready(function () {
            var URL = "{{ url('/') }}";
            // for tour
            $.ajax({
                url: URL + '/check-first-time',
                type: 'POST',
                data: {_token: token, column_name: 'queue_tour'}
            }).done(function (data) {
                if (data == 'true') {
                    var tour = new Tour({
                        debug: true,
                        steps: [
                            {
                                element: "#start-queue",
                                title: "Start queue",
                                content: "Start Your queue to get your appointments and track them here.",
                                placement: "bottom",
                                backdrop: true,
                                template: "<div class='popover tour'>" +
                                    "<div class='arrow'></div>" +
                                    "<h3 class='popover-title'></h3>" +
                                    "<div class='popover-content'></div>" +
                                    "<div class='popover-navigation'>" +
                                    "<button class='btn btn-default' data-role='end'>Got it!</button>" +
                                    "</div>" +
                                    "</div>",
                            }
                        ]
                    });
                    tour.init();
                    tour.restart();
                }
            });

            // stop and resume the Queue
            $(document).on('click', '#queue_status', function (event) {
                event.preventDefault();
                let queue = $(this).data('queue');
                let status = $(this).data('status');

                let the_title = status == 1 ? "You want to pause the queue" : "You want to resume the queue";

                if (status === 1)
                    status = -1;
                else
                    status = 1;

                swal({
                        title: "Are you sure?",
                        text: the_title,
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-danger",
                        showLoaderOnConfirm: true,
                        confirmButtonText: "Yes",
                        closeOnConfirm: false
                    },
                    function () {
                        $.ajax({
                            url: "{{ route('queue.changeStatus') }}",
                            type: 'POST',
                            data: {
                                _token: token,
                                queue: queue,
                                status: status
                            }
                        }).done(function (data) {
                            if (data.status == true) {
                                swal({
                                        title: "Done",
                                        text: "",
                                        type: "success",
                                    },
                                    function () {
                                        window.location.reload();
                                    });
                            } else {
                                swal({
                                        title: "Error",
                                        text: "Whoops something went wrong",
                                        type: "error",
                                    },
                                    function () {
                                        window.location.reload();
                                    });
                            }
                        });
                    });
            });
        });
    </script>
@endpush
