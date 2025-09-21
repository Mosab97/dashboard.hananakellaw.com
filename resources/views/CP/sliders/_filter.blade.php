<div class="me-3">
    <!--begin::Menu toggle-->
    <a href="#" class="btn btn-flex btn-secondary fw-bold" data-kt-menu-trigger="click"
        data-kt-menu-placement="bottom-end">
        <i class="ki-duotone ki-filter fs-6 text-muted me-1"><span class="path1"></span><span class="path2"></span></i>
        {{ __('Filter') }}
    </a>
    <!--end::Menu toggle-->

    <!--begin::Menu 1-->
    <div class="menu menu-sub menu-sub-dropdown w-250px w-md-800px" data-kt-menu="true" id="kt_menu_64ca1a18f399e">
        <!--begin::Header-->
        <div class="px-7 py-5">
            <div class="fs-5 text-dark fw-bold">{{ __('Filter Options') }}</div>
        </div>
        <!--end::Header-->

        <!--begin::Menu separator-->
        <div class="separator border-gray-200"></div>
        <!--end::Menu separator-->

        <!--begin::Form-->
        <form id="filter-form" class="px-7 py-5">
            <!--begin::Input group-->
            <div class="row">
                <!-- Member Filter -->
                <div class="col-md-3">
                    <div class="mb-10">
                        <!--begin::Label-->
                        <label class="form-label fw-semibold">{{ t('Member') }}:</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <div>
                            <select class="form-select form-select-solid datatable-input filter-selectpicker"
                                data-kt-select2="true" data-col-index="member_id" multiple
                                data-placeholder="Select option" data-dropdown-parent="#kt_menu_64ca1a18f399e"
                                data-allow-clear="true">
                                <option></option>
                                @foreach ($members_list ?? [] as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!--end::Input-->
                    </div>
                </div>

                <!-- User Type Filter -->
                <div class="col-md-3">
                    <div class="mb-10">
                        <!--begin::Label-->
                        <label class="form-label fw-semibold">{{ t('User Type') }}:</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <div>
                            <select class="form-select form-select-solid datatable-input filter-selectpicker"
                                data-kt-select2="true" data-col-index="user_type_id" multiple
                                data-placeholder="Select option" data-dropdown-parent="#kt_menu_64ca1a18f399e"
                                data-allow-clear="true">
                                <option></option>
                                @foreach ($user_type_list ?? [] as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!--end::Input-->
                    </div>
                </div>

                <!-- Duration Filter -->
                <div class="col-md-3">
                    <div class="mb-10">
                        <!--begin::Label-->
                        <label class="form-label fw-semibold">{{ t('Duration') }}:</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <div>
                            <select class="form-select form-select-solid datatable-input filter-selectpicker"
                                data-kt-select2="true" data-col-index="duration_id" multiple
                                data-placeholder="Select option" data-dropdown-parent="#kt_menu_64ca1a18f399e"
                                data-allow-clear="true">
                                <option></option>
                                @foreach ($duration_list ?? [] as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!--end::Input-->
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="col-md-3">
                    <div class="mb-10">
                        <!--begin::Label-->
                        <label class="form-label fw-semibold">{{ t('Status') }}:</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <div>
                            <select class="form-select form-select-solid datatable-input filter-selectpicker"
                                data-kt-select2="true" data-col-index="status_id" multiple
                                data-placeholder="Select option" data-dropdown-parent="#kt_menu_64ca1a18f399e"
                                data-allow-clear="true">
                                <option></option>
                                @foreach ($status_list ?? [] as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!--end::Input-->
                    </div>
                </div>

                <!-- Payment Method Filter -->
                <div class="col-md-3">
                    <div class="mb-10">
                        <!--begin::Label-->
                        <label class="form-label fw-semibold">{{ t('Payment Method') }}:</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <div>
                            <select class="form-select form-select-solid datatable-input filter-selectpicker"
                                data-kt-select2="true" data-col-index="payment_method_id" multiple
                                data-placeholder="Select option" data-dropdown-parent="#kt_menu_64ca1a18f399e"
                                data-allow-clear="true">
                                <option></option>
                                @foreach ($payment_method_list ?? [] as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!--end::Input-->
                    </div>
                </div>

                <!-- Is Paid Filter -->
                <div class="col-md-3">
                    <div class="mb-10">
                        <!--begin::Label-->
                        <label class="form-label fw-semibold">{{ t('Paid Status') }}:</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <div>
                            <select class="form-select form-select-solid datatable-input filter-selectpicker"
                                data-kt-select2="true" data-col-index="is_paid" multiple
                                data-placeholder="Select option" data-dropdown-parent="#kt_menu_64ca1a18f399e"
                                data-allow-clear="true">
                                <option></option>
                                <option value="1">{{ t('Paid') }}</option>
                                <option value="0">{{ t('Unpaid') }}</option>
                            </select>
                        </div>
                        <!--end::Input-->
                    </div>
                </div>

                <!-- Is Received Filter -->
                <div class="col-md-3">
                    <div class="mb-10">
                        <!--begin::Label-->
                        <label class="form-label fw-semibold">{{ t('Payment Received') }}:</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <div>
                            <select class="form-select form-select-solid datatable-input filter-selectpicker"
                                data-kt-select2="true" data-col-index="is_received" multiple
                                data-placeholder="Select option" data-dropdown-parent="#kt_menu_64ca1a18f399e"
                                data-allow-clear="true">
                                <option></option>
                                <option value="1">{{ t('Received') }}</option>
                                <option value="0">{{ t('Not Received') }}</option>
                            </select>
                        </div>
                        <!--end::Input-->
                    </div>
                </div>

                <!-- Price Range Filter -->
                <div class="col-md-3">
                    <div class="mb-10">
                        <!--begin::Label-->
                        <label class="form-label fw-semibold">{{ t('Price Range') }}:</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <div class="d-flex gap-2">
                            <input type="number" class="form-control form-control-solid datatable-input"
                                data-col-index="price_range_min" placeholder="{{ t('Min') }}" />
                            <input type="number" class="form-control form-control-solid datatable-input"
                                data-col-index="price_range_max" placeholder="{{ t('Max') }}" />
                        </div>
                        <!--end::Input-->
                    </div>
                </div>

                <!-- Creation Date Range Filter -->
                <div class="col-md-3">
                    <div class="mb-10">
                        <!--begin::Label-->
                        <label class="form-label fw-semibold">{{ t('Creation Date') }}:</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <div>
                            <input class="form-control form-control-solid datatable-input"
                                data-col-index="date_range"
                                placeholder="Pick date range"
                                id="kt_subscription_creation_date_range"/>
                        </div>
                        <!--end::Input-->
                    </div>
                </div>

                <!-- Subscription Date Range Filter -->
                <div class="col-md-3">
                    <div class="mb-10">
                        <!--begin::Label-->
                        <label class="form-label fw-semibold">{{ t('Subscription Period') }}:</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <div>
                            <input class="form-control form-control-solid datatable-input"
                                data-col-index="subscription_date_range"
                                placeholder="Pick date range"
                                id="kt_subscription_period_date_range"/>
                        </div>
                        <!--end::Input-->
                    </div>
                </div>
            </div>
            <!--begin::Actions-->

            <div class="d-flex justify-content-end">
                <button type="reset" id="resetFilterBtn" class="btn btn-sm btn-light btn-active-light-primary me-2"
                    data-kt-menu-dismiss="true">{{ t('Reset') }}
                </button>

                <button type="submit" id="filterBtn" class="btn btn-sm btn-primary"
                    data-kt-menu-dismiss="true">{{ t('Apply') }}
                </button>
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
            // Initialize date range pickers
            $("#kt_subscription_creation_date_range, #kt_subscription_period_date_range").flatpickr({
                altInput: true,
                altFormat: "Y-m-d",
                dateFormat: "Y-m-d",
                mode: "range",
                static: true
            });

            // Handle price range
            $('#filter-form').on('submit', function() {
                const min = $('[data-col-index="price_range_min"]').val();
                const max = $('[data-col-index="price_range_max"]').val();

                if (min || max) {
                    // Store as a JSON object for the filter service
                    $('#filter_params').data('price_range', JSON.stringify({
                        min: min || null,
                        max: max || null
                    }));
                }
            });
        });
    </script>
@endpush
