@extends('CP.metronic.index')

@section('title', t('General Settings'))
@section('subpageTitle', t('General Settings'))

@section('content')
    <!--begin::Content container-->
    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <!--begin::Col-->
        <div class="col-md-12 col-lg-12 col-xl-12 col-xxl-12">
            <!--begin::Card-->
            <div class="card">
                <!--begin::Card header-->
                <div class="card-header border-0 pt-6">
                    <!--begin::Card title-->
                    <div class="card-title">
                        <h2>{{ t('General Settings') }}</h2>
                    </div>
                    <!--end::Card title-->
                </div>
                <!--end::Card header-->

                <!--begin::Card body-->
                <div class="card-body py-4">
                    <form id="settings_form" method="POST" action="{{ route($config['full_route_name'] . '.update') }}">
                        @csrf

                        <!--begin::Tabs-->
                        <ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab"
                                    href="#tab_site_info">{{ t('Site Information') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab"
                                    href="#tab_social_media">{{ t('Social Media') }}</a>
                            </li>
                            {{-- <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#tab_legal">{{ t('Legal Documents') }}</a>
                            </li> --}}


                        </ul>
                        <!--end::Tabs-->

                        <!--begin::Tab content-->
                        <div class="tab-content" id="settingsTabContent">

                            <!--begin::Site Information Tab-->
                            <div class="tab-pane fade show active" id="tab_site_info" role="tabpanel">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="mb-5">
                                            <label class="form-label">{{ t('Site Phone') }}</label>
                                            <input type="text" class="form-control" name="site_phone"
                                                value="{{ $settings['site_phone'] }}">
                                        </div>

                                    </div>
                                    <div class="col-6">
                                        <div class="mb-5">
                                            <label class="form-label">{{ t('Site Email') }}</label>
                                            <input type="text" class="form-control" name="site_email"
                                                value="{{ $settings['site_email'] }}">
                                        </div>
                                    </div>

                                    @foreach (config('app.locales') as $language)
                                        <div class="col-6">
                                            <div class="mb-5">
                                                <label class="form-label">{{ t('Site Address') }}
                                                    ({{ strtoupper($language) }})
                                                </label>
                                                <input type="text" class="form-control"
                                                    name="site_address[{{ $language }}]"
                                                    value="{{ $settings['site_address'][$language] ?? '' }}">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <!--end::Site Information Tab-->

                            <!--begin::Social Media Tab-->
                            <div class="tab-pane fade " id="tab_social_media" role="tabpanel">
                                <div class="row mb-6">
                                    <div class="col-lg-6">
                                        <div class="mb-5">
                                            <label class="form-label">{{ t('Facebook URL') }}</label>
                                            <input type="url" class="form-control" name="social_facebook"
                                                value="{{ $settings['social_facebook'] }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-5">
                                            <label class="form-label">{{ t('Instagram URL') }}</label>
                                            <input type="url" class="form-control" name="social_instagram"
                                                value="{{ $settings['social_instagram'] }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-6">
                                    <div class="col-lg-6">
                                        <div class="mb-5">
                                            <label class="form-label">{{ t('Twitter URL') }}</label>
                                            <input type="url" class="form-control" name="social_twitter"
                                                value="{{ $settings['social_twitter'] }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Social Media Tab-->
{{--
                            <!--begin::Legal Documents Tab-->
                            <div class="tab-pane fade" id="tab_legal" role="tabpanel">

                                <!-- Privacy Policy -->
                                <div class="mb-10">
                                    <h4 class="mb-3">{{ t('Privacy Policy') }}</h4>

                                    <!-- English -->
                                    <div class="mb-5">
                                        <label class="form-label">{{ t('English') }}</label>
                                        <textarea class="form-control" id="privacy_policy_en" name="privacy_policy[en]" rows="6">{{ $settings['privacy_policy']['en'] }}</textarea>
                                    </div>

                                    <!-- Arabic -->
                                    <div class="mb-5">
                                        <label class="form-label">{{ t('Arabic') }}</label>
                                        <textarea class="form-control" id="privacy_policy_ar" name="privacy_policy[ar]" rows="6" dir="rtl">{{ $settings['privacy_policy']['ar'] }}</textarea>
                                    </div>
                                </div>

                                <!-- Terms & Conditions -->
                                <div class="mb-10">
                                    <h4 class="mb-3">{{ t('Terms & Conditions') }}</h4>

                                    <!-- English -->
                                    <div class="mb-5">
                                        <label class="form-label">{{ t('English') }}</label>
                                        <textarea class="form-control" id="terms_conditions_en" name="terms_conditions[en]" rows="6">{{ $settings['terms_conditions']['en'] }}</textarea>
                                    </div>

                                    <!-- Arabic -->
                                    <div class="mb-5">
                                        <label class="form-label">{{ t('Arabic') }}</label>
                                        <textarea class="form-control" id="terms_conditions_ar" name="terms_conditions[ar]" rows="6" dir="rtl">{{ $settings['terms_conditions']['ar'] }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <!--end::Legal Documents Tab--> --}}



                        </div>
                        <!--end::Tab content-->

                        <div class="d-flex justify-content-end mt-8">
                            <button type="submit" class="btn btn-primary" id="submit_btn">
                                <span class="indicator-label">{{ t('Save Changes') }}</span>
                                <span class="indicator-progress">
                                    {{ t('Please wait...') }}
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
        </div>
    </div>
    <!--end::Content container-->
@endsection
