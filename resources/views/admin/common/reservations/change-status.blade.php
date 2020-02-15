@if($reservation->status == \App\Http\Controllers\WebController::R_STATUS_APPROVED)
    <select data-id="{{ $reservation->id }}" class="select-save" id="select-save">
        <option value="1"
                data-status="1" {{ Request::is($reservation->status == 1) ? 'selected' : '' }} >
            {{ trans('lang.approved') }}
        </option>
        <option value="2"
                data-status="2" {{ Request::is($reservation->status == 2) ? 'selected' : '' }}>
            {{ trans('lang.canceled') }}
        </option>
        @if($auth->account->type == \App\Http\Controllers\WebController::ACCOUNT_TYPE_POLY)
            <option value="3"
                    data-status="2" {{ Request::is($reservation->status == 2) ? 'selected' : '' }}>
                {{ trans('lang.attended') }}
            </option>
            <option value="4"
                    data-status="2" {{ Request::is($reservation->status == 2) ? 'selected' : '' }}>
                {{ trans('lang.missed') }}
            </option>
        @endif
    </select>
@endif

{{-- Add Stand By Icon --}}
{{-- when the reservation day is today and the reservation is missed --}}
@if($reservation->day ==  now("Africa/Cairo")->format("Y-m-d") && $reservation->status == 4)
    <a href="{{ route('reservations.standBy' , $reservation->id) }}"
       title={{trans('lang.standBy')}}><i class="ml-10 ace-icon fas fa-male bigger-120 loon"></i></a>
@endif

@if($reservation->day >=  now("Africa/Cairo")->format("Y-m-d") && $reservation->status != 3)
    <a href="{{ route('reservations.edit' , $reservation->id) }}"
       title={{trans('lang.reschedule')}}><i
                class="ml-10 ace-icon far fa-clock bigger-120 loon"></i></a>
@endif

@if($auth->role_id == $role_doctor && $reservation->status == 3)
    @if($visit)
        <a title={{trans('lang.edit_visit')}} href="{{route('visits.edit' , [$visit->id])}}"><i
                    class="ml-10 ace-icon fa fa-edit bigger-120  edit">
            </i></a>
    @else
        <a title={{trans('lang.add_visit')}} href="{{route('visits.create', [$reservation->id])}}"><i
                    class="ml-10 ace-icon fa fa-plus bigger-120 add "></i></a>
    @endif
@endif
