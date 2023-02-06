@extends('layouts.master')

@can('isAdmin')
@section('content')
@include('pages.dashboard.main_admin')
@endsection
@endcan

@can('isSuperAdmin')
@section('content')
@include('pages.dashboard.main_superAdmin')
@endsection
@endcan


@can('isCashier')
@section('content')
@include('pages.dashboard.main_cashier')
@endsection
@endcan
