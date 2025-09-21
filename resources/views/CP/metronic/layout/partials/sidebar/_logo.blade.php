<!--begin::Logo-->
<div class="app-sidebar-logo px-6" id="kt_app_sidebar_logo">
    <!--begin::Logo image-->
    <a href="{{ route('home') }}">
       <h4 class="text text-white">{{t(Config::get('app.name', 'default'))}}</h4>
    </a>
    <!--end::Logo image-->
    <div id="kt_app_sidebar_toggle"
        class="app-sidebar-toggle btn btn-icon btn-shadow btn-sm btn-color-muted btn-active-color-primary body-bg h-30px w-30px position-absolute top-50 start-100 translate-middle rotate"
        data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body"
        data-kt-toggle-name="app-sidebar-minimize">
        <i class="ki-duotone ki-double-left fs-2 rotate-180"><span class="path1"></span><span
                class="path2"></span></i>
    </div>
</div>
<!--end::Logo-->
