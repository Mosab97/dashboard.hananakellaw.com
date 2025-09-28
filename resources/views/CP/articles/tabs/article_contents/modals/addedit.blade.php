<div class="modal-content">
    <div class="modal-header">
        <h2 class="fw-bold">
            {{ $_model->exists ? t('Edit ' . $config['singular_name']) : t('Add ' . $config['singular_name']) }}
        </h2>
        <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
            <span class="svg-icon svg-icon-1">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                        transform="rotate(-45 6 17.3137)" fill="currentColor" />
                    <rect x="7.41422" y="6" width="16" height="2" rx="1"
                        transform="rotate(45 7.41422 6)" fill="currentColor" />
                </svg>
            </span>
        </div>
    </div>

    <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
        <form id="{{ $config['singular_key'] }}_modal_form" class="form"
            action="{{ route($config['full_route_name'] . '.addedit', ['article' => $article->id]) }}">
            @if (isset($_model))
                <input type="hidden" name="{{ $config['id_field'] }}" value="{{ $_model->id }}">
            @endif

            <div class="d-flex flex-column scroll-y me-n7 pe-7">
                <div class="row">
                    {{-- Translatable Name Fields --}}
                    @foreach (config('app.locales') as $locale)
                        <div class="col-md-4">
                            <div class="fv-row mb-7">
                                <label class="fw-semibold fs-6 mb-2">
                                    {{ t('Title') }}
                                    <small>({{ strtoupper($locale) }})</small>
                                </label>
                                <input type="text" name="title[{{ $locale }}]"
                                    class="form-control form-control-solid mb-3 mb-lg-0 validate-required @error("title.$locale") is-invalid @enderror"
                                    placeholder="{{ t('Enter Title in ' . strtoupper($locale)) }}"
                                    value="{{ old("title.$locale", isset($_model) ? $_model->getTranslation('title', $locale) : '') }}" />
                                @error("title.$locale")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    @endforeach

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
                                    $features_old = is_array($_model->features)
                                        ? $_model->features
                                        : [$_model->features];
                                    $features = old('features', $features_old);
                                @endphp
                                @foreach ($features as $feature)
                                    <div data-repeater-item class="mb-2">
                                        <div class="row">
                                            <div class="col-5">
                                                <label class="fw-semibold fs-6 mb-2">{{ t('Hebrew Feature') }}</label>
                                                <textarea name="he" class="form-control" placeholder="{{ t('Enter feature') }}" rows="3">{{ is_array($feature) ? $feature['he'] ?? '' : $feature }}</textarea>
                                            </div>
                                            <div class="col-5">
                                                <label class="fw-semibold fs-6 mb-2">{{ t('Arabic Feature') }}</label>
                                                <textarea name="ar" class="form-control" placeholder="{{ t('Enter feature') }}" rows="3">{{ is_array($feature) ? $feature['ar'] ?? '' : $feature }}</textarea>
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


                <div class="row">


                    {{-- Is active Field --}}
                    <div class="col-md-6">
                        <div class="fv-row mb-7">
                            <div class="form-check form-switch form-check-custom form-check-solid mt-5">
                                <input class="form-check-input" type="checkbox" name="active" value="1"
                                    {{ isset($_model) && $_model->active ? 'checked' : '' }} />
                                <label class="form-check-label fw-semibold fs-6" for="active">
                                    {{ t('Active') }}
                                </label>
                            </div>
                        </div>
                    </div>



                </div>
            </div>

            <div class="text-center pt-15">
                <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">{{ t('Discard') }}</button>
                <button type="submit" class="btn btn-primary"
                    data-kt-modal-action="submit_{{ $config['singular_key'] }}">
                    <span class="indicator-label">{{ t('Submit') }}</span>
                    <span class="indicator-progress">{{ t('Please wait...') }}
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
