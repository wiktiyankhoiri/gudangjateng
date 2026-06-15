@extends('layouts.app')

@section('content')

@php $role = auth()->user()->role; @endphp

@switch($role)
    @case('admin')
        @include('dashboard.admin')
        @break
    @case('manager')
        @include('dashboard.manager')
        @break
    @case('sales')
        @include('dashboard.sales')
        @break
    @case('audit')
        @include('dashboard.audit')
        @break
    @case('super_admin')
        @include('dashboard.super-admin')
        @break
    @default
        <div class="rounded-xl border border-error-300 bg-error-50 p-4 text-sm text-error-600 dark:border-error-700 dark:bg-error-500/15 dark:text-error-500">Role tidak dikenali.</div>
@endswitch

@endsection
