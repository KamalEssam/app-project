@extends('layouts.admin.admin-master')

@section('title',  trans('lang.account') )


@section('content')
    <div class="page-content">
        <div class="page-header">
            <div class="row">
                <div class="col-md-11">
                    <h1>{{ trans('lang.account') }}</h1>
                </div>

            </div>
        </div>

        <div class="row">

            <div id="user-profile-1" class="col-xs-3 user-profile row">
                <div class="col-xs-12 center">
                    <span>
                        <img id="avatar" class="editable img-responsive profile-img"
                             alt="Alex's Avatar"
                             src="{{ $user->image }}"/>
                    </span>

                    <div class="space-4"></div>

                    <div class="width-60 label label-info label-xlg arrowed-in arrowed-in-right">
                        <div class="inline position-relative">
                            &nbsp;
                            <span class="white">{{ $user->name }}</span>

                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xs-6">

                <div class="profile-user-info profile-user-info-striped" style="margin-top: 80px !important;">
                    <div class="profile-info-row">
                        <div class="profile-info-name">{{ trans('lang.email') }}</div>

                        <div class="profile-info-value">
                            <span class="editable" id="email">{{ Super::getProperty( $user->email) }}</span>
                        </div>
                    </div>
                    <div class="profile-info-row">
                        <div class="profile-info-name">{{ trans('lang.name') }}</div>

                        <div class="profile-info-value">
                            <span class="editable" id="username">{{ Super::getProperty( $user->name ) }}</span>
                        </div>
                    </div>

                    <div class="profile-info-row">
                        <div class="profile-info-name">{{ trans('lang.unique_id') }}</div>

                        <div class="profile-info-value">
                            <span class="editable" id="email">{{ Super::getProperty( $account->unique_id ) }}</span>
                        </div>
                    </div>

                    <div class="profile-info-row">
                        <div class="profile-info-name"> {{ trans('lang.due_amount') }} </div>

                        <div class="profile-info-value">
                            <span class="editable" id="birthday">{{ Super::getProperty( $account->due_amount ) }}</span>
                        </div>
                    </div>
                    <div class="profile-info-row">
                        <div class="profile-info-name"> {{ trans('lang.due_date') }} </div>

                        <div class="profile-info-value">
                            <span class="editable" id="birthday">{{ Super::getProperty( $account->due_date ) }}</span>
                        </div>
                    </div>

                    <div class="profile-info-row">
                        <div class="profile-info-name"> {{ trans('lang.mobile') }}</div>

                        <div class="profile-info-value">
                            <span class="editable" id="mobile">{{ Super::getProperty( $user->mobile ) }}</span>
                        </div>
                    </div>

                    <div class="profile-info-row">
                        <div class="profile-info-name"> {{ trans('lang.plan') }}</div>

                        <div class="profile-info-value">
                            <span class="editable"
                                  id="gender">{{  Super::getProperty( $plan[ App::getLocale() . '_name' ] ) }}</span>
                        </div>
                    </div>

                    <div class="profile-info-row">
                        <div class="profile-info-name"> {{ trans('lang.country') }}</div>

                        <div class="profile-info-value">
                                <span class="editable"
                                      id="speciality">{{ Super::getProperty( isset($country) ? $country[ App::getLocale() . '_name' ]  : null ) }}</span>
                        </div>
                    </div>

                    <div class="profile-info-row">
                        <div class="profile-info-name"> {{ trans('lang.city') }}</div>

                        <div class="profile-info-value">
                                <span class="editable"
                                      id="mobile">{{  Super::getProperty( isset($city) ? $city[ App::getLocale() . '_name' ]  : null  ) }}</span>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>


@stop




