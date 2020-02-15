@if(count($reservations) > 0)
<div class="table-responsive">
    <table id="dynamic-table" class="table table-striped table-bordered {{--table-hover--}}">
        <thead>
        <tr>
            <th class="center" style="width: 50px !important;">image</th>
            <th class="center">Name</th>
            <th class="center">Time</th>
            <th class="center">Day</th>
            <th class="center">Status</th>
            <th class="center">Type</th>
        </tr>
        </thead>

        <tbody id="table" class="t-content">
        @foreach($reservations as $reservation)
            <tr>
                <td class="center">
                    <div class="premium-container">
                        @if($reservation->user->image)
                            <img src="{{ $reservation->user->image }}" class="premium-image"
                                 style="">
                            @if ($reservation->user->is_premium == 1)
                                <img src="{{ asset('assets/images/premium.png') }}" alt=""
                                     class="premium-icon">
                            @endif
                        @else
                            {{ trans('lang.n/a') }}
                        @endif
                    </div>
                </td>
                <td class="center">{{ ($reservation->user->name)}}</td>
                <td class="center">{{($reservation->dictionary->time)}}</td>
                <td class="center">{{($reservation->day) }}</td>
                <td class="center status-container">
                    @if($reservation->is_approved == 0)
                        <a class="change-status check-true control-icon"><i
                                    class="ace-icon bigger-120 fa fa-check edit"
                                    data-id="{{ $reservation->id }}"
                                    data-status="1"></i></a>
                        <a class="change-status check-false control-icon"><i
                                    class="ace-icon bigger-120 fa fa-times reject"
                                    data-id="{{ $reservation->id }}"
                                    data-status="2"></i></a>
                    @endif
                </td>
                <td class="center">
                    @if($reservation->status == 0)
                        <p>Check up</p>
                    @elseif($reservation->status == 1)
                        <p>Follow up</p>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

@else
<div class="table-responsive">
    <table id="dynamic-table" class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th class="center">Name</th>
            <th class="center">Time</th>
            <th class="center">Day</th>
            <th class="center">Status</th>
            <th class="center">Type</th>
        </tr>
        </thead>

        <tbody id="table" class="t-content">
        <tr>
            <td colspan="5" class="text-center">There is no data found.</td>
        </tr>
        </tbody>
    </table>
</div>

@endif
