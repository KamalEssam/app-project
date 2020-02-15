@extends('layouts.admin.admin-master')

@section('title', trans('lang.edit_reservation'))

@section('styles')
    {!! Html::style('assets/css/admin/form.css') !!}
    {!! Html::style('assets/css/admin/jquery-ui.min.css') !!}
    <link href="http://code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css" rel="Stylesheet">

@stop


@section('content')
    <div class="page-content mt-70">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <h1 class="font-18 loon">{{ trans('lang.edit_reservation') }}</h1>
                        <hr>
                        {!! Form::model($reservation, ['route' => ['reservations.update', $reservation->id], 'method' => 'PATCH', 'files' => true])!!}
                        @include('admin.common.reservations.form', ['btn' => 'Add', 'classes' => 'btn-xs pull-right'])
                        {!! Form::close() !!}
                        @include('admin.common.reservations.modal-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@push('more-scripts')

    <script type="text/javascript">

        //filter day times
        $('#day').on('change', function () {
            var day = $('#day').val();
            var clinic_id = $('#edit_clinic_id').val();

            $.ajax({
                url: URL + '/reservation/time_reserved',
                type: 'POST',
                data: {_token: token, day: day, clinic_id: clinic_id}
            }).done(function (data) {
                var i = 0;
                var string;
                console.log(data);
                // in case of Queue Clinic Reschedule
                if (data == 'true') {
                    // means that the clinic is queue and is you can reschedule
                } else if (data == 'false') {
                    swal("Not Available!", "No Times available on this day!");
                } else if (data.length == 0) {
                    swal("Not Available!", "No Times available on this day!");

                    string = '<option value="">' + 'No times available on this day' + '</option>';
                } else {
                    for (i = 0; i < data.length; i++) {
                        // $('#time').val(data);
                        if (data[i]) {
                            string += '<option value="' + data[i].id + '">' + data[i].time + '</option>';
                        }
                    }

                }
                $('#time').html(string);

            });
        });


    </script>

@endpush
