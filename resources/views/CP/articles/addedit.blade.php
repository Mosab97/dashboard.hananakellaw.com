@extends('CP.metronic.index')

@section('subpageTitle', t($config['plural_name']))
@section('subpageTitleLink', route($config['full_route_name'] . '.index'))

@section('title', t($config['singular_name'] . ($_model->exists ? ' Edit ' : '- Add new ') . $config['singular_name']))
@section('subpageTitle', t($config['singular_name']))
@section('subpageName', t(($_model->exists ? ' Edit ' : '- Add new ') . $config['singular_name']))

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
                            {{ t($config['singular_name']) }}
                        </a>
                    </li>
                    <li class="nav-item mt-2">
                        <a class="nav-link text-active-primary ms-0 me-6 px-2 py-5 " data-bs-toggle="tab"
                            data-bs-target="#kt_tab_article_contents" href="#kt_tab_article_contents">
                            <span class="svg-icon svg-icon-2 me-2"></span>
                            {{ t($config['children']['article_contents']['singular_name']) }}
                        </a>
                    </li>
                </ul>
                <!--end::Navs-->
            </div>
        </div>
    </div>

    <form action="{{ route($config['full_route_name'] . '.addedit', ['Id' => $_model->id ?? null]) }}" method="POST"
        id="kt_add_edit_form" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="{{ $config['id_field'] }}" value="{{ $_model->id ?? '' }}">

        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="kt_tab_pane_1" role="tabpanel">
                @include($config['view_path'] . 'tabs.form')
            </div>
            @if ($_model->exists)
                <div class="tab-pane fade " id="kt_tab_article_contents" role="tabpanel">
                    @include('CP.articles.tabs.article_contents.index')
                </div>
            @endif
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

    @include($config['view_path'] . 'scripts.addeditJS')
@endsection
