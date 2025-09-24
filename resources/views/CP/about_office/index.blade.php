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

    <form action="{{ route('about_office.addedit', ['Id' => $_model->id ?? null]) }}" method="POST"
        id="kt_add_edit_form" enctype="multipart/form-data">
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
                                                value="{{ old("title.$locale", $title) }}"
                                                />
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
                                            <textarea name="description[{{ $locale }}]"
                                                class="form-control form-control-solid mb-3 mb-lg-0 validate-required @error("description.$locale") is-invalid @enderror"
                                                placeholder="{{ t('Enter Description in ' . strtoupper($locale)) }}">{{ old("description.$locale", isset($_model) ? $_model->getTranslation('description', $locale) : '') }}</textarea>
                                            @error("description.$locale")
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="row">
                                <!-- Category Image Upload -->
                                <div class="col-md-6">
                                    <div class="fv-row mb-7">
                                        <label class="fw-semibold fs-6 mb-2">{{ t('Slider Image') }}</label>
                                        <input type="file" name="image" id="image"
                                            class="form-control form-control-solid @error('image') is-invalid @enderror"
                                            accept="image/*">
                                        @error('image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        {{-- @if ($_model->exists && $_model->image)
                                            <div class="mt-3">
                                                <img src="{{ $_model->image_path }}" alt="Current Image"
                                                    class="img-thumbnail" style="max-width: 100px; max-height: 100px;">
                                                <p class="text-muted mt-1">{{ t('Current image') }}</p>
                                            </div>
                                        @endif --}}
                                        <div class="form-text">{{ t('Upload slider image (optional, max 2MB)') }}</div>
                                    </div>
                                </div>


                            </div>

                            <div class="row">
                                <!-- Active Status -->
                                <div class="col-md-6">
                                    <div class="fv-row mb-7">
                                        <div class="form-check form-switch form-check-custom form-check-solid">
                                            <input class="form-check-input" type="checkbox" name="active" id="active"
                                                value="1"
                                                {{ old('active', $_model->active ?? true) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-semibold fs-6" for="active">
                                                {{ t('Active') }}
                                            </label>
                                        </div>
                                        <div class="form-text">
                                            {{ t('Inactive sliders will not be displayed to customers') }}</div>
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
