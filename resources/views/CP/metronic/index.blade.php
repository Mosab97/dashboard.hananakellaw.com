
<!DOCTYPE html>

<html direction="{{ direction() }}" dir="{{ direction() }}" style="direction: {{ direction() }}"
    lang="{{ lang() }}">
<!--begin::Head-->

<head>
    {{-- <base href="" /> --}}
    <title>{{ __(config('app.name')) }} - @yield('title', 'Home')</title>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ config('app.name') }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" href="{{ asset('media/logos/logo-opts-light-small.png') }}" />
    {{-- <link rel="shortcut icon" href="{{ asset('media/logos/favicon.ico') }}" /> --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!--begin::Vendor Stylesheets(used for this page only)-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;500&family=Tajawal:wght@300;500&display=swap"
        rel="stylesheet">
    @if (lang() == 'he')
        <!--begin::Fonts(mandatory for all pages)-->
        <!--end::Fonts-->
        <link href="{{ asset('plugins/custom/datatables/datatables.bundle.css?v=1') }}" rel="stylesheet"
            type="text/css" />
        <!--end::Vendor Stylesheets-->
        <!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
        <link href="{{ asset('plugins/global/plugins.bundle.css?v=1') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('css/style.bundle.css?v=1') }}" rel="stylesheet" type="text/css" />
    @elseif (lang() == 'ar')
        <!--begin::Fonts(mandatory for all pages)-->
        <!--end::Fonts-->
        <link href="{{ asset('plugins/custom/datatables/datatables.bundle.rtl.css?v=1') }}" rel="stylesheet"
            type="text/css" />
        <!--end::Vendor Stylesheets-->
        <!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
        <link href="{{ asset('plugins/global/plugins.bundle.rtl.css?v=1') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('css/style.bundle.rtl.css?v=1') }}" rel="stylesheet" type="text/css" />
    @endif
    @if (lang() == 'he')
        <link href="{{ asset('css/custom.css?v=1') }}" rel="stylesheet" type="text/css" />
    @endif

    @stack('styles')
    @if (lang() == 'ar')
        <style>
            .select2-container .select2-selection__clear {
                right: 5px !important;
            }

            .select2-container--bootstrap5 .select2-dropdown .select2-results__option.select2-results__option--selected::after {
                left: 1.25rem !important;
                right: auto;
                !important;
            }
        </style>
        <style>
            table thead tr th {
                text-align: center !important;
                font-weight: bolder;
                font-size: 14px !important;
            }

            table thead tr {
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                /* Shadow effect */
            }
        </style>
        <style>
            @media (min-width: 992px) {
                [data-kt-app-sidebar-minimize=on][data-kt-app-sidebar-hoverable=true] .app-sidebar:not(:hover) .app-sidebar-menu input {
                    opacity: 0;
                    transition: opacity 0.3s ease !important;
                }
            }
        </style>
    @endif
    <style>
        .form-field-card {
            border: 1px solid #eee;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            background: white;
        }

        .form-field-card:hover {
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .field-type-btn {
            width: 100%;
            margin-bottom: 10px;
            text-align: left;
        }

        .drag-handle {
            cursor: move;
            color: #a1a5b7;
        }
    </style>
    <!--end::Global Stylesheets Bundle-->
</head>
<!--end::Head-->
<!--begin::Body-->

<body id="kt_app_body" data-kt-app-page-loading-enabled="true" data-kt-app-page-loading="on"
    data-kt-app-layout="dark-sidebar" data-kt-app-header-fixed="true" data-kt-app-sidebar-enabled="true"
    data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-hoverable="true" data-kt-app-sidebar-push-header="true"
    data-kt-app-sidebar-push-toolbar="true" data-kt-app-sidebar-push-footer="true" class="app-default">
    <!--layout-partial:partials/theme-mode/_init.html-->
    @include('CP.metronic.partials.theme-mode._init')
    <!--layout-partial:layout/partials/_page-loader.html-->
    @include('CP.metronic.layout.partials._page-loader')
    <!--layout-partial:layout/_default.html-->
    @include('CP.metronic.layout._default')
    @include('CP.metronic.partials._scrolltop')
    @include('CP.metronic.layout.partials._chat')
    <div class="modal fade" id="kt_modal_general" tabindex="-1" aria-hidden="true">
        <!--begin::Modal dialog-->
        <div class="modal-dialog modal-lg modal-dialog-centered">

        </div>
        <!--end::Modal dialog-->
    </div>
    <!--begin::User modal-->
    <div class="modal fade" id="kt_modal_general_m" tabindex="-1" aria-hidden="true">
        <!--begin::Modal dialog-->
        <div class="modal-dialog modal-dialog-centered mw-650px">

        </div>
        <!--end::Modal dialog-->
    </div>
    <!--end::User modal-->
    <!-- Status Change Modal -->
    <div class="modal fade" id="kt_modal_general_sm" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <!-- Modal content will be loaded here via AJAX -->
            </div>
        </div>
    </div>
    <div class="modal fade" id="kt_modal_add_attachment" tabindex="-1" aria-hidden="true">
        <!--begin::Modal dialog-->
        <div class="modal-dialog modal-lg modal-dialog-centered">

        </div>
        <!--end::Modal dialog-->
    </div>

    <!--layout-partial:partials/_scrolltop.html-->
    <!--begin::Modals-->
    <!--layout-partial:partials/modals/_upgrade-plan.html-->
    <!--layout-partial:partials/modals/_view-users.html-->
    <!--layout-partial:partials/modals/users-search/_main.html-->
    <!--layout-partial:partials/modals/_invite-friends.html-->
    <!--end::Modals-->
    <!--begin::Javascript-->
    <!--begin::Global Javascript Bundle(mandatory for all pages)-->

    <script src="{{ asset('plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('js/scripts.bundle.js') }}"></script>
    <!--end::Global Javascript Bundle-->
    <!--begin::Vendors Javascript(used for this page only)-->
    {{-- <script src="{{ asset('plugins/custom/fullcalendar/fullcalendar.bundle.js') }}"></script>
    <script src="https://cdn.amcharts.com/lib/5/index.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/percent.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/radar.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/map.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/geodata/worldLow.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/geodata/continentsLow.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/geodata/usaLow.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/geodata/worldTimeZonesLow.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/geodata/worldTimeZoneAreasLow.js"></script> --}}
    <script src="{{ asset('plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <!--end::Vendors Javascript-->
    <!--begin::Custom Javascript(used for this page only)-->
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>


    <script>
        function debounce_menu(cb, interval, immediate) {
            var timeout;

            return function() {
                var context = this,
                    args = arguments;
                var later = function() {
                    timeout = null;
                    if (!immediate) cb.apply(context, args);
                };

                var callNow = immediate && !timeout;

                clearTimeout(timeout);
                timeout = setTimeout(later, interval);

                if (callNow) cb.apply(context, args);
            };
        };

        function MenukeyPressCallback() {
            var searchTerm = $('#menu-filter').val().toLowerCase();
            var noResultsElement = $('.menu-noResult');
            // noResultsElement.addClass('d-none');
            var hasVisibleItems = false;

            $('.app-sidebar-menu .menu-item').each(function() {
                var menuItem = $(this);
                var menuItemText = menuItem.find('.menu-title').text().toLowerCase();
                var isAccordion = menuItem.hasClass('menu-accordion');
                var isHere = menuItem.hasClass('here');
                if (searchTerm === '') {
                    // Clear input, show all menu items
                    menuItem.show();
                    // if (isAccordion && !menuItem.hasClass('show')) {
                    //     menuItem.removeClass('show');
                    // }
                    hasVisibleItems = true;
                    if (isHere) {
                        menuItem.addClass('show');
                        // hasVisibleItems = true;
                    } else {
                        menuItem.removeClass('show');
                    }
                } else if (menuItemText.indexOf(searchTerm) === -1) {
                    // Hide menu item
                    menuItem.hide();
                } else {
                    // Show menu item
                    menuItem.show();
                    hasVisibleItems = true;
                    // Add "show" class to menu-accordion items if not already present
                    if (isAccordion && !menuItem.hasClass('show')) {
                        menuItem.addClass('show');
                    }
                }
            });
            // Show or hide the "No results found" element
            if (!hasVisibleItems) {
                noResultsElement.removeClass('d-none');
            } else {
                noResultsElement.addClass('d-none');
            }
        }

        $(function() {
            const filterSearchMenu = document.querySelector('#menu-filter');
            filterSearchMenu.onkeydown = debounce_menu(MenukeyPressCallback, 300);
            // Filter the menu items as you type
            // $('#menu-filter').on('input', function() {
            //     var searchTerm = $(this).val().toLowerCase();

            // });
        });
    </script>
    <script>
        $(function() {
            $(document).on('show.bs.modal', '.modal', function() {
                const zIndex = 1040 + 10 * $('.modal:visible').length;
                $(this).css('z-index', zIndex);
                setTimeout(() => $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1)
                    .addClass('modal-stack'));
            });
        });
    </script>



    <script>
        function createDataTable(tableSelector, columnDefs, ajaxUrl, order, filterSelector =
            '.datatable-input', additionalParams = {}, ) {
            var table = document.querySelector(tableSelector);
            if (!table) {
                console.error(`Table with selector ${tableSelector} not found.`);
                return;
            }

            // Initialize DataTable with modified configuration
            var datatable = $(table).DataTable({
                responsive: {
                    details: {
                        type: 'column',
                        target: 1,
                        display: DataTable.Responsive.display.modal({
                            header: function(row) {
                                var data = row.data();
                                var textt = 'More Information - المزيد من المعلومات';
                                return textt;
                            }
                        }),
                        renderer: function(api, rowIdx, columns) {
                            var data = $.map(columns, function(col, i) {
                                if (col.columnIndex != 0 && col.columnIndex != 1 && col.columnIndex != -
                                    1) {
                                    return datatable.column(i).visible() ?
                                        '<tr data-dt-row="' + col.rowIndex + '" data-dt-column="' + col
                                        .columnIndex + '">' +
                                        "<td>" + col.title + ":" + "</td> " + "<td>" + col.data +
                                        "</td>" +
                                        "</tr>" :
                                        "";
                                }
                            }).join("");
                            return data ? $("<table class='table table-bordered'/>").append(data) : false;
                        },
                    }
                },
                processing: true,
                serverSide: true,
                searching: true,
                searchDelay: 1050,
                pageLength: 10,
                lengthMenu: [10, 50, 100],
                ajax: {
                    url: ajaxUrl,
                    type: "POST",
                    data: function(d) {
                        var filterParams = filterParameters(filterSelector);
                        d.params = $.extend({}, filterParams, additionalParams);
                        return d;
                    },
                    error: handleAjaxErrors,
                },
                columns: columnDefs,
                order: order,
                // Remove Select2 initialization from DataTable settings
                initComplete: function(settings, json) {
                    // Basic styling for length menu select
                    $(tableSelector + '_length select').addClass('form-select form-select-sm');
                },
                drawCallback: function(settings) {
                    // Ensure length menu select maintains basic styling
                    $(tableSelector + '_length select').addClass('form-select form-select-sm');
                }
            });

            return datatable;
        }



        // Helper function to get filter parameters (keep your existing implementation)
        function filterParameters(filterSelector) {
            var params = {};
            $(filterSelector).each(function() {
                var i = $(this).data('col-index');
                if ($(this).is(':checkbox')) {
                    params[i] = $(this).is(':checked') ? 'on' : 'off';
                } else {
                    if (params[i]) {
                        params[i] += '|' + $(this).val();
                    } else {
                        params[i] = $(this).val();
                    }
                }
            });
            return params;
        }

        function debounce(cb, interval, immediate) {
            var timeout;

            return function() {
                var context = this,
                    args = arguments;
                var later = function() {
                    timeout = null;
                    if (!immediate) cb.apply(context, args);
                };

                var callNow = immediate && !timeout;

                clearTimeout(timeout);
                timeout = setTimeout(later, interval);

                if (callNow) cb.apply(context, args);
            };
        };
    </script>
    @include('CP.metronic.CustomJS.indexJS')

    @stack('scripts')

    <script>
        KTToggle.createInstances();
        var toggleElement = document.querySelector("#kt_app_sidebar_toggle");
        var toggle = KTToggle.getInstance(toggleElement);
        toggle.on("kt.toggle.changed", function() {
            localStorage.setItem("aside_toggle", toggle.isEnabled());
        });

        if (localStorage.getItem("aside_toggle") == 'true') {
            toggle.enable();
        } else {
            toggle.disable();
        }
    </script>
    <!--end::Javascript-->
</body>
<!--end::Body-->

</html>
