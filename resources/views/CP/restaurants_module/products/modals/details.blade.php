<!--Program Details Modal-->
<div class="modal-content">
    <div class="modal-header">
        <h2 class="fw-bold">{{ t('Program Details') }} - {{ $_model->name }}</h2>
        <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
            <span class="svg-icon svg-icon-1">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor"/>
                    <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor"/>
                </svg>
            </span>
        </div>
    </div>
{{-- @dd() --}}
    <div class="modal-body hover-scroll-overlay-y px-10 py-8" style="max-height: 80vh;">
        <div class="d-flex flex-column scroll-y me-n7 pe-7">
            <!--Program Overview Card-->
            <div class="card mb-7">
                <div class="card-body p-9">
                    <!--Header with Photo-->
                    <div class="d-flex flex-wrap mb-6">
                        <div class="me-7 mb-4">
                            <div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative">
                                <img src="{{ asset($_model->photo_attachment->first()->file_path) }}" alt="Program Photo" class="w-100"/>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                                <div class="d-flex flex-column">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="text-gray-800 fs-2 fw-bold me-1">{{ $_model->name }}</span>
                                        <span class="badge badge-light-{{ $_model->deadline_status === 'expired' ? 'danger' : 'success' }} fs-base">
                                            {{ t($_model->deadline_status) }}
                                        </span>
                                    </div>
                                    <div class="d-flex flex-wrap fw-semibold fs-6 mb-4 pe-2">
                                        <span class="d-flex align-items-center text-gray-400 me-5 mb-2">
                                            <i class="ki-duotone ki-dollar fs-4 me-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            {{ number_format($_model->fund, 2) }}
                                        </span>
                                        @if($_model->deadline)
                                        <span class="d-flex align-items-center text-gray-400 mb-2">
                                            <i class="ki-duotone ki-calendar fs-4 me-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            {{ t('Deadline') }}: {{ $_model->deadline->format('d M Y') }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--Program Description-->
                    <div class="fs-5 fw-semibold text-gray-600 mb-6">
                        {{ $_model->description }}
                    </div>

                    <!--Categories & Target-->
                    <div class="d-flex flex-stack">
                        <div class="d-flex">
                            @if($_model->categories->count() > 0)
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6">
                                <div class="fs-6 text-gray-800 fw-bold">{{ t('Categories') }}</div>
                                <div class="fw-semibold text-gray-600">
                                    @foreach($_model->categories as $category)
                                    <span class="badge badge-light-primary me-2">{{ $category->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            @if($_model->target_applicant)
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4">
                                <div class="fs-6 text-gray-800 fw-bold">{{ t('Target Applicant') }}</div>
                                <div class="fw-semibold text-gray-600">
                                    {{ $_model->target_applicant->name }}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!--Important Dates-->
            @if($_model->important_dates->count() > 0)
            <div class="card mb-7">
                <div class="card-body p-9">
                    <h3 class="card-title mb-6">{{ t('Important Dates') }}</h3>
                    <div class="timeline">
                        @foreach($_model->important_dates as $date)
                        <div class="timeline-item">
                            <div class="timeline-line w-40px"></div>
                            <div class="timeline-icon symbol symbol-circle symbol-40px me-4">
                                <div class="symbol-label bg-light">
                                    <i class="ki-duotone ki-calendar-8 fs-2 text-gray-500">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                    </i>
                                </div>
                            </div>
                            <div class="timeline-content mb-10 mt-n1">
                                <div class="pe-3 mb-5">
                                    <div class="fs-5 fw-semibold mb-2">{{ $date->title }}</div>
                                    <div class="d-flex align-items-center mt-1 fs-6">
                                        <div class="text-muted me-2 fs-7">{{ $date->date->format('d M Y') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!--Eligibility & Facilities-->
            <div class="row g-5 g-xl-8">
                @if($_model->eligibilities->count() > 0)
                <div class="col-xl-6">
                    <div class="card card-xl-stretch mb-xl-8">
                        <div class="card-body p-9">
                            <h3 class="card-title mb-6">{{ t('Eligibility Criteria') }}</h3>
                            <div class="d-flex flex-column">
                                @foreach($_model->eligibilities as $eligibility)
                                <div class="d-flex align-items-center mb-3">
                                    <span class="bullet bullet-dot bg-success me-3"></span>
                                    <div class="flex-grow-1">
                                        <span class="text-gray-700 text-hover-primary fs-6 fw-semibold">
                                            {{ $eligibility->name }}
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if($_model->facilities->count() > 0)
                <div class="col-xl-6">
                    <div class="card card-xl-stretch mb-xl-8">
                        <div class="card-body p-9">
                            <h3 class="card-title mb-6">{{ t('Facilities') }}</h3>
                            <div class="d-flex flex-column">
                                @foreach($_model->facilities as $facility)
                                <div class="d-flex align-items-center mb-3">
                                    <span class="bullet bullet-dot bg-primary me-3"></span>
                                    <div class="flex-grow-1">
                                        <span class="text-gray-700 text-hover-primary fs-6 fw-semibold">
                                            {{ $facility->name }}
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!--How to Apply-->
            <div class="card mb-7">
                <div class="card-body p-9">
                    <h3 class="card-title mb-6">{{ t('How to Apply') }}</h3>
                    <div class="fs-5 fw-semibold text-gray-600">
                        {{ $_model->how_to_apply }}
                    </div>
                </div>
            </div>
        </div>

        <!--Modal Footer-->
        <div class="text-center pt-15">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ t('Close') }}</button>
        </div>
    </div>
</div>
