@extends('layouts.admin.admin-master')

@section('title',  trans('lang.profile') )

@section('content')
    <div class="row no-margin">
        <div class="twPc-div">
            <a class="twPc-bg twPc-block"></a>
            <div class="profile-data">
                @if($auth->role_id != $role_rk_admin && $auth->role_id != $role_rk_super_admin && $auth->role_id != $role_rk_sales)
                    <div class="twPc-button">
                        <a href="{{ route('profile.edit' , [$profile->id]) }}"
                           class="btn-circle-loon"><i
                                class="fa fa-edit bigger-120 center"></i></a>
                    </div>
                @endif
                <div class="row full-width">
                    <br>
                    <div class="col-md-3">
                        <a title="{{ \App\Http\Controllers\WebController::getProperty( $profile->name ) }}"
                           class="twPc-avatarLink">
                            <img
                                src="{{ asset($profile->image) }}"
                                class="twPc-avatarImg">
                        </a>
                    </div>
                    <div class="twPc-divUser col-md-8">
                        <div class="twPc-divName loon mb-5 mt-25">
                            <a>{{ \App\Http\Controllers\WebController::getProperty( $profile->name ) }}</a>
                        </div>
                        @if($auth->role_id != $role_rk_admin && $auth->role_id != $role_rk_super_admin)
                            <a class="font-12 grey">@
                                <span>{{ \App\Http\Controllers\WebController::getProperty( $profile->unique_id ) }}</span>
                            </a>
                        @else
                            <a class="font-12 grey">@
                                <span>{{ trans('lang.rk-admin')}}</span>
                            </a>
                        @endif
                    </div>
                </div>
                <div class="row full-width">
                    <div class="col-md-8 col-md-offset-3">
                        <div class="panel panel-default panel-no-border">
                            <div class="panel-heading bolder">{{ trans('lang.personal_information') }}</div>
                            <div class="panel-body">
                                <div class="mb-10">
                                    <span class="font-18 bolder"> {{ trans('lang.email') . " " .":". " " }}</span>
                                    <span
                                        class="grey">{{  \App\Http\Controllers\WebController::getProperty( $profile->email)  }}</span>
                                </div>
                                <div class="mb-10">
                                    <span class="font-18 bolder"> {{ trans('lang.mobile') . " " .":". " " }}</span>
                                    <span
                                        class="grey">{{  \App\Http\Controllers\WebController::getProperty( $profile->mobile)  }}</span>
                                </div>
                                <div class="mb-10">
                                    <span class="font-18 bolder"> {{ trans('lang.birthday') . " " .":". " " }}</span>
                                    <span
                                        class="grey">{{  \App\Http\Controllers\WebController::getProperty( $profile->birthday)  }}</span>
                                </div>
                                <div class="mb-10">
                                    <span class="font-18 bolder"> {{ trans('lang.gender') . " " .":". " " }}</span>
                                    <span
                                        class="grey">{{  Request::is( $profile->gender == 0) ? trans('lang.male') : trans('lang.female') }}</span>
                                </div>
                                @if($auth->role_id == $role_assistant)
                                    <div class="mb-10">
                                        <span class="font-18 bolder"> {{ trans('lang.address') . " " .":". " " }}</span>
                                        <span
                                            class="grey">{{  \App\Http\Controllers\WebController::getProperty( $profile->address)  }}</span>
                                    </div>
                                @endif
                                @if($auth->role_id == $role_doctor)
                                    @if($auth->account->type == 0)
                                        <div class="mb-10">
                                            <span
                                                class="font-18 bolder"> {{ trans('lang.speciality') . " " .":". " " }}</span>
                                            <span
                                                class="grey">{{  Request::is( $profile->en_speciality || $profile->ar_speciality  ) ? $profile[app()->getLocale() . '_speciality']  : 'n/a' }}</span>
                                        </div>
                                        <div class="mb-10">
                                            <span
                                                class="font-18 bolder"> {{ trans('lang.title') . " " .":". " " }}</span>
                                            <span
                                                class="grey">{{  Request::is( $profile->en_title || $profile->ar_title  ) ? $profile[app()->getLocale() . '_title']  : 'n/a' }}</span>
                                        </div>
                                    @endif
                                    <div class="mb-10">
                                        <span class="font-18 bolder"> {{ trans('lang.bio') . " " .":". " " }}</span>
                                        <span
                                            class="grey">{{  \App\Http\Controllers\WebController::getProperty( $profile[app()->getLocale() . '_bio'] )  }}</span>
                                    </div>
                                    <div class="mb-10">
                                        <span
                                            class="font-18 bolder"> {{ trans('lang.sub_specialities') . " " .":". " " }}</span>
                                        <span>
                                              @php
                                                  if(count($profile->sub_specialities) > 0) {
                                                    foreach ($profile->sub_specialities as $speciality) {
                                                         echo '<span class="tag">' . $speciality[app()->getLocale() . '_name'] . '</span>';
                                                       }
                                                  } else {
                                                        echo trans('lang.not_set');
                                                  }
                                              @endphp
                                        </span>
                                    </div>
                                    <div class="mb-10">
                                        <span
                                            class="font-18 bolder"> {{ trans('lang.insurance_companies') . " " .":". " " }}</span>
                                        <span>
                                            @php
                                                if(count($profile->insurance_companies) > 0) {
                                                  foreach ($profile->insurance_companies as $company) {
                                                       echo '<span class="tag">' . $company[app()->getLocale() . '_name'] . '</span>';
                                                     }
                                                } else {
                                                      echo trans('lang.not_set');
                                                }
                                            @endphp
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop




