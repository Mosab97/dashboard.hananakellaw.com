<!--begin::sidebar menu-->
<div class="app-sidebar-menu overflow-hidden flex-column-fluid">
    <!--begin::Menu wrapper-->
    <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper hover-scroll-overlay-y my-5" data-kt-scroll="true"
        data-kt-scroll-activate="true" data-kt-scroll-height="auto"
        data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer"
        data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px" data-kt-scroll-save-state="true">
        <!--begin::Menu-->
        <div class="menu menu-column menu-rounded menu-sub-indention px-3" id="#kt_app_sidebar_menu" data-kt-menu="true"
            data-kt-menu-expand="false">
            <div class="input-group px-4">
                <span class="svg-icon svg-icon-2 mt-2">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M21.7 18.9L18.6 15.8C17.9 16.9 16.9 17.9 15.8 18.6L18.9 21.7C19.3 22.1 19.9 22.1 20.3 21.7L21.7 20.3C22.1 19.9 22.1 19.3 21.7 18.9Z"
                            fill="currentColor" />
                        <path opacity="0.3"
                            d="M11 20C6 20 2 16 2 11C2 6 6 2 11 2C16 2 20 6 20 11C20 16 16 20 11 20ZM11 4C7.1 4 4 7.1 4 11C4 14.9 7.1 18 11 18C14.9 18 18 14.9 18 11C18 7.1 14.9 4 11 4ZM8 11C8 9.3 9.3 8 11 8C11.6 8 12 7.6 12 7C12 6.4 11.6 6 11 6C8.2 6 6 8.2 6 11C6 11.6 6.4 12 7 12C7.6 12 8 11.6 8 11Z"
                            fill="currentColor" />
                    </svg>
                </span>
                <input type="text" id="menu-filter" style="color: #F9F9F9 !important;"
                    class="form-control form-control-flush form-control-sm text-gray-100" placeholder="{{__('Search Menu')}}"
                    aria-label="Recipient's username" aria-describedby="basic-addon2" />
            </div>
            <div class="opacity-20 separator separator-dashed mt-2 mb-3"></div>
            <div class="menu-noResult px-6 py-3 d-none">
                <!--begin:Menu content-->
                <span class="menu-heading fw-bold text-uppercase fs-7">No Results found</span>
                <!--end:Menu content-->
            </div>
            {{-- <input type="text" id="menu-filter" class="form-control form-control-flush form-control-sm" placeholder="Search..."> --}}
            <!--begin:Menu item-->
            <!--begin:Menu item-->
            {{-- App\Providers\ViewServiceProvider --}}
            @foreach ($_menu as $menuItem)
                @if ($menuItem->children->count() > 0)
                    @canany($menuItem->children->pluck('permission_name')->flatten(1)->unique()->toArray())
                        <div data-kt-menu-trigger="click"
                            class="menu-item menu-accordion {{ in_array(request()->route()->getName(),$menuItem->children->pluck('route_list')->flatten(1)->unique()->toArray())? 'here show': '' }}">
                            <span class="menu-link">
                                <span class="menu-icon">
                                    <span class="svg-icon svg-icon-2">
                                        {!! $menuItem->icon_svg !!}
                                    </span>
                                    <!--end::Svg Icon-->
                                </span>
                                <span class="menu-title">

                                    @if (app()->getLocale() == 'en')
                                        {{ $menuItem->name_en }}
                                    @elseif (app()->getLocale() == 'ar')
                                        {{ $menuItem->name }}
                                    @elseif (app()->getLocale() == 'he')
                                        {{ $menuItem->name_he }}
                                    @endif
                                </span>
                                <span class="menu-arrow"></span>
                            </span>
                            <!--end:Menu link-->

                            @foreach ($menuItem->children as $menuSubItem)
                                @canany($menuSubItem->permission_name)
                                    <div class="menu-sub menu-sub-accordion">
                                        <!--begin:Menu item-->
                                        @php
                                            $routeNames = $menuSubItem->route_list;
                                            $matchedRouteName = $menuSubItem->route;
                                            if (count($menuSubItem->route_list) > 1) {
                                                $matchedRouteName = collect($routeNames)->first(function ($routeName) {
                                                    return Str::is(
                                                        $routeName,
                                                        request()
                                                            ->route()
                                                            ->getName(),
                                                    );
                                                }, $routeNames[0]);
                                            }
                                        @endphp
                                        <div class="menu-item">
                                            <a class="menu-link {{ request()->routeIs($matchedRouteName) ? 'active' : '' }}"
                                                href="{{ route($routeNames[0]) }}">
                                                <span class="menu-bullet">
                                                    <span class="bullet bullet-dot"></span>
                                                </span>
                                                <span class="menu-title">

                                                    @if (app()->getLocale() == 'en')
                                                        {{ $menuSubItem->name_en }}
                                                    @elseif (app()->getLocale() == 'ar')
                                                        {{ $menuSubItem->name }}
                                                    @elseif (app()->getLocale() == 'he')
                                                        {{ $menuSubItem->name_he }}
                                                    @endif
                                                </span>
                                            </a>
                                            <!--end:Menu link-->
                                        </div>
                                    </div>
                                @endcanany
                            @endforeach
                            <!--begin:Menu sub-->
                        </div>
                    @endcanany
                @else
                    @if ($menuItem->route == 'home')
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs($menuItem->route) ? 'active' : '' }}"
                                href="{{ route($menuItem->route) }}"><span class="menu-icon">
                                    <!--begin::Svg Icon | path: icons/duotune/general/gen014.svg-->
                                    <span class="svg-icon svg-icon-2">
                                        {!! $menuItem->icon_svg !!}
                                    </span>
                                    <!--end::Svg Icon-->
                                </span><span class="menu-title">
                                    @if (app()->getLocale() == 'he')
                                        {{ $menuItem->name_he }}
                                    @elseif (app()->getLocale() == 'ar')
                                        {{ $menuItem->name }}
                                    @else
                                        {{ $menuItem->name_en }}
                                    @endif

                                    {{-- {{ __('menu.dashboard') }} --}}
                                </span>
                            </a>
                        </div>
                    @else
                        @canany($menuItem->permission_name)
                            @php
                                $routeNames = $menuItem->route_list;
                                $matchedRouteName = $menuItem->route;
                                if (count($menuItem->route_list) > 1) {
                                    $matchedRouteName = collect($routeNames)->first(function ($routeName) {
                                        return Str::is(
                                            $routeName,
                                            request()
                                                ->route()
                                                ->getName(),
                                        );
                                    }, $routeNames[0]);
                                }
                            @endphp
                            <div class="menu-item">
                                <a class="menu-link {{ request()->routeIs($matchedRouteName) ? 'active' : '' }}"
                                    href="{{ route($routeNames[0]) }}"><span class="menu-icon">
                                        <!--begin::Svg Icon | path: icons/duotune/general/gen014.svg-->
                                        <span class="svg-icon svg-icon-2">
                                            {!! $menuItem->icon_svg !!}
                                        </span>
                                        <!--end::Svg Icon-->
                                    </span><span class="menu-title">
                                        @if (app()->getLocale() == 'he')
                                            {{ $menuItem->name_he }}
                                        @elseif (app()->getLocale() == 'ar')
                                            {{ $menuItem->name }}
                                        @else
                                            {{ $menuItem->name_en }}
                                        @endif
                                        {{-- {{ __('menu.dashboard') }} --}}
                                    </span>
                                </a>
                            </div>
                        @endcanany
                    @endif
                @endif
            @endforeach



            <!--end:Menu item-->

        </div>
        <!--end::Menu-->
    </div>
    <!--end::Menu wrapper-->
</div>
<!--end::sidebar menu-->
