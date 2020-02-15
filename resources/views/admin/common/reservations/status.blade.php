@switch ($reservation->status)
    @case (1)
    {{ trans('lang.approved') }}
    @break
    @case (2)
    {{ trans('lang.canceled') }}
    @break
    @case (3)
    {{ trans('lang.attended') }}
    @break
    @case (4)
    {{ trans('lang.missed') }}
    @break
    @default
    {{ trans('lang.pending') }}
@endswitch