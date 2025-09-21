@php
    $name = isset($_model) ? $_model->getTranslations()['name'] : null;
@endphp

<div class="modal-content">
    <div class="modal-header">
        <h2 class="fw-bold">{{ t('Add ' . $_model::ui['p_ucf']) }}</h2>
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
        <form id="{{ $_model::ui['s_lcf'] }}_modal_form" class="form" enctype="multipart/form-data"
            data-editMode="{{ isset($_model) ? 'enabled' : 'disabled' }}"
            action="{{ route($_model::ui['route'] . '.addedit') }}">
            @csrf
            @if (isset($_model))
                <input type="hidden" name="{{ $_model::ui['_id'] }}" value="{{ $_model->id }}">
            @endif

            <div class="row">
                <!-- Avatar Upload -->

                <div class="col-12">
                    <!-- First row with Avatar and Active Status -->
                    <div class="row mb-7">
                        <!-- Avatar -->
                        <div class="col-md-8">
                            <div class="fv-row">
                                <label class="d-block fw-semibold fs-6 mb-5">{{ t('Avatar') }}</label>
                                <style>
                                    .image-input-placeholder {
                                        background-image: url("{{ asset('media/svg/files/blank-image.svg') }}");
                                    }

                                    [data-bs-theme="dark"] .image-input-placeholder {
                                        background-image: url("{{ asset('media/svg/files/blank-image-dark.svg') }}");
                                    }
                                </style>
                                <div class="image-input image-input-outline image-input-placeholder"
                                    data-kt-image-input="true">
                                    <div class="image-input-wrapper w-125px h-125px"
                                        style="background-image: url('{{ $_model->avatar }}')" {{-- style="background-image: url('{{ (isset($_model) && $_model->exist) ? ($_model->avatar != null ? asset('images/' . $_model->avatar) : asset('media/avatars/blank.png')) : asset('media/avatars/blank.png') }}')" --}}>
                                    </div>
                                    <label
                                        class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                        data-kt-image-input-action="change" data-bs-toggle="tooltip"
                                        title="Change avatar">
                                        <i class="bi bi-pencil-fill fs-7"></i>
                                        <input type="file" name="avatar" accept=".png, .jpg, .jpeg" />
                                        <input type="hidden" name="avatar_remove" />
                                    </label>
                                    <span
                                        class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                        data-kt-image-input-action="cancel" data-bs-toggle="tooltip"
                                        title="Cancel avatar">
                                        <i class="bi bi-x fs-2"></i>
                                    </span>
                                    <span
                                        class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                        data-kt-image-input-action="remove" data-bs-toggle="tooltip"
                                        title="Remove avatar">
                                        <i class="bi bi-x fs-2"></i>
                                    </span>
                                </div>
                                <div class="form-text">{{ t('Allowed file types: png, jpg, jpeg.') }}</div>
                            </div>
                        </div>

                        <!-- Active Status -->
                        <div class="col-md-4 d-flex align-items-center">
                            @foreach ([['name' => 'active', 'label' => 'Active']] as $checkbox)
                                <div class="align-items-center d-flex flex-row me-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                            id="{{ $checkbox['name'] }}" name="{{ $checkbox['name'] }}"
                                            @isset($_model)
                            @checked($_model->{$checkbox['name']} == 1)
                        @endisset
                                            @if (old($checkbox['name'])) checked @endif>
                                        <label class="form-check-label cursor-pointer text-primary fw-semibold fs-6"
                                            for="{{ $checkbox['name'] }}">
                                            {{ t($checkbox['label']) }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Rest of your form content goes here -->
                </div>



                {{-- Name Fields --}}
                @foreach (config('app.locales') as $locale)
                    <div class="col-md-6">
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">
                                {{ t('Name') }}
                                <small>({{ strtoupper($locale) }})</small>
                            </label>
                            <input type="text" name="name[{{ $locale }}]"
                                class="form-control form-control-solid mb-3 mb-lg-0 validate-required"
                                placeholder="{{ t('Enter name in ' . strtoupper($locale)) }}"
                                value="{{ old("name[$locale]", isset($name) && is_array($name) && array_key_exists($locale, $name) ? $name[$locale] : '') }}" />
                        </div>
                    </div>
                @endforeach


                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="required fw-semibold fs-6 mb-2">{{ t('Mobile') }}</label>
                        <input type="text" name="mobile" class="form-control form-control-solid validate-required"
                            placeholder="{{ t('Mobile number') }}"
                            value="{{ isset($_model) ? $_model->mobile : '' }}" />
                    </div>
                </div>

                <!-- Email and Mobile -->
                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="required fw-semibold fs-6 mb-2">{{ t('Email') }}</label>
                        <input type="email" name="email"
                            class="form-control form-control-solid validate-required validate-email"
                            placeholder="{{ t('example@domain.com') }}"
                            value="{{ isset($_model) ? $_model->email : '' }}" />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="required fw-semibold fs-6 mb-2">{{ t('Password') }}</label>
                        <input type="password" name="password" class="form-control form-control-solid"
                            placeholder="{{ t('Password') }}" />
                    </div>
                </div>



            </div>

            <div class="text-center pt-15">
                <button type="reset" class="btn btn-light me-3"
                    data-kt-modal-action="cancel{{ $_model::ui['s_ucf'] }}" data-bs-dismiss="modal">
                    {{ __('Discard') }}
                </button>
                <button type="submit" class="btn btn-primary"
                    data-kt-modal-action="submit_{{ $_model::ui['s_lcf'] }}">
                    <span class="indicator-label">{{ t('Submit') }}</span>
                    <span class="indicator-progress">
                        {{ t('Please wait...') }}
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
