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
        <div class="row justify-content-center mb-30-none">
            <div class="col-xl-12 mb-30">
                <div class="dash-payment-item-wrapper">
                    <div class="dash-payment-item active">
                        <div class="dash-payment-title-area">
                            <span class="dash-payment-badge">!</span>
                            <h5 class="title">{{ @$page_title }}</h5>
                        </div>
                        <div class="dash-payment-body">
                            <form id="retailerForm" class="card-form" method="POST" novalidate>
                                @csrf
                                <div class="row">

                                    <!-- First / Last Name -->
                                    <div class="col-xl-6 col-lg-6 form-group">
                                        <label>First Name</label>
                                        <input type="text" name="first_name" class="form--control" placeholder="First Name" required>
                                        <small class="error-text first_name_error"></small>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 form-group">
                                        <label>Last Name</label>
                                        <input type="text" name="last_name" class="form--control" placeholder="Last Name" required>
                                        <small class="error-text last_name_error"></small>
                                    </div>

                                    <!-- Email -->
                                    <div class="col-xl-12 col-lg-12 form-group">
                                        <label>Email Address<span>*</span></label>
                                        <input type="email" name="email" class="form--control" placeholder="Enter Email Address" required>
                                        <small class="error-text email_error"></small>
                                    </div>

                                    <!-- Username -->
                                    <div class="col-xl-6 col-lg-6 form-group">
                                        <label>Username</label>
                                        <input type="text" name="username" class="form--control" placeholder="Enter Username" required>
                                        <small class="error-text username_error"></small>
                                    </div>

                                    <!-- Phone -->
                                    <div class="col-xl-6 col-lg-6 form-group">
                                        <label>Phone Number<span>*</span></label>
                                        <input type="text" name="phone_number" class="form--control" placeholder="Enter Mobile Number" required>
                                        <small class="error-text phone_number_error"></small>
                                    </div>

                                    <!-- Submit -->
                                    <div class="col-xl-12 col-lg-12">
                                        <button type="submit" class="btn--base w-100 submitBtn">
                                            {{ __("Add Retailer") }} <i class="fas fa-plus-circle ms-1"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <div id="formMessage" class="mt-3"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
$(document).ready(function () {
    $('#retailerForm').on('submit', function (e) {
        e.preventDefault();

        // clear previous errors
        $('.error-text').text('');
        $('#formMessage').html('');

        let isValid = true;

        // get form values
        let first_name   = $('input[name="first_name"]').val().trim();
        let last_name    = $('input[name="last_name"]').val().trim();
        let email        = $('input[name="email"]').val().trim();
        let username     = $('input[name="username"]').val().trim();
        let phone_number = $('input[name="phone_number"]').val().trim();

        // simple regex for email
        let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        // validation
        if (first_name === "") {
            $('.first_name_error').text("First name is required");
            isValid = false;
        }
        if (last_name === "") {
            $('.last_name_error').text("Last name is required");
            isValid = false;
        }
        if (email === "") {
            $('.email_error').text("Email is required");
            isValid = false;
        } else if (!emailRegex.test(email)) {
            $('.email_error').text("Enter a valid email address");
            isValid = false;
        }
        if (username === "") {
            $('.username_error').text("Username is required");
            isValid = false;
        }
        if (phone_number === "") {
            $('.phone_number_error').text("Phone number is required");
            isValid = false;
        }

        // stop if invalid
        if (!isValid) return;

        // if valid → send AJAX
        let formData = $(this).serialize();

        $.ajax({
            url: "{{ route('agent.retailer.recipient.add') }}",
            method: "POST",
            data: formData,
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#retailerForm')[0].reset();
                    setTimeout(function () {
                        window.location.href = "{{ route('agent.retailer.recipient.index') }}";
                    }, 1500);
                } else {
                    toastr.error(response.message);
                    $('.submitBtn').attr('disabled',false);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, function (key, value) {
                        $('.'+key+'_error').text(value[0]);
                    });
                    $('.submitBtn').attr('disabled',false);
                } else {
                    toastr.error("Something went wrong!");
                }
            }
        });
    });
});
</script>
@endpush
