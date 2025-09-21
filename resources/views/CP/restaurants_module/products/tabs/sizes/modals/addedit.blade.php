<div class="modal-content">
    <div class="modal-header">
        <h2 class="fw-bold">{{ $_model->exists ? t('Edit ' . $config['singular_name']) : t('Add ' . $config['singular_name']) }}
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
            action="{{ route($config['full_route_name'] . '.addedit', ['product' => $product->id]) }}">
            @if (isset($_model))
                <input type="hidden" name="{{ $config['id_field'] }}" value="{{ $_model->id }}">
            @endif
            <div class="d-flex flex-column scroll-y me-n7 pe-7">
                <div class="row">
                    {{-- Supervisor Selection --}}
                    <div class="col-md-12">
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">{{ t('Size') }}</label>
                            <select name="size_id" class="form-select form-select-solid mb-3 mb-lg-0 validate-required"
                                data-control="select2" data-dropdown-parent="#kt_modal_general">
                                <option value="">{{ t('Select Size') }}</option>
                                @foreach ($sizes_list??[] as $size)
                                    <option value="{{ $size->id }}"
                                        {{ isset($_model) && $_model->size_id == $size->id ? 'selected' : '' }}>
                                        {{ $size->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- price Field --}}
                    <div class="col-md-6">
                        <div class="fv-row mb-7">
                            <label class="fw-semibold fs-6 mb-2">{{ t('Price') }}</label>
                            <input type="number" name="price" class="form-control form-control-solid"
                                value="{{ old('price', isset($_model) ? $_model->price : '') }}" />
                        </div>
                    </div>

                    {{-- Order Field --}}
                    <div class="col-md-6">
                        <div class="fv-row mb-7">
                            <label class="fw-semibold fs-6 mb-2">{{ t('Order') }}</label>
                            <input type="number" name="order" class="form-control form-control-solid"
                                value="{{ old('order', isset($_model) ? $_model->order : '') }}" />
                        </div>
                    </div>
                    {{-- active Field --}}
                    <div class="col-md-6">
                        <div class="fv-row mb-7">
                            <div class="form-check form-switch form-check-custom form-check-solid mt-5">
                                <input class="form-check-input" type="checkbox" name="active" value="1"
                                        {{ isset($_model) && $_model->active ? 'checked' : '' }}/>
                                <label class="form-check-label fw-semibold fs-6" for="active">
                                    {{ t('Active') }}
                                </label>
                            </div>
                        </div>
                    </div>



                </div>
            </div>

            <div class="text-center pt-15">
                <button type="button" class="btn btn-light me-3"
                    data-bs-dismiss="modal">{{ t('Discard') }}</button>
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
