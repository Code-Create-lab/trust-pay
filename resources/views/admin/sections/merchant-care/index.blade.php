@extends('admin.layouts.master')

@push('css')
@endpush

@section('page-title')
    @include('admin.components.page-title', ['title' => __($page_title)])
@endsection

@section('breadcrumb')
    @include('admin.components.breadcrumb', [
        'breadcrumbs' => [
            [
                'name' => __('Dashboard'),
                'url' => setRoute('admin.dashboard'),
            ],
        ],
        'active' => __('Distributor Care'),
    ])
@endsection

@section('content')
    <div class="table-area">
        <div class="table-wrapper">
            <div class="table-header">
                <h5 class="title">{{ __("All Distributor") }}</h5>
                <div class="table-btn-area">
                    @include('admin.components.search-input',[
                        'name'  => 'merchant_search',
                    ])
                     @include('admin.components.link.add-default',[
                        'href'          => "#admin-add",
                        'class'         => "modal-btn",
                        'text'          => __("Add Admin"),
                        'permission'    => "admin.admins.admin.store"
                    ])
                </div>
            </div>
            <div class="table-responsive">
                @include('admin.components.data-table.merchant-table',compact('merchants'))
            </div>
        </div>

         {{-- Admin Add Modal --}}
    @include('admin.components.modals.add-distributor')

    {{-- Admin Edit Modal --}}
    {{-- @include('admin.components.modals.edit-admin',compact('admin_roles')) --}}
        {{ get_paginate($merchants) }}
    </div>
@endsection

@push('script')
    <script>
        itemSearch($("input[name=merchant_search]"),$(".merchant-search-table"),"{{ setRoute('admin.merchants.search') }}");
    </script>
@endpush
