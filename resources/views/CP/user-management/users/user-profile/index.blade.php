@extends('CP.metronic.index')
@section('title', t('User Profile'))
@section('subpageTitle', t('User Profile'))
@section('subpageName', t('Update Information'))

@push('styles')
    <link href="{{ asset('css/custom.css?v=1') }}" rel="stylesheet" type="text/css" />
@endpush

@section('content')
    @include('CP.partials.notification')

    <!--begin::Content container-->
    <div class="card mb-5 mb-xl-5" id="kt_profile_form_tabs">
        <div class="card-body pt-0 pb-0">
            <div class="d-flex flex-column flex-lg-row justify-content-between">
                <!--begin::Navs-->
                <ul id="myTab"
                    class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold order-lg-1 order-2">

                    <li class="nav-item mt-2">
                        <a class="nav-link text-active-primary ms-0 me-6 px-2 py-5 active" data-bs-toggle="tab"
                            data-bs-target="#kt_tab_pane_1" href="#kt_tab_pane_1">
                            <span class="svg-icon svg-icon-2 me-2">
                            </span>
                            {{ t('Personal Information') }}
                        </a>
                    </li>
                </ul>
            </div>
            <div class="d-flex my-4 justify-content-end order-lg-2 order-1">
                <a href="{{ route('home') }}" class="btn btn-sm btn-light me-2" id="kt_user_follow_button">
                    <span class="svg-icon svg-icon-2">
                        <!-- SVG content remains unchanged -->
                    </span>
                    {{ t('Exit') }}
                </a>

                <a href="#" class="btn btn-sm btn-primary" data-kt-profile-action="submit">
                    <span class="indicator-label">
                        <span class="svg-icon svg-icon-2">
                            <!-- SVG content remains unchanged -->
                        </span>
                        {{ t('Save Changes') }}
                    </span>
                    <span class="indicator-progress">
                        {{ t('Please wait...') }} <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                    </span>
                </a>
            </div>
            <!--begin::Navs-->
        </div>
    </div>
    <!--end::Content container-->

    <form class="tab-content" id="user_profile_form" method="post" enctype="multipart/form-data"
        action="{{ route('user.profile.update') }}">
        @csrf
        <div class="tab-pane fade show active" id="kt_tab_pane_1" role="tabpanel">
            <div class="card mb-5 mb-xl-10">
                <div class="card-header border-0">
                    <div class="card-title m-0">
                        <h3 class="fw-bold m-0">{{ t('User Profile') }}</h3>
                    </div>
                </div>
                <div class="card-body border-top p-9">
                    <!-- Profile Picture -->
                    <div class="row mb-8">
                        <div class="col-md-12">
                            <div class="fv-row mb-7">
                                <label class="fw-semibold fs-6 mb-2">{{ t('Profile Picture') }}</label>
                                <div class="mt-2">
                                    <div class="image-input image-input-outline image-input-circle"
                                        data-kt-image-input="true">
                                        <div class="image-input-wrapper w-125px h-125px"
                                            style="background-image: url({{ $user->avatar }})"></div>

                                        <label
                                            class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                            data-kt-image-input-action="change" data-bs-toggle="tooltip"
                                            title="{{ t('Change avatar') }}">
                                            <i class="bi bi-pencil-fill fs-7"></i>
                                            <input type="file" name="avatar" accept=".png, .jpg, .jpeg" />
                                            <input type="hidden" name="avatar_remove" value="0" />
                                        </label>

                                        <span
                                            class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                            data-kt-image-input-action="cancel" data-bs-toggle="tooltip"
                                            title="{{ t('Cancel avatar') }}">
                                            <i class="bi bi-x fs-2"></i>
                                        </span>

                                        <span
                                            class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                            data-kt-image-input-action="remove" data-bs-toggle="tooltip"
                                            title="{{ t('Remove avatar') }}">
                                            <i class="bi bi-x fs-2"></i>
                                        </span>
                                    </div>
                                    <div class="form-text">{{ t('Allowed file types: png, jpg, jpeg.') }}</div>
                                    @error('avatar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-8">
                        <!-- Full Name -->
                        <div class="col-md-3">
                            <div class="fv-row mb-7">
                                <label class="fw-semibold fs-6 mb-2 required">{{ t('Full Name') }}</label>
                                <input type="text" name="name"
                                    class="form-control form-control-solid mb-3 mb-lg-0 validate-required @error('name') is-invalid @enderror"
                                    placeholder="{{ t('Enter Full Name') }}" value="{{ old('name', $user->name) }}" />
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Email Address -->
                        <div class="col-md-3">
                            <div class="fv-row mb-7">
                                <label class="fw-semibold fs-6 mb-2 required">{{ t('Email Address') }}</label>
                                <input type="email" name="email"
                                    class="form-control form-control-solid mb-3 mb-lg-0 validate-required validate-email @error('email') is-invalid @enderror"
                                    placeholder="{{ t('Enter Email Address') }}"
                                    value="{{ old('email', $user->email) }}" />
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Mobile Number -->
                        <div class="col-md-3">
                            <div class="fv-row mb-7">
                                <label class="fw-semibold fs-6 mb-2">{{ t('Mobile Number') }}</label>
                                <input type="text" name="mobile"
                                    class="form-control form-control-solid mb-3 mb-lg-0  validate-required validate-phone @error('mobile') is-invalid @enderror"
                                    placeholder="{{ t('Enter Mobile Number') }}"
                                    value="{{ old('mobile', $user->mobile) }}" />
                                @error('mobile')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-5 mb-xl-10">
                <div class="card-header border-0">
                    <div class="card-title m-0">
                        <h3 class="fw-bold m-0">{{ t('Change Password') }}</h3>
                    </div>
                </div>
                <div class="card-body border-top p-9">
                    <div class="row mb-8">
                        <!-- Current Password -->
                        <div class="col-md-3">
                            <div class="fv-row mb-7">
                                <label class="fw-semibold fs-6 mb-2">{{ t('Current Password') }}</label>
                                <input type="password" name="current_password"
                                    class="form-control form-control-solid mb-3 mb-lg-0 @error('current_password') is-invalid @enderror"
                                    placeholder="{{ t('Enter Current Password') }}" />
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">{{ t('Fill this only if you want to change password') }}</div>
                            </div>
                        </div>

                        <!-- New Password -->
                        <div class="col-md-3">
                            <div class="fv-row mb-7">
                                <label class="fw-semibold fs-6 mb-2">{{ t('New Password') }}</label>
                                <input type="password" name="password"
                                    class="form-control form-control-solid mb-3 mb-lg-0 @error('password') is-invalid @enderror"
                                    placeholder="{{ t('Enter New Password') }}" />
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="col-md-3">
                            <div class="fv-row mb-7">
                                <label class="fw-semibold fs-6 mb-2">{{ t('Confirm Password') }}</label>
                                <input type="password" name="password_confirmation"
                                    class="form-control form-control-solid mb-3 mb-lg-0"
                                    placeholder="{{ t('Confirm New Password') }}" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Initialize avatar image input
            const imageInputs = document.querySelectorAll('[data-kt-image-input]');
            imageInputs.forEach(element => {
                // Initialize the KTImageInput if available
                if (typeof KTImageInput !== 'undefined') {
                    new KTImageInput(element);
                }

                // Manual handling for the remove button
                const removeBtn = element.querySelector('[data-kt-image-input-action="remove"]');
                const removeInput = element.querySelector('input[name="avatar_remove"]');
                const fileInput = element.querySelector('input[type="file"]');

                if (removeBtn && removeInput) {
                    removeBtn.addEventListener('click', () => {
                        removeInput.value = '1';
                        fileInput.value = '';
                    });
                }

                // When a new file is selected, reset the remove flag
                if (fileInput && removeInput) {
                    fileInput.addEventListener('change', () => {
                        if (fileInput.files.length > 0) {
                            removeInput.value = '0';
                        }
                    });
                }
            });

            // Initialize the form handler with fileUpload option
            const formHandler = RegularFormHandler.initialize(
                '#user_profile_form',
                '[data-kt-profile-action="submit"]', {
                    hasFileUploads: true
                }
            );
        });
    </script>
@endpush
