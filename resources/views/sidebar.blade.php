@extends('layouts.app')

@section('content')
<!-- Menu -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme"> 
    <div class="app-brand demo">    
        <a href="{{ route('publisher') }}" class="app-brand-link">
            <img src="{{ asset_url('img/favicon.svg') }}" alt="Logo" class="w-60 small-logo">
            <img src="{{ asset_url('img/logo.svg') }}" alt="Logo" class="w-100 full-logo">
            <!-- <span class="app-brand-logo demo">
                <img src="{{ asset_url('img/logo.svg') }}" alt="Logo">
            </span> -->
            <!-- <span class="app-brand-text demo menu-text fw-bold">Vuexy</span> -->
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
            <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboards -->
        <li class="menu-item {{ (isset($slug) && $slug == 'dashboard') ? 'active' : '' }}">
            <a href="{{ route('lslbadmin.dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-smart-home"></i>
                <div data-i18n="Dashboard">Dashboard</div>
            </a>
        </li>

        <!-- Users List -->
        <li class="menu-item {{ (isset($slug) && $slug == 'users-list') ? 'active' : '' }}">
            <a href="{{ route('lslbadmin.users') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-users"></i>
                <div data-i18n="Users">Users</div>
            </a>
        </li>

        <!-- My Websites -->
        <li class="menu-item {{ (isset($slug) && $slug == 'websites-list') ? 'active' : '' }}">
            <a href="{{ route('lslbadmin.websites') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-world"></i>
                <div data-i18n="Websites">Websites</div>
            </a>
        </li>

        <!-- Orders -->
        <li class="menu-item {{ (isset($slug) && $slug == 'orders-list') ? 'active' : '' }}">
            <a href="{{ route('lslbadmin.orders') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-briefcase"></i>
                <div data-i18n="Orders">Orders</div>
            </a>
        </li>

        <!-- Payments -->
        <!-- <li class="menu-item {{ (isset($slug) && $slug == 'payment') ? 'active' : '' }}">
            <a href="{{ url('/publisher/payment') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-wallet"></i>
                <div data-i18n="Payments">Payments</div>
            </a>
        </li> -->
    </ul>
</aside>
<!-- / Menu -->
@yield('sidebar-content')
@endsection