@extends('CP.metronic.index')

@section('subpageTitle', t('About Office'))
@section('subpageTitleLink', route('about_office.index'))

@section('title', t('About Office'))
@section('subpageTitle', t('About Office'))

@push('styles')
    <link href="{{ asset('css/custom.css?v=1') }}" rel="stylesheet" type="text/css" />
@endpush

@section('content')
    @include('CP.partials.notification')

    <!--begin::Content container-->
    <div class="card mb-5 mb-xl-5" id="kt_form_tabs">
        <div class="card-body pt-0 pb-0">
            <div class="d-flex flex-column flex-lg-row justify-content-between">
                <!--begin::Navs-->
                <ul id="myTab"
                    class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold order-lg-1 order-2">
                    <li class="nav-item mt-2">
                        <a class="nav-link text-active-primary ms-0 me-6 px-2 py-5 {{ request()->has('tab') ? '' : 'active' }}"
                            data-bs-toggle="tab" data-bs-target="#kt_tab_pane_1" href="#kt_tab_pane_1">
                            <span class="svg-icon svg-icon-2 me-2"></span>
                            {{ t('About Office') }}
                        </a>
                    </li>
                </ul>
                <!--end::Navs-->
            </div>
        </div>
    </div>

    <form action="{{ route('about_office.addedit', ['Id' => $_model->id ?? null]) }}" method="POST" id="kt_add_edit_form"
        enctype="multipart/form-data">
        @csrf

        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="kt_tab_pane_1" role="tabpanel">
                <div class="card mb-5 mb-xl-10">
                    <div class="card-header">
                        <div class="card-title m-0">
                            <h3 class="fw-bold m-0">{{ t('About Office Details') }}</h3>
                        </div>
                    </div>

                    <div class="card mb-5 mb-xl-10">
                        <div class="card-body p-9">
                            <div class="row">
                                {{-- Translatable Title Fields --}}
                                @foreach (config('app.locales') as $locale)
                                    <div class="col-md-4">
                                        <div class="fv-row mb-7">
                                            <label class="fw-semibold fs-6 mb-2">
                                                {{ t('Name') }}
                                                <small>({{ strtoupper($locale) }})</small>
                                            </label>
                                            @php
                                                $title = setting('about_office.title.' . $locale);
                                            @endphp
                                            <input type="text" name="title[{{ $locale }}]"
                                                class="form-control form-control-solid mb-3 mb-lg-0 validate-required @error("title.$locale") is-invalid @enderror"
                                                placeholder="{{ t('Enter Name in ' . strtoupper($locale)) }}"
                                                value="{{ old("title.$locale", $title) }}" />
                                            @error("title.$locale")
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                @endforeach

                            </div>

                            <div class="row">
                                {{-- Translatable Description Fields --}}
                                @foreach (config('app.locales') as $locale)
                                    <div class="col-md-4">
                                        <div class="fv-row mb-7">
                                            <label class="fw-semibold fs-6 mb-2">
                                                {{ t('Description') }}
                                                <small>({{ strtoupper($locale) }})</small>
                                            </label>
                                            @php
                                                $description = setting('about_office.description.' . $locale);
                                            @endphp
                                            <textarea name="description[{{ $locale }}]"
                                                class="form-control form-control-solid mb-3 mb-lg-0 validate-required @error("description.$locale") is-invalid @enderror"
                                                placeholder="{{ t('Enter Description in ' . strtoupper($locale)) }}">{{ old("description.$locale", $description) }}</textarea>
                                            @error("description.$locale")
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="row">
                                <!-- Image Upload -->
                                <div class="col-md-6">
                                    <div class="fv-row mb-7">
                                        <label class="fw-semibold fs-6 mb-2">{{ t('Image') }}</label>
                                        <input type="file" name="image" id="image"
                                            class="form-control form-control-solid @error('image') is-invalid @enderror"
                                            accept="image/*">
                                        @error('image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        @php
                                            $image = asset('storage/' . setting('about_office.image'));
                                        @endphp
                                        @if ($image)
                                            <div class="mt-3">
                                            <a href="{{ $image }}" target="_blank">
                                                <img src="{{ $image }}" alt="Current Image" class="img-thumbnail"
                                                style="max-width: 100px; max-height: 100px;">
                                            <p class="text-muted mt-1">{{ t('Current image') }}</p>
                                            </a>
                                            </div>
                                        @endif
                                        <div class="form-text">{{ t('Upload  image (optional, max 2MB)') }}</div>
                                    </div>
                                </div>


                            </div>


                            <!-- Image Preview Section -->
                            <div class="row" id="image-preview-section" style="display: none;">
                                <div class="col-md-6">
                                    <div class="fv-row mb-7">
                                        <label class="fw-semibold fs-6 mb-2">{{ t('Image Preview') }}</label>
                                        <div class="border border-dashed border-gray-300 rounded p-3">
                                            <img id="image-preview" src="" alt="Image Preview" class="img-fluid"
                                                style="max-height: 200px;">
                                        </div>
                                    </div>
                                </div>

                            </div>

                            {{-- Repeater for features --}}
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="fw-semibold fs-6 mb-2">
                                        {{ t('Features') }}
                                    </label>
                                    <div class="form-group repeater">
                                        <div data-repeater-list="features" class="service-repeater">
                                            @php
                                                $features = setting('about_office.features');
                                                $features_old = is_array($features) ? $features : [$features];
                                                $features = old('features', $features_old);
                                                // dd($features);
                                            @endphp
                                            @foreach ($features as $feature)
                                            <div data-repeater-item class="mb-2">
                                                <div class="row">
                                                    <div class="col-5">
                                                        <label class="fw-semibold fs-6 mb-2">{{ t('Hebrew Feature') }}</label>
                                                        <textarea name="he" class="form-control" placeholder="{{ t('Enter feature') }}" rows="3">{{ is_array($feature) ? $feature['he']??'' : $feature }}</textarea>
                                                    </div>
                                                    <div class="col-5">
                                                        <label class="fw-semibold fs-6 mb-2">{{ t('Arabic Feature') }}</label>
                                                        <textarea name="ar" class="form-control" placeholder="{{ t('Enter feature') }}" rows="3">{{ is_array($feature) ? $feature['ar']??'' : $feature }}</textarea>
                                                    </div>
                                                    <div class="col-2 d-flex align-items-center">
                                                        <button type="button" data-repeater-delete
                                                            class="btn btn-outline-danger btn-sm">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach

                                        </div>
                                        <button type="button" data-repeater-create class="btn btn-primary btn-sm">
                                            <i class="feather icon-plus"></i> {{ t('Add Feature') }}
                                        </button>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>


            </div>
        </div>

        <div class="d-flex justify-content-end mt-5">
            <button type="submit" class="btn btn-primary" id="kt_add_edit_submit">
                <span class="indicator-label">{{ t('Save') }}</span>
                <span class="indicator-progress">{{ t('Please wait...') }}
                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                </span>
            </button>
        </div>
    </form>

    {{-- @include('CP.about_office.scripts.addeditJS') --}}
@endsection


@push('scripts')
    <script src="{{ asset('js/repeater/jquery.repeater.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('.repeater').repeater({
                show: function() {
                    $(this).slideDown();
                },

                hide: function(deleteElement) {
                    if (confirm('Are you sure you want to delete this element?')) {
                        $(this).slideUp(deleteElement);
                    }
                },

                ready: function(setIndexes) {
                    // $dragAndDrop.on('drop', setIndexes);
                },
                isFirstItemUndeletable: true
            })

        });
    </script>
@endpush
