<div class="col-md-{{ $width }}">
    <div class="dash-box dash-box-color">
        <div class="dash-box-icon">
            <i class="icon fa fa-users"></i>
        </div>
        <div class="dash-box-body">
            {{-- total number of clinic --}}
            <span
                class="dash-box-count">{{\Illuminate\Support\Facades\DB::table('clinics')->where('account_id', $auth->account_id)->count() }}</span>
            <span
                class="dash-box-title">{{ ($auth->account->type == 0) ? trans('lang.total_clinics') : trans('lang.total_clinics_poly') }}</span>
        </div>

        <div class="dash-box-action">
            <button><a href="{{ url('clinics') }}">{{ trans('lang.more_info') }}</a></button>
        </div>
    </div>
</div>

@if($auth->account->type == 0)
    <div class="col-md-{{ $width }}">
        <div class="dash-box dash-box-color">
            <div class="dash-box-icon">
                <i class="icon fa fa-user-md"></i>
            </div>
            <div class="dash-box-body">
                {{-- total number of assistant--}}
                <span
                    class="dash-box-count">{{App\Models\User::where('account_id',$auth->account_id)->where('role_id',$role_assistant)->count()}}</span>
                <span class="dash-box-title">{{ trans('lang.total_assistant') }}</span>
            </div>

            <div class="dash-box-action">
                <button><a href="{{ url('assistants') }}">{{ trans('lang.more_info') }}</a></button>
            </div>
        </div>
    </div>
@endif

<div class="col-md-{{ $width }}">
    <div class="dash-box dash-box-color">
        <div class="dash-box-icon">
            <i class="icon fa fa-users"></i>
        </div>
        <div class="dash-box-body">
                        <span class="dash-box-count">
                        {{-- get the number of user who subscribed this doctor --}}
                            {{
                            DB::table('account_user')
                            ->where('account_user.account_id',$auth->account_id)
                            ->count()
                            }}
                        </span>
            <span class="dash-box-title">{{ trans('lang.total_patients') }}</span>
        </div>

        <div class="dash-box-action">
            <button><a href="{{ url('patients') }}">{{ trans('lang.more_info') }}</a></button>
        </div>
    </div>
</div>

<div class="col-md-{{ $width }}">
    <div class="dash-box dash-box-color">
        <div class="dash-box-icon">
            <i class="icon fa fa-history"></i>
        </div>
        <div class="dash-box-body">
                                <span class="dash-box-count">
                                    {{-- get doctor total visits--}}
                                    {{
                                           DB::table('visits')
                                           ->where('visits.created_by', $auth->id)
                                           ->count()
                                           }}
                                </span>
            <span class="dash-box-title">{{ trans('lang.total_visits') }}</span>
        </div>

        <div class="dash-box-action">
            <button><a href="#">{{ trans('lang.more_info') }}</a></button>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-md-12">
        <div class="col-md-3">
            <select name="months" id="selectYear" class="form-control">
                @php $currentYear = date('Y'); $currentMonth = date('m'); @endphp
                @for ($i = 2018; $i <= $currentYear; $i++)
                    <option value="{{$i}}" {{$currentYear == $i ? 'selected' : ''}}>{{$i}}</option>
                @endfor
            </select>
        </div>
        <div class="col-md-3">
            <select name="months" id="selectMonth" class="form-control">
                @for ($i = 1; $i <= 12; $i++)
                    @php $timestamp = mktime(0, 0, 0, $i);
                                    $label = date('F', $timestamp);
                    @endphp
                    <option
                        value="{{ sprintf('%02d', $i)  }}" {{$currentMonth == $i ? 'selected' : ''}}>{{ $label }}</option>
                @endfor
            </select>
        </div>


        <div class="col-md-3">
            <select name="clinic" id="selectClinic" class="form-control">
                @php
                    // single
                    if ($auth->account->type == 0) {
                            $clinics = DB::table('clinics')->where('account_id',$auth->account_id)->select('id',app()->getLocale() . '_address as name')->get();
                    } else {
                    // poly
                            $clinics = DB::table('clinics')->where('account_id',$auth->account_id)->select('id',app()->getLocale() . '_name as name')->get();
                    }
                @endphp
                <option value="-1">{{ trans('lang.all-clinics') }}</option>
                @foreach($clinics as $clinic)
                    <option value="{{ $clinic->id }}">{{ $clinic->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <input type="button" id="DoctorReservationsChartLines" class="btn btn-primary"
                   value="{{ trans('lang.show-results') }}">
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <canvas id="reservations_statistics"></canvas>
    </div>
</div>

@push('more-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.js"></script>
    <script>
        $(document).ready(function () {
            const URL = "{{ route('admin') }}";

            // registered doctors
            $.ajax({
                url: URL + '/statistics/account/doctor-reservations',
                type: 'GET',
            }).done(function (doctors) {
                if (doctors.status == true) {
                    ReservationsLinesChart(doctors);
                }
            });

            function ReservationsLinesChart(data) {
                var ctx3 = document.getElementById("reservations_statistics").getContext('2d');
                new Chart(ctx3, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: 'reservations number ',
                                fillColor: "rgba(78,164,219,1)",
                                strokeColor: "rgba(78,164,219,1)",
                                pointColor: "rgba(78,164,219,1)",
                                pointStrokeColor: "#4EA4DB",
                                pointHighlightFill: "#4EA4DB",
                                pointHighlightStroke: "rgba(78,164,219,1)",
                                backgroundColor: "rgba(78,164,219,0.5)",
                                data: data.data
                            }
                        ]
                    },
                });
            }

            $('#DoctorReservationsChartLines').on('click', function () {
                updateLineChart();
            });

            function updateLineChart() {
                let year = $('#selectYear').val();
                let month = $('#selectMonth').val();
                let clinic = $('#selectClinic').val();

                $.ajax({
                    url: URL + '/statistics/account/doctor-reservations',
                    type: 'GET',
                    data: {year: year, month: month, clinic: clinic}
                }).done(function (doctors) {
                    if (doctors.status == true) {
                        ReservationsLinesChart(doctors);
                    }
                });
            }
        });
    </script>
@endpush
