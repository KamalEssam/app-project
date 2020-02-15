@extends('layouts.admin.admin-master')

@section('title',  trans('lang.show_visits') )

@section('content')
    <div class="page-content mt-70">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel">
                    @if(count($reservations) > 0)
                        @foreach($reservations as $reservation)
                            <?php
                            if ($reservation->visit) {
                                $comments = $reservation->visit->comments;
                            }
                            ?>
                            <div class="row">
                                <div class="col-md-10 col-md-offset-1">
                                    <div class="panel panel-default panel-no-border">
                                        <div class="panel-heading">
                                            <h3 class="panel-title bolder">{{$reservation->user->name .' '.trans('lang.history') .' '.trans('lang.in_day').' '. $reservation->day }}</h3>
                                        </div>

                                        <div class="panel-body">
                                            <ul class="list-unstyled col-md-12">
                                                <li>
                                                    <span class="bolder">{{ trans('lang.clinic_name') .' '.':'.' ' }}</span>
                                                    <span class="grey">{{$reservation->clinic[App::getLocale() . '_address'] }}</span>

                                                </li>
                                                <li>
                                                    <span class="bolder">{{ trans('lang.reservation') .' '.':'.' ' }}</span>
                                                    <span class="grey">{{ $reservation->day .' '.','.' '}}</span>
                                                    <span class="grey">{{ $reservation->queue != 0 ? " " : Super::getNiceTime($reservation->workingHour->time) }} </span>
                                                </li>
                                                @if( $reservation->complaint != Null)
                                                    <li>
                                                        <span class="bolder">{{ trans('lang.complaint') .' '.':'.' ' }}</span>
                                                        <span class="grey break-all">{{ Super::getProperty($reservation->complaint)	 }}</span>
                                                    </li>
                                                @endif
                                                @if($reservation->user->attachments->count() > 0)
                                                    <li>
                                                        <span class="bolder">{{ trans('lang.attachments') .' '.':'.' ' }}</span>
                                                        @foreach($reservation->user->attachments as $attachment)
                                                            @if($attachment->type == 1 || $attachment->type == 2)
                                                                <span id="attachment"><a
                                                                            href="{{ $attachment->attachment }}"
                                                                            download>{{ $attachment->attachment }}</a></span>
                                                            @elseif($attachment->type == 0)
                                                                <span id="attachment-img"><a
                                                                            href="{{ asset('assets/attachments/profiles/') }}{{ "/" .$reservation->user->unique_id . "/" }}{{ $attachment->attachment }}"
                                                                            target="_blank"><img
                                                                                src="{{ asset('assets/attachments/profiles/') }}{{ "/" .$reservation->user->unique_id . "/" }}{{ $attachment->attachment }}"
                                                                                style="width: 50px;height:50px;"></a></span>
                                                            @endif
                                                        @endforeach
                                                    </li>
                                                @endif

                                                @if($reservation->visit)
                                                    @if( $reservation->visit->type != Null)
                                                        <li>
                                                            <span class="bolder">{{ trans('lang.type') .' '.':'.' ' }}</span>
                                                            <span class="grey">{{ Request::is($reservation->visit->type == 0) ? trans('lang.check_up') : trans('lang.follow_up') }}</span>
                                                        </li>
                                                    @endif

                                                    @if($reservation->visit->medications()->count() > 0)
                                                        <li>
                                                            <span class="bolder">{{ trans('lang.medications') .' '.':'.' ' }}</span>
                                                            @foreach($reservation->visit->medications as $medication)
                                                                <span class="grey">{{ Super::getProperty($medication->name).' '.'.'.' ' }}</span>
                                                            @endforeach
                                                        </li>
                                                    @endif
                                                    @if( $reservation->visit->diagnosis != Null)
                                                        <li>
                                                            <span class="bolder">{{ trans('lang.diagnosis') .' '.':'.' ' }}</span>
                                                            <span class="grey break-all">{{ Super::getProperty($reservation->visit->diagnosis)	 }}</span>
                                                        </li>
                                                    @endif
                                                    @if( $reservation->visit->next_visit != Null)
                                                        <li>
                                                            <span class="bolder">{{ trans('lang.next_visit') .' '.':'.' ' }}</span>
                                                            <span class="grey">{{ Super::getProperty($reservation->visit->next_visit)	 }}</span>
                                                        </li>
                                                    @endif
                                                    @if($comments->count() > 0 )
                                                        <li>
                                                            <span class="bolder">{{ trans('lang.comments') .' '.':'.' ' }}</span>
                                                            <ul>
                                                                @foreach($comments as $comment)
                                                                    <li class="grey break-all">{{ Super::getProperty($comment->comment) }}</li>
                                                                @endforeach
                                                            </ul>
                                                        </li>
                                                    @endif
                                                @else
                                                    <li>
                                                        <span class="bolder">{{ trans('lang.visit') .' '.':'.' ' }}</span>
                                                        <span class="grey">{{trans('lang.no_visit_added')}}</span>

                                                    </li>
                                                @endif
                                                <li class="pull-right bolder">
                                                    <a href="javascript:history.back()" class="loon"><i
                                                                class="fas fa-arrow-left mr-10"></i>{{ trans('lang.back') }}
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty">{{trans('lang.no_visits')}}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop





