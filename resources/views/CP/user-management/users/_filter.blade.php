<!--begin::Filter menu-->
<div class="me-3">
    <!--begin::Menu toggle-->
    <a href="#" class="btn btn-flex btn-secondary fw-bold" data-kt-menu-trigger="click"
        data-kt-menu-placement="bottom-end">
        <i class="ki-duotone ki-filter fs-6 text-muted me-1"><span class="path1"></span><span class="path2"></span></i>
        {{ t('Filter') }}
    </a>
    <!--end::Menu toggle-->



    <!--begin::Menu 1-->
    <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_64ca1a18f399e">
        <!--begin::Header-->
        <div class="px-7 py-5">
            <div class="fs-5 text-dark fw-bold">{{ t('Filter Options') }}</div>
        </div>
        <!--end::Header-->

        <!--begin::Menu separator-->
        <div class="separator border-gray-200"></div>
        <!--end::Menu separator-->


        <!--begin::Form-->
        <form id="filter-form" class="px-7 py-5">
            <!--begin::Input group-->

            <!--begin::Input group-->
            {{-- <div class="mb-10">
                <!--begin::Label-->
                <label class="form-label fw-semibold">{{ t('Branch:') }}</label>
                <!--end::Label-->
                <!--begin::Input-->
                <div>
                    <select class="form-select form-select-solid datatable-input filter-selectpicker" multiple
                        data-kt-select2="true" data-col-index="branch_id" data-close-on-select="false"
                        data-placeholder="Select option" data-dropdown-parent="#kt_menu_64ca1a18f399e"
                        data-allow-clear="true">
                        <option></option>
                        @foreach ($ALHAYAT_BRANCHES ?? [] as $ALHAYAT_BRANCHE)
                            <option value="{{ $ALHAYAT_BRANCHE->id }}">
                                {{ $ALHAYAT_BRANCHE->name }}</option>
                        @endforeach
                    </select>
                </div>
                <!--end::Input-->
            </div> --}}
            <div class="mb-10">
                <!--begin::Label-->
                <label class="form-label fw-semibold">{{ t('Role:') }}</label>
                <!--end::Label-->
                <!--begin::Input-->
                <div>
                    <select class="form-select form-select-solid datatable-input filter-selectpicker"
                        data-kt-select2="true" data-col-index="role_id" multiple data-placeholder="Select option"
                        data-dropdown-parent="#kt_menu_64ca1a18f399e" data-allow-clear="true">
                        <option></option>
                        @foreach ($roles ?? [] as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <!--end::Input-->
            </div>
            <div class="mb-10 ">
                <!--begin::Label-->
                <label class="form-label fw-semibold">{{ t('Created Date:') }}</label>
                <!--end::Label-->
                <!--begin::Input-->
                <div class="">
                    <input class="form-control form-control-solid datatable-input " data-col-index="created_at"
                        data-bs-focus="false" placeholder="Pick date" id="kt_datepicker_7" />
                </div>
                <!--end::Input-->
            </div>
            <div class="mb-10">
                <!--begin::Label-->
                <label class="form-label fw-semibold">{{ t('Is Active:') }}</label>
                <!--end::Label-->
                <!--begin::Input-->
                <div>
                    <select class="form-select form-select-solid datatable-input filter-selectpicker"
                        data-kt-select2="true" data-col-index="is_active" data-placeholder="Select option"
                        data-dropdown-parent="#kt_menu_64ca1a18f399e" data-allow-clear="true">
                        <option></option>
                        <option value="1">{{ t('Active') }}</option>
                        <option value="0">{{ T('Disabled') }}</option>

                    </select>
                </div>
                <!--end::Input-->
            </div>
            <!--end::Input group-->

            <!--end::Input group-->

            <!--begin::Actions-->
            <div class="d-flex justify-content-end">
                <button type="reset" id="resetFilterBtn" class="btn btn-sm btn-light btn-active-light-primary me-2"
                    data-kt-menu-dismiss="true">{{ t('Reset') }}</button>

                <button type="submit" id="filterBtn" class="btn btn-sm btn-primary"
                    data-kt-menu-dismiss="true">{{ t('Apply') }}</button>
            </div>
            <!--end::Actions-->
        </form>
        <!--end::Form-->
    </div>
    <!--end::Menu 1-->
</div>

@push('scripts')
    <script>
        $(function() {
            $("#kt_datepicker_7").flatpickr({
                altInput: true,
                altFormat: "Y-m-d",
                dateFormat: "Y-m-d",
                mode: "range",
                static: true
            });
        });

    </script>
@endpush
