@extends('agent.layouts.master')

@push('css')
<style>
    .error-text { color: red; font-size: 0.9em; }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endpush

@section('breadcrumb')
    @include('agent.components.breadcrumb',['breadcrumbs' => [
        [
            'name'  => __("Dashboard"),
            'url'   => setRoute("agent.dashboard"),
        ]
    ], 'active' => __(@$page_title)])
@endsection

@section('content')
<div class="body-wrapper">
    <div class="dashboard-area mt-10">
        <div class="dashboard-header-wrapper">
            <h3>{{ __($page_title) }}</h3>
            <a href="{{ setRoute('agent.retailer.recipient.add') }}" class="btn--base">{{ __("Add") }} <i class="fas fa-plus-circle ms-2"></i></a>
        </div>
    </div>
    <div class="dashboard-list-area mt-20">
        <div class="dashboard-list-wrapper">
            @forelse ($retailers ?? []  as $data)
            <div class="dashboard-list-item-wrapper" id="row-{{ $data->id }}">
                <div class="dashboard-list-item sent">
                    <div class="dashboard-list-left">
                        <div class="dashboard-list-user-wrapper">
                            <div class="dashboard-list-user-icon">
                                <i class="las la-arrow-up"></i>
                            </div>
                            <div class="dashboard-list-user-content">
                                <h4 class="title">{{ @$data->fullname }}
                                    @if($data->type == "wallet-to-wallet-transfer")
                                        <span class="text-success">( {{@$basic_settings->site_name}} {{__("rWallet")}} )</span>
                                    @elseif($data->type == "cash-pickup")
                                        <span class="text-success">( {{ __("r".@$data->type)}} )</span>
                                    @else
                                        <span class="text-success">( {{ __(@$data->type)}} )</span>
                                    @endif </h4>
                                <span class="sub-title text--warning">{{ @$data->email }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="dashboard-list-right">
                        <div class="dashboard-list-right-btn-area">
                            <a href="{{ setRoute('agent.retailer.recipient.edit',$data->id) }}" class="btn--base"><i class="fas fa-edit"></i></a>
                            <button type="button" class="btn--base delete-btn" data-id="{{ $data->id }}" data-name="{{ $data->fullname }}"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                </div>

            </div>
            @empty
            <div class="alert alert-primary text-center">
                {{ __("No Sender Recipient Found!") }}
            </div>
            @endforelse

        </div>
    </div>
    <nav>
        <ul class="pagination">
            {{ get_paginate($retailers) }}
        </ul>
    </nav>
</div>
@endsection

@push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    // Initialize toastr with proper error handling
    if (typeof toastr !== 'undefined') {
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000",
            "extendedTimeOut": "1000"
        };
    } else {
        console.error('Toastr not loaded');
    }

    $(".delete-btn").click(function(e){
        e.preventDefault();

        var target = $(this).data('id');
        var name   = $(this).data('name');

        var actionRoute = "{{ route('agent.retailer.recipient.delete', ':id') }}";
        actionRoute = actionRoute.replace(':id', target);

        var btnText = "Delete";
        var message = `Are you sure to delete <strong>${name}</strong>?`;

        // Use your existing modal function
        openAlertModal(actionRoute, target, message, btnText, "DELETE");

        $(document).off("click", "#confirmBtn").on("click", "#confirmBtn", function(e){
            e.preventDefault();

            // Show loading state in modal button
            var confirmBtn = $(this);
            var originalText = confirmBtn.html();
            confirmBtn.html('<i class="fas fa-spinner fa-spin"></i> Processing...');
            confirmBtn.prop('disabled', true);

            $.ajax({
                url: actionRoute,
                type: "DELETE",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function (response) {
                    // Close the modal
                    $('#alert-modal').modal('hide');
                    
                    if (response.success) {
                        // Show toastr success message
                        if (typeof toastr !== 'undefined') {
                            toastr.success(response.message);
                        } else {
                            alert('Success: ' + response.message);
                        }
                        
                        // Redirect to listing page after a short delay
                        setTimeout(function() {
                            window.location.href = "{{ route('agent.retailer.recipient.index') }}";
                        }, 1500);
                    } else {
                        // Show error message
                        if (typeof toastr !== 'undefined') {
                            toastr.error(response.message || "Something went wrong!");
                        } else {
                            alert('Error: ' + (response.message || "Something went wrong!"));
                        }
                        // Restore button state
                        confirmBtn.html(originalText);
                        confirmBtn.prop('disabled', false);
                    }
                },
                error: function (xhr) {
                    // Close the modal
                    $('#alert-modal').modal('hide');
                    
                    // Restore button state
                    confirmBtn.html(originalText);
                    confirmBtn.prop('disabled', false);
                    
                    if (xhr.status === 404) {
                        if (typeof toastr !== 'undefined') {
                            toastr.error("Retailer not found!");
                        } else {
                            alert('Error: Retailer not found!');
                        }
                    } else {
                        if (typeof toastr !== 'undefined') {
                            toastr.error("Server error!");
                        } else {
                            alert('Error: Server error!');
                        }
                    }
                }
            });
        });
    });
</script>
@endpush