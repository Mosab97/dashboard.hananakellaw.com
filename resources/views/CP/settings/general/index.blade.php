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
                    <form id="settings_form" method="POST" action="{{ route($config['full_route_name'] . '.update') }}" enctype="multipart/form-data">
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
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#tab_legal">{{ t('Legal Documents') }}</a>
                            </li>


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
                                                value="{{ Setting::get('site_phone', '') }}">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-5">
                                            <label class="form-label">{{ t('Site WhatsApp') }}</label>
                                            <input type="text" class="form-control" name="site_whatsapp"
                                                value="{{ Setting::get('site_whatsapp', '') }}">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-5">
                                            <label class="form-label">{{ t('Site Email') }}</label>
                                            <input type="text" class="form-control" name="site_email"
                                                value="{{ Setting::get('site_email', '') }}">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-5">
                                            <label class="form-label">{{ t('Years of Experience') }}</label>
                                            <input type="text" class="form-control" name="years_of_experience"
                                                value="{{ Setting::get('years_of_experience', '') }}">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-5">
                                            <label class="form-label">{{ t('Logo') }}</label>
                                            <input type="file" class="form-control" name="logo" accept="image/*">
                                            @php
                                                $logo = asset('storage/' . Setting::get('logo', ''));
                                            @endphp
                                            @if ($logo)
                                                <a href="{{ $logo }}" target="_blank">
                                                    <img src="{{ $logo }}" alt="{{ t('Logo') }}"
                                                        class="img-fluid" style="width: 100px; height: 100px;">
                                                    <p class="text-muted mt-1">{{ t('Current logo') }}</p>
                                                </a>
                                            @endif
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
                                                    value="{{ Setting::get('site_address', [])[$language] ?? '' }}">
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
                                                value="{{ Setting::get('social_facebook', '') }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-5">
                                            <label class="form-label">{{ t('Instagram URL') }}</label>
                                            <input type="url" class="form-control" name="social_instagram"
                                                value="{{ Setting::get('social_instagram', '') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-6">
                                    <div class="col-lg-6">
                                        <div class="mb-5">
                                            <label class="form-label">{{ t('Twitter URL') }}</label>
                                            <input type="url" class="form-control" name="social_twitter"
                                                value="{{ Setting::get('social_twitter', '') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Social Media Tab-->

                            <!--begin::Legal Documents Tab-->
                            <div class="tab-pane fade" id="tab_legal" role="tabpanel">

                                <div class="row">
                                    <!-- Privacy Policy -->
                                    @foreach (config('app.locales') as $language)
                                        <div class="col-6">
                                            <div class="mb-5">
                                                <label class="form-label">{{ t('Privacy Policy') }}
                                                    ({{ strtoupper($language) }})
                                                </label>
                                                <textarea class="form-control" rows="6" name="privacy_policy[{{ $language }}]">{!! Setting::get('privacy_policy', [])[$language] ?? '' !!}</textarea>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="row">
                                    <!-- Terms & Conditions -->
                                    @foreach (config('app.locales') as $language)
                                        <div class="col-6">
                                            <div class="mb-5">
                                                <label class="form-label">{{ t('Terms & Conditions') }}
                                                    ({{ strtoupper($language) }})
                                                </label>
                                                <textarea class="form-control" rows="6" name="terms_conditions[{{ $language }}]">{!! Setting::get('terms_conditions', [])[$language] ?? '' !!}</textarea>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="row">
                                    <!-- FAQ -->
                                    @foreach (config('app.locales') as $language)
                                        <div class="col-6">
                                            <div class="mb-5">
                                                <label class="form-label">{{ t('FAQ') }}
                                                    ({{ strtoupper($language) }})
                                                </label>
                                                <textarea class="form-control" rows="6" name="faq[{{ $language }}]">{!! Setting::get('faq', [])[$language] ?? '' !!}</textarea>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="row">
                                    <!-- Disclaimer -->
                                    @foreach (config('app.locales') as $language)
                                        <div class="col-6">
                                            <div class="mb-5">
                                                <label class="form-label">{{ t('Disclaimer') }}
                                                    ({{ strtoupper($language) }})
                                                </label>
                                                <textarea class="form-control" rows="6" name="disclaimer[{{ $language }}]">{!! Setting::get('disclaimer', [])[$language] ?? '' !!}</textarea>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>




                            </div>
                            <!--end::Legal Documents Tab-->



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



@push('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Find all textarea elements in the settings form
            const form = document.getElementById('settings_form');
            if (!form) return;

            const textareas = form.querySelectorAll('textarea');
            const editors = []; // Store editor instances for form submission

            // Initialize CKEditor for each textarea
            textareas.forEach((textarea, index) => {
                ClassicEditor.create(textarea, {
                        placeholder: 'Enter content...',
                        toolbar: {
                            items: [
                                'heading', '|',
                                'bold', 'italic', 'underline', 'link', '|',
                                'bulletedList', 'numberedList', 'outdent', 'indent', '|',
                                'blockQuote', 'insertTable', 'undo', 'redo'
                            ]
                        },
                        table: {
                            contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
                        }
                        // language: 'ar' // uncomment if you want full UI in Arabic
                    })
                    .then(editor => {
                        // Store editor instance
                        editors.push({
                            editor: editor,
                            textarea: textarea
                        });

                        console.log(`CKEditor initialized for textarea: ${textarea.name || 'unnamed'}`);
                    })
                    .catch(error => {
                        console.error(
                            `Failed to initialize CKEditor for textarea ${textarea.name || 'unnamed'}:`,
                            error);
                    });
            });

            // Handle form submission - sync all editor data
            form.addEventListener('submit', function(e) {
                editors.forEach(({
                    editor,
                    textarea
                }) => {
                    try {
                        textarea.value = editor.getData();
                    } catch (error) {
                        console.error(`Failed to sync data for textarea ${textarea.name}:`, error);
                    }
                });
            });
        });
    </script>
@endpush
