<div class="page-content">
    <div class="card" style="border: 1px solid #eee; border-radius: 15px;">
        <h5 class="card-header"
            style=" background-color: #eee;  margin: 0px !important; height: 40px; border-radius: 10px 10px 0px 0px; padding-top: 9px; padding-left: 2%;font-weight: bold; font-size: 20px;">
            <a href="{{ route('accounts.index') }}">
                {{ trans('lang.accounts') }}
                ({{ \App\Http\Repositories\Web\AccountRepository::getAccountsCount() }})
            </a></h5>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <canvas id="published_accounts" width="80" height="80">
                    </canvas>
                </div>

                <div class="col-md-3">
                    <canvas id="activated_accounts" width="80" height="80">
                    </canvas>
                </div>

                <div class="col-md-3">
                    <canvas id="premium_accounts" width="80" height="80">
                    </canvas>
                </div>


                <div class="col-md-3">
                    <canvas id="single_accounts" width="80" height="80">
                    </canvas>
                </div>

            </div>
        </div>
    </div>
    <br><br>
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-3">
                <select name="years" id="selectYear" class="form-control">
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
                <select name="types" id="selectType" class="form-control">
                    <option value="1">Doctors SignUp</option>
                    <option value="2">Patients SignUp</option>
                    <option value="3">Reservations</option>
                </select>
            </div>

            <div class="col-md-3">
                <input type="button" id="AdminChartLines" class="btn btn-primary"
                       value="{{ trans('lang.show-results') }}">

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <canvas id="registered_doctors"></canvas>
        </div>
    </div>

    <br><br>
    {{--  Accounts and Reservations Count  --}}
    @php
        $reservations_statistics = (new \App\Http\Repositories\Web\ReservationRepository())->getLastReservationsCountWithDoctors(5);
    @endphp
    <div class="row">
        <div class="col-md-12">
            <div class="widget-box transparent">
                <div class="widget-header widget-header-flat">
                    <h4 class="widget-title lighter">
                        <i class="ace-icon fa fa-star orange"></i>
                        5 Most Popular Accounts
                    </h4>

                    <div class="widget-toolbar">
                        <a href="#" data-action="collapse">
                            <i class="ace-icon fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>

                <div class="widget-body">
                    <div class="widget-main no-padding">
                        <table class="table table-bordered table-striped">
                            <thead class="thin-border-bottom">
                            <tr>
                                <th>
                                    <i class="ace-icon fa fa-caret-right blue"></i>{{ trans('lang.name') }}
                                </th>

                                <th>
                                    <i class="ace-icon fa fa-caret-right blue"></i>{{ trans('lang.email') }}
                                </th>

                                <th>
                                    <i class="ace-icon fa fa-caret-right blue"></i>{{ trans('lang.mobile') }}
                                </th>

                                <th class="hidden-480">
                                    <i class="ace-icon fa fa-caret-right blue"></i>{{ trans('lang.reservations') }}
                                </th>

                                <th class="hidden-480">
                                    <i class="ace-icon fa fa-caret-right blue"></i>{{ trans('lang.controls') }}
                                </th>

                            </tr>
                            </thead>
                            <tbody>
                            @if (count($reservations_statistics) > 0)
                                @foreach($reservations_statistics as $reservation_statistic)
                                    <tr>
                                        <td>
                                            {{ $reservation_statistic->account_name }}
                                        </td>
                                        <td>
                                            {{ $reservation_statistic->email }}
                                        </td>
                                        <td>
                                            {{ $reservation_statistic->mobile }}
                                        </td>
                                        <td>
                                            {{ $reservation_statistic->count }}
                                        </td>
                                        <td>
                                            <div class="btn-group control-icon">
                                                <a href="{{ route('account.reservations',['id' => $reservation_statistic->id]) }}"><i
                                                        class="ace-icon fa fa-eye show"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div><!-- /.widget-main -->
                </div><!-- /.widget-body -->
            </div><!-- /.widget-box -->
        </div><!-- /.col -->
    </div><!-- /.row -->
</div>


@push('more-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.js"></script>
    <script>
        $(document).ready(function () {
            const URL = "{{ route('admin') }}";
            // publish
            $.ajax({
                url: URL + '/statistics/account/publish',
                type: 'GET',
            }).done(function (account) {
                if (account.status == true) {
                    var ctx = document.getElementById("published_accounts").getContext('2d');
                    var orange_gradient = ctx.createLinearGradient(0, 0, 0, 300);
                    orange_gradient.addColorStop(0, '#00ebff');
                    orange_gradient.addColorStop(1, '#003de6');
                    var purple_gradient = ctx.createLinearGradient(0, 0, 0, 300);
                    purple_gradient.addColorStop(0, '#d8d6ed');
                    purple_gradient.addColorStop(1, '#d8d6ed');

                    new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            datasets: [{
                                data: account.account_published,
                                backgroundColor: [orange_gradient, purple_gradient],
                                hoverBackgroundColor: [orange_gradient, purple_gradient],
                                hoverBorderColor: [orange_gradient, purple_gradient],
                                borderWidth: 2,
                            }],
                            // These labels appear in the legend and in the tooltips when hovering different arcs
                            labels: [
                                'published',
                                'unpublished'
                            ]
                        },
                        options: {
                            cutoutPercentage: 60,
                            tooltips: {
                                callbacks: {
                                    title: function (tooltipItem, data) {
                                        return data['labels'][tooltipItem[0]['index']];
                                    },
                                    label: function (tooltipItem, data) {
                                        return data['datasets'][0]['data'][tooltipItem['index']] + ' Account';
                                    },
                                    afterLabel: function (tooltipItem, data) {
                                        var dataset = data['datasets'][0];
                                        var percent = Math.round((dataset['data'][tooltipItem['index']] / dataset["_meta"][0]['total']) * 100);
                                        return '(' + percent + '%)';
                                    }
                                },
                                backgroundColor: '#FFF',
                                titleFontSize: 16,
                                titleFontColor: '#0066ff',
                                bodyFontColor: '#000',
                                bodyFontSize: 14,
                                displayColors: false
                            }
                        }
                    });
                }
            });

            // active
            $.ajax({
                url: URL + '/statistics/account/active',
                type: 'GET',
            }).done(function (account) {
                if (account.status == true) {
                    var ctx1 = document.getElementById("activated_accounts").getContext('2d');
                    var orange_gradient = ctx1.createLinearGradient(0, 0, 0, 180);
                    orange_gradient.addColorStop(0, '#009ee0');
                    orange_gradient.addColorStop(1, '#95f7a3');
                    var purple_gradient = ctx1.createLinearGradient(0, 0, 0, 300);
                    purple_gradient.addColorStop(0, '#d8d6ed');
                    purple_gradient.addColorStop(1, '#d8d6ed');

                    new Chart(ctx1, {
                        type: 'doughnut',
                        data: {
                            datasets: [{
                                data: account.account_active,
                                backgroundColor: [orange_gradient, purple_gradient],
                                hoverBackgroundColor: [orange_gradient, purple_gradient],
                                hoverBorderColor: [orange_gradient, purple_gradient],
                                borderWidth: 2
                            }],
                            // These labels appear in the legend and in the tooltips when hovering different arcs
                            labels: [
                                'activated',
                                'not activated'
                            ]
                        },
                        options: {
                            cutoutPercentage: 60,
                            tooltips: {
                                callbacks: {
                                    title: function (tooltipItem, data) {
                                        return data['labels'][tooltipItem[0]['index']];
                                    },
                                    label: function (tooltipItem, data) {
                                        return data['datasets'][0]['data'][tooltipItem['index']] + ' Account';
                                    },
                                    afterLabel: function (tooltipItem, data) {
                                        var dataset = data['datasets'][0];
                                        var percent = Math.round((dataset['data'][tooltipItem['index']] / dataset["_meta"][1]['total']) * 100);
                                        return '(' + percent + '%)';
                                    }
                                },
                                backgroundColor: '#FFF',
                                titleFontSize: 16,
                                titleFontColor: '#0066ff',
                                bodyFontColor: '#000',
                                bodyFontSize: 14,
                                displayColors: false
                            }
                        }
                    });
                }
            });

            // premium and in premiun
            $.ajax({
                url: URL + '/statistics/account/premium',
                type: 'GET',
            }).done(function (account) {
                if (account.status == true) {
                    var ctx2 = document.getElementById("premium_accounts").getContext('2d');
                    var orange_gradient = ctx2.createLinearGradient(0, 0, 0, 100);
                    orange_gradient.addColorStop(0, '#b28012');
                    orange_gradient.addColorStop(1, '#dbb653');
                    var purple_gradient = ctx2.createLinearGradient(0, 0, 0, 300);
                    purple_gradient.addColorStop(0, '#d8d6ed');
                    purple_gradient.addColorStop(1, '#d8d6ed');
                    new Chart(ctx2, {
                        type: 'doughnut',
                        data: {
                            datasets: [{
                                data: account.account_premium,
                                backgroundColor: [orange_gradient, purple_gradient],
                                hoverBackgroundColor: [orange_gradient, purple_gradient],
                                hoverBorderColor: [orange_gradient, purple_gradient],
                                borderWidth: 2
                            }],
                            // These labels appear in the legend and in the tooltips when hovering different arcs
                            labels: [
                                'premium',
                                'not premium'
                            ]
                        },
                        options: {
                            cutoutPercentage: 60,
                            tooltips: {
                                callbacks: {
                                    title: function (tooltipItem, data) {
                                        return data['labels'][tooltipItem[0]['index']];
                                    },
                                    label: function (tooltipItem, data) {
                                        return data['datasets'][0]['data'][tooltipItem['index']] + ' Account';
                                    },
                                    afterLabel: function (tooltipItem, data) {
                                        var dataset = data['datasets'][0];
                                        var percent = Math.round((dataset['data'][tooltipItem['index']] / dataset["_meta"][2]['total']) * 100);
                                        return '(' + percent + '%)';
                                    }
                                },
                                backgroundColor: '#FFF',
                                titleFontSize: 16,
                                titleFontColor: '#0066ff',
                                bodyFontColor: '#000',
                                bodyFontSize: 14,
                                displayColors: false
                            }
                        }
                    });
                }
            });

            // single and poly
            $.ajax({
                url: URL + '/statistics/account/single',
                type: 'GET',
            }).done(function (account) {
                if (account.status == true) {
                    var ctx3 = document.getElementById("single_accounts").getContext('2d');
                    var orange_gradient = ctx3.createLinearGradient(0, 0, 0, 200);
                    orange_gradient.addColorStop(0, '#fca6c5');
                    orange_gradient.addColorStop(1, '#fca6c5');
                    var purple_gradient = ctx3.createLinearGradient(0, 0, 0, 200);
                    purple_gradient.addColorStop(0, '#4b265b');
                    purple_gradient.addColorStop(1, '#9f6575');
                    new Chart(ctx3, {
                        type: 'doughnut',
                        data: {
                            datasets: [{
                                data: account.account_single,
                                backgroundColor: [orange_gradient, purple_gradient],
                                hoverBackgroundColor: [orange_gradient, purple_gradient],
                                hoverBorderColor: [orange_gradient, purple_gradient],
                                borderWidth: 2
                            }],
                            // These labels appear in the legend and in the tooltips when hovering different arcs
                            labels: [
                                'single',
                                'poly'
                            ]
                        },
                        options: {
                            cutoutPercentage: 60,
                            tooltips: {
                                callbacks: {
                                    title: function (tooltipItem, data) {
                                        return data['labels'][tooltipItem[0]['index']];
                                    },
                                    label: function (tooltipItem, data) {
                                        return data['datasets'][0]['data'][tooltipItem['index']] + ' Account';
                                    },
                                    afterLabel: function (tooltipItem, data) {
                                        var dataset = data['datasets'][0];
                                        var percent = Math.round((dataset['data'][tooltipItem['index']] / dataset["_meta"][3]['total']) * 100);
                                        return '(' + percent + '%)';
                                    }
                                },
                                backgroundColor: '#FFF',
                                titleFontSize: 16,
                                titleFontColor: '#0066ff',
                                bodyFontColor: '#000',
                                bodyFontSize: 14,
                                displayColors: false
                            }
                        }
                    });
                }
            });


            // registered doctors
            $.ajax({
                url: URL + '/statistics/account/registered',
                type: 'GET',
            }).done(function (doctors) {
                if (doctors.status == true) {
                    AdminLinesChart(doctors);
                }
            });


            function AdminLinesChart(data, msg = 'registered doctors') {
                var ctx3 = document.getElementById("registered_doctors").getContext('2d');
                new Chart(ctx3, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: msg,
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

            $('#AdminChartLines').on('click', function () {
                updateLineChart();
            });

            function updateLineChart() {
                let year = $('#selectYear').val();
                let month = $('#selectMonth').val();
                let type = $('#selectType').val();

                $.ajax({
                    url: URL + '/statistics/account/registered',
                    type: 'GET',
                    data: {year: year, month: month, type: type}
                }).done(function (doctors) {
                    if (doctors.status == true) {
                        let msg = '';
                        if (type == 1) {
                            msg = 'registered doctors';
                        } else if (type == 2) {
                            msg = 'registered patient';
                        } else {
                            msg = 'reservation number';
                        }
                        AdminLinesChart(doctors, msg);
                    }
                });
            }
        });
    </script>
@endpush
