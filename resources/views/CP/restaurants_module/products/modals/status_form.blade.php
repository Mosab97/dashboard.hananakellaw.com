{{-- price-offers/status_form.blade.php --}}
<div class="modal-content">
    <div class="modal-header" id="kt_modal_change_status_header">
        <h2 class="fw-bold">{{ $title }}</h2>
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

    <div class="modal-body scroll-y mx-5 my-7">
        <form id="kt_modal_change_status_form" class="form"
            data-editMode="{{ isset($_model) ? 'enabled' : 'disabled' }}"
            action="{{ route($_model::ui['route'] . '.update_status', ['_model' => $_model->id]) }}">
            @csrf

            <div class="row">
                <div class="col-md-12">
                    <div class="fv-row mb-4">
                        <label class="fw-semibold fs-6 mb-2">{{ t('Status') }}</label>
                        <select class="form-select form-select-solid validate-required" name="status_id" id="status"
                            style="width: 100%" data-control="select2" data-dropdown-parent="#kt_modal_general_sm"
                            data-allow-clear="true" data-placeholder="{{ t('Select Status') }}">
                            <option></option>
                            @foreach ($lead_status_list ?? [] as $status)
                                <option value="{{ $status->id }}" @selected(isset($_model) && $_model->status_id == $status->id)>
                                    {{ $status->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="text-center pt-15">
                <button type="reset" class="btn btn-light me-3" data-kt-change-status-modal-action="cancel"
                    data-bs-dismiss="modal">
                    {{ t('Discard') }}
                </button>
                <button type="submit" class="btn btn-primary" data-kt-change-status-modal-action="submit">
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
