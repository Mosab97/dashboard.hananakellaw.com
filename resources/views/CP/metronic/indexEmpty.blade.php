<!DOCTYPE html>
@if (app()->getLocale() == 'en')
    <html lang="{{ app()->getLocale() }}">
@elseif (app()->getLocale() == 'ar' || app()->getLocale() == 'he')
    <html direction="rtl" dir="rtl" style="direction: rtl" lang="{{ app()->getLocale() }}">
@endif

<!--begin::Head-->

<head>
    {{-- <base href="" /> --}}
    <title>{{env('APP_NAME')}} CRM - @yield('title', 'Home')</title>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{env('APP_NAME')}} CRM " />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" href="{{ asset('media/logos/favicon.ico') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!--begin::Vendor Stylesheets(used for this page only)-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;500&family=Tajawal:wght@300;500&display=swap"
        rel="stylesheet">
    @if (app()->getLocale() == 'en')
        <!--begin::Fonts(mandatory for all pages)-->
        <!--end::Fonts-->
        <link href="{{ asset('plugins/custom/datatables/datatables.bundle.css?v=1') }}" rel="stylesheet"
            type="text/css" />
        <!--end::Vendor Stylesheets-->
        <!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
        <link href="{{ asset('plugins/global/plugins.bundle.css?v=1') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('css/style.bundle.css?v=1') }}" rel="stylesheet" type="text/css" />
    @elseif (app()->getLocale() == 'ar' || app()->getLocale() == 'he')
        <!--begin::Fonts(mandatory for all pages)-->
        <!--end::Fonts-->
        <link href="{{ asset('plugins/custom/datatables/datatables.bundle.rtl.css?v=1') }}" rel="stylesheet"
            type="text/css" />
        <!--end::Vendor Stylesheets-->
        <!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
        <link href="{{ asset('plugins/global/plugins.bundle.rtl.css?v=1') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('css/style.bundle.rtl.css?v=1') }}" rel="stylesheet" type="text/css" />
    @endif
    @if (app()->getLocale() == 'he')
        <link href="{{ asset('css/custom.css?v=1') }}" rel="stylesheet" type="text/css" />
    @endif

    @stack('styles')
    <style>
        @media (min-width: 992px) {
            [data-kt-app-sidebar-minimize=on][data-kt-app-sidebar-hoverable=true] .app-sidebar:not(:hover) .app-sidebar-menu input {
                opacity: 0;
                transition: opacity 0.3s ease !important;
            }
        }
        .scrollWhatApp  {
            position: relative;
            overflow: hidden;
            overflow-y: scroll !important;
            height: 400px;
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
    @yield('content')
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
        function createDataTable(tableSelector, columnDefs, ajaxUrl, order, params = null) {
            var table = document.querySelector(tableSelector);
            return $(table).DataTable({
                responsive: true,
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
                        d.params = filterParameters();
                    }
                },
                columns: columnDefs,
                order: order
            });
        }
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
        function globalRenderModal(url, button, modalId, modalBootstrap, validatorFields, formId, dataTableId,
            submitButtonName, RequiredInputList = null, onFormSuccessCallBack = null) {


            $.ajax({
                type: "GET",
                url: url,
                dataType: "json",
                success: function(response) {
                    $(modalId).find('.modal-dialog').html(response.createView);
                    modalBootstrap.show();
                    KTScroll.createInstances();
                    KTImageInput.createInstances();

                    const form = document.querySelector(formId);

                    var validator = FormValidation.formValidation(
                        form, {
                            fields: validatorFields,
                            plugins: {
                                trigger: new FormValidation.plugins.Trigger(),
                                bootstrap: new FormValidation.plugins.Bootstrap5({
                                    rowSelector: '.fv-row',
                                    eleInvalidClass: '',
                                    eleValidClass: ''
                                })
                            }
                        }
                    );


                    if (RequiredInputList != null) {
                        for (var key in RequiredInputList) {
                            // console.log("key " + key + " has value " + RequiredInputList[key]);
                            var fieldName = $(RequiredInputList[key] + ["[name=" + key + "]"]).closest(
                                    ".fv-row")
                                .find(
                                    "label[data-input-name]").attr('data-input-name');

                            const NameValidators = {
                                validators: {
                                    notEmpty: {
                                        message: fieldName + ' is required',
                                    },
                                },
                            };

                            validator.addField(key, NameValidators);
                            // validator.addField($(this).find('.constantNames').attr('name'),
                            //                         NameValidators);
                        }

                    }

                    // Submit button handler
                    const submitButton = document.querySelector(submitButtonName);
                    submitButton.addEventListener('click', function(e) {
                        // Prevent default button action
                        e.preventDefault();

                        // const form = document.querySelector(formId);

                        // Validate form before submit
                        if (validator) {
                            validator.validate().then(function(status) {
                                console.log('validated!');

                                if (onFormSuccessCallBack == null) {
                                    onFormSuccessCallBack = function(response) {
                                        toastr.success(response.message);
                                        form.reset();
                                        modalBootstrap.hide();
                                        if (dataTableId != '')
                                            dataTableId.ajax.reload(null,
                                                false);
                                    };
                                }
                                if (status == 'Valid') {
                                    // Show loading indication
                                    submitButton.setAttribute('data-kt-indicator', 'on');

                                    // Disable button to avoid multiple clicks
                                    submitButton.disabled = true;

                                    let data = $(form).serialize();

                                    $.ajax({
                                        type: 'POST',
                                        url: $(form).attr('action'),
                                        data: data,
                                        success: onFormSuccessCallBack,
                                        complete: function() {
                                            // Release button
                                            submitButton.removeAttribute(
                                                'data-kt-indicator');

                                            // Re-enable button
                                            submitButton.disabled = false;
                                        },
                                        error: function(response, textStatus,
                                            errorThrown) {
                                            toastr.error(response.responseJSON
                                                .message);
                                        }
                                    });
                                } else {
                                    Swal.fire({
                                        text: "Sorry, looks like there are some errors detected, please try again.",
                                        icon: "error",
                                        buttonsStyling: false,
                                        confirmButtonText: "Ok, got it!",
                                        customClass: {
                                            confirmButton: "btn btn-primary"
                                        }
                                    });
                                }
                            });
                        }
                    });

                    $('[data-control="select2"]').select2({
                        dropdownParent: $(modalId),
                        allowClear: true,
                    });
                },
                complete: function() {
                    if (button) {
                        button.removeAttr('data-kt-indicator');
                    }
                }
            });
        }
    </script>
    <script>
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

    <script>
        function filterParameters() {
            var params = {};
            $('.datatable-input').each(function() {
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
    </script>



    <script>

        var chat = document.querySelector(".chat");
        var blockUI_chat = new KTBlockUI(chat, {
            message: '<div class="blockui-message"><span class="spinner-border text-primary"></span> Please wait...</div>',
        });

        jQuery(document).on('click', '.whatsApp-send', function () {

            var mobile = $("#chat_mobile").val();
            var sender = $("#chat_sender").val();
            var patient = $("#chat_patient").val();
            var thisDiv = jQuery('.scrollWhatApp');


            jQuery.ajax({
                url: "/sendWhatsappChat",
                type: 'POST',
                data: jQuery('#fromWhatsApp').serialize(),
                dataType: "json",
                beforeSend: function () {
                    jQuery('.whatsApp-send').addClass('spinner spinner-white spinner-right');
                },
                complete: function () {
                    $("#chat_message").text("");
                    $("#chat_message").val("");
                    jQuery('.whatsApp-send').removeClass('spinner spinner-white spinner-right');
                    jQuery('.scrollWhatApp').scrollTop($('.scrollWhatApp').prop("scrollHeight"));
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    $.ajax({
                        url: '/getWhatsAppMessage?mobile=' + mobile + "&patient=" + patient + "&sender=" + sender + "&limit=10",
                        success: function (data) {
                            if (data.status == 0)
                                toastr.error("Error", data.message);
                            else
                                thisDiv.find('.messagesWhatApp').append(data);
                        },
                        beforeSend: function () {


                        },
                        complete: function () {

                            jQuery('.scrollWhatApp').scrollTop($('.scrollWhatApp').prop("scrollHeight"));


                        },
                        dataType: 'html'
                    });
                },
                error: function (xhr) {

                    toastr.error("Error", "Error");
                    return false;
                }
            });


            return false;
        });

        $(document).ready(function () {

            $(document).on('click', '.ShowChatWhats', function (e) {
                e.preventDefault();
                const url = $(this).attr('link');
                var drawerElement = document.querySelector("#kt_drawer_chat");
                var drawer = KTDrawer.getInstance(drawerElement);
                drawer.show();
                $.ajax({
                    url: '/getUnreadMessages?sender=EMC&type=count',
                    success: function (data) {
                        if (parseInt(data) > 0) {
                            $('#ssender').val('EMC');
                            $('#senderN').html('EMC ('+data+')');
                            $('.searchinputWhat').val('');
                        } else {
                            $('#ssender').val('EMC');
                            $('#senderN').html('EMC');
                            $('.searchinputWhat').val('');
                        }

                    },
                    beforeSend: function () {


                    },
                    complete: function () {

                    },

                });




                $(chat).find('.unread_messages').html('');

                blockUI_chat.block();

                $.ajax({
                    type: "GET",
                    url: url,
                    dataType: "html",
                    success: function (response) {
                        // console.log(response);

                        $(chat).find('.unread_messages').html(response);
                    },
                    complete: function () {
                        blockUI_chat.release();
                        // blockUI.release();
                    }

                });

            });



        });

    </script>

    @stack('scripts')

    {{-- <script src="{{ asset('js/widgets.bundle.js') }}"></script> --}}
    {{-- <script src="{{ asset('js/custom/widgets.js') }}"></script> --}}
    {{-- <script src="{{ asset('js/custom/apps/chat/chat.js') }}"></script>
    <script src="{{ asset('js/custom/utilities/modals/upgrade-plan.js') }}"></script>
    <script src="{{ asset('js/custom/utilities/modals/users-search.js') }}"></script> --}}
    <!--end::Custom Javascript-->
    <!--end::Javascript-->
</body>
<!--end::Body-->

</html>
