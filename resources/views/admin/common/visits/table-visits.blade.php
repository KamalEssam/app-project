@if(count($visits) > 0)
    <div class="table-responsive">
        <table id="dynamic-table" class="table table-striped table-bordered">
            <thead>
            <tr>
                <th class="center">{{trans('lang.name')}}</th>
                <th class="center">{{trans('lang.type')}}</th>
                <th class="center">{{trans('lang.reservation_date')}}</th>
                <th class="center">{{trans('lang.clinic_name')}}</th>
                <th class="center">{{trans('lang.next_visit')}}</th>
                @if(auth()->user()->role_id == $role_doctor)
                    <th class="center">{{trans('lang.created_by')}}</th>
                    <th class="center">{{trans('lang.updated_by')}}</th>
                @endif
                <th class="center">{{trans('lang.controls')}}</th>

            </tr>
            </thead>

            <tbody id="table" class="t-content">
            @foreach($visits as $visit)
                <?php
                $reservation = \App\Models\Reservation::where('id', $visit->reservation_id)->first();
                $clinic = \App\Models\Clinic::where('id', $reservation->clinic_id)->first();
                $user = \App\Models\User::where('id',$reservation->user_id)->first();
                $created_by = \App\Models\User::where('id',$visit->created_by)->first();
                $updated_by = \App\Models\User::where('id',$visit->updated_by)->first();
                ?>
                <tr>
                    <td class="center">
                        <a href="{{route('visits.show' , [$visit->id])}}">{{ ($user->name)}}</a>
                    </td>
                    <td class="center">
                        @if($visit->type == 0)
                            <p>{{trans('lang.check_up')}}</p>
                        @elseif($visit->type == 1)
                            <p>{{trans('lang.follow_up')}}</p>
                        @endif
                    </td>
                    <td class="center">{{ Super::getProperty( $reservation->day) }}</td>

                    <td class="center">{{ Super::getProperty( $clinic[App::getLocale() . '_address']) }}</td>

                    <td class="center">{{ Super::getProperty( $visit->next_visit) }}</td>


                    @if(auth()->user()->role_id == 1)
                        <td class="center">{{ Request::is( isset($created_by) ) ? $created_by->name : trans('lang.n/a')  }}</td>

                        <td class="center">{{ Request::is( isset($updated_by) ) ? $updated_by->name : trans('lang.n/a') }}</td>
                    @endif
                    <td class="center">

                        <div class="btn-group control-icon">
                            <a href="{{route('visits.show' , [$visit->user_id])}}">
                                <i class="add fa fa-eye"></i>
                            </a>
                            @if(auth()->user()->role_id == 1)

                                <a href="{{ route('visits.edit', $visit->id)  }}"><i
                                            class="ace-icon fa fa-edit bigger-120  edit"
                                            data-id=""></i></a>
                            @endif
                        </div>
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
                <th class="center">{{trans('lang.name')}}</th>
                <th class="center">{{trans('lang.type')}}</th>
                <th class="center">{{trans('lang.reservation_date')}}</th>
                <th class="center">{{trans('lang.clinic_name')}}</th>
                <th class="center">{{trans('lang.next_visit')}}</th>
                @if(auth()->user()->role_id == $role_doctor)
                    <th class="center">{{trans('lang.created_by')}}</th>
                    <th class="center">{{trans('lang.updated_by')}}</th>
                @endif
            </tr>
            </thead>

            <tbody id="table" class="t-content">
            <tr>
                @if(auth()->user()->role_id == $role_doctor)
                    <td colspan="7" class="text-center">There is no data found.</td>
                @elseif(auth()->user()->role_id == $role_assistant)
                    <td colspan="5" class="text-center">There is no data found.</td>
                @endif
            </tr>
            </tbody>
        </table>
    </div>
@endif
