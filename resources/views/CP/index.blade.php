@extends('CP.metronic.index')

@section('subpageTitle', t('Dashboard'))

@section('content')
    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <!--begin::Col-->
        <div class="col-md-12 col-lg-12 col-xl-12 col-xxl-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="card-title">{{ t(config('app.name') . ' CRM') }} </h3>

                </div>
                <div class="card-body">

                    <!-- Stats Cards -->
                    <div class="row g-5 g-xl-8 mb-5">
                        <!-- Categories Card -->
                        <div class="col-xl-4">
                            <div class="card bg-light-primary card-xl-stretch mb-xl-8">
                                <div class="card-body my-3">
                                    <a href="#"
                                        class="card-title fw-bold text-primary fs-5 mb-3 d-block">{{ t('Categories') }}</a>
                                    <div class="py-1">
                                        <span class="text-dark fs-1 fw-bold me-2">{{ number_format($categoriesCount) }}</span>
                                        <span class="fw-semibold text-muted fs-7">{{ t('Total') }}</span>
                                    </div>
                                    <div class="py-1">
                                        <span
                                            class="text-success fs-1 fw-bold me-2">{{ number_format($activeCategoriesCount) }}</span>
                                        <span class="fw-semibold text-muted fs-7">{{ t('Active') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Products Card -->
                        <div class="col-xl-4">
                            <div class="card bg-light-info card-xl-stretch mb-xl-8">
                                <div class="card-body my-3">
                                    <a href="#"
                                        class="card-title fw-bold text-info fs-5 mb-3 d-block">{{ t('Products') }}</a>
                                    <div class="py-1">
                                        <span class="text-dark fs-1 fw-bold me-2">{{ number_format($productsCount) }}</span>
                                        <span class="fw-semibold text-muted fs-7">{{ t('Total') }}</span>
                                    </div>
                                    <div class="py-1">
                                        <span
                                            class="text-success fs-1 fw-bold me-2">{{ number_format($activeProductsCount) }}</span>
                                        <span class="fw-semibold text-muted fs-7">{{ t('Active') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Success Stories Card -->
                        <div class="col-xl-4">
                            <div class="card bg-light-warning card-xl-stretch mb-xl-8">
                                <div class="card-body my-3">
                                    <a href="#"
                                        class="card-title fw-bold text-info fs-5 mb-3 d-block">{{ t('Success Stories') }}</a>
                                    <div class="py-1">
                                        <span class="text-dark fs-1 fw-bold me-2">{{ number_format($sucessStoriesCount) }}</span>
                                        <span class="fw-semibold text-muted fs-7">{{ t('Total') }}</span>
                                    </div>
                                    <div class="py-1">
                                        <span
                                            class="text-success fs-1 fw-bold me-2">{{ number_format($activeSucessStoriesCount) }}</span>
                                        <span class="fw-semibold text-muted fs-7">{{ t('Active') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
