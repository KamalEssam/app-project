@extends('layouts.admin.admin-master')

<!-- comment -->
@section('title', 'Dashboard')

@section('content')
    @if($auth->role_id == $role_assistant)
        @include('admin.dashboard.assistant-dashboard')
    @elseif($auth->role_id == $role_rk_sales)
        @include('admin.dashboard.sales-dashboard')
    @else
        @include('admin.dashboard.admin-dashboard')
    @endif
@stop
