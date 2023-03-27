@extends('layouts.master')

@can('isAdmin')
@section('content')
@include('pages.dashboard.admin.index')
@endsection
@endcan

@can('isSuperAdmin')
@section('content')
@include('pages.dashboard.super-admin.index')
@endsection
@endcan


@can('isCashier')
@section('content')
@include('pages.dashboard.cashier.index')
@endsection
@endcan
