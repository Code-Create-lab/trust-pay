@extends('agent.layouts.master')

@push('css')
<style>
    .error-text { color: red; font-size: 0.9em; }
</style>
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
                            @method("PUT")
                            <input type="hidden" name="id" value="{{ $retailer->id }}">
                            <div class="row">

                                <!-- First / Last Name -->
                                <div class="col-xl-6 col-lg-6 form-group">
                                    <label>First Name<span>*</span></label>
                                    <input type="text" name="first_name" 
                                           value="{{ old('first_name', $retailer->firstname) }}" 
                                           class="form--control" placeholder="First Name" required>
                                    <small class="error-text first_name_error"></small>
                                </div>
                                <div class="col-xl-6 col-lg-6 form-group">
                                    <label>Last Name<span>*</span></label>
                                    <input type="text" name="last_name" 
                                           value="{{ old('last_name', $retailer->lastname) }}" 
                                           class="form--control" placeholder="Last Name" required>
                                    <small class="error-text last_name_error"></small>
                                </div>

                                <!-- Email -->
                                <div class="col-xl-12 col-lg-12 form-group">
                                    <label>Email Address<span>*</span></label>
                                    <input type="email" name="email" 
                                           value="{{ old('email', $retailer->email) }}" 
                                           class="form--control" placeholder="Enter Email Address" required>
                                    <small class="error-text email_error"></small>
                                </div>

                                <!-- Username -->
                                <div class="col-xl-6 col-lg-6 form-group">
                                    <label>Username<span>*</span></label>
                                    <input type="text" name="username" 
                                           value="{{ old('username', $retailer->username) }}" 
                                           class="form--control" placeholder="Enter Username" required>
                                    <small class="error-text username_error"></small>
                                </div>

                                <!-- Phone -->
                                <div class="col-xl-6 col-lg-6 form-group">
                                    <label>Phone Number<span>*</span></label>
                                    <input type="text" name="phone_number" 
                                           value="{{ old('phone_number', $retailer->full_mobile) }}" 
                                           class="form--control" placeholder="Enter Mobile Number" required>
                                    <small class="error-text phone_number_error"></small>
                                </div>

                                <!-- Submit -->
                                <div class="col-xl-12 col-lg-12">
                                    <button type="submit" class="btn--base w-100 btn-loading transfer">
                                        {{ __("Update Retailer") }} <i class="fas fa-save ms-1"></i>
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
<script>
$(document).ready(function () {
    $('#retailerForm').on('submit', function (e) {
        e.preventDefault();

        // clear errors
        $('.error-text').text('');
        $('#formMessage').html('');

        let isValid = true;

        let first_name   = $('input[name="first_name"]').val().trim();
        let last_name    = $('input[name="last_name"]').val().trim();
        let email        = $('input[name="email"]').val().trim();
        let username     = $('input[name="username"]').val().trim();
        let phone_number = $('input[name="phone_number"]').val().trim();

        // email regex
        let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        let phoneRegex = /^[0-9]{7,15}$/; // 7-15 digit number only

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
        } else if (!phoneRegex.test(phone_number)) {
            $('.phone_number_error').text("Enter a valid phone number (7–15 digits)");
            isValid = false;
        }

        if (!isValid) return; // stop submit if invalid

        let formData = $(this).serialize();

        $.ajax({
            url: "{{ route('agent.retailer.recipient.update') }}",
            type: "POST",
            data: formData,
            success: function(response) {
                if(response.success){
                    toastr.success(response.message);
                    window.location.href = "{{ route('agent.retailer.recipient.index') }}";
                }
            },
            error: function(xhr){
                toastr.error("Something went wrong!");
            }
        });

    });
});
</script>
@endpush
