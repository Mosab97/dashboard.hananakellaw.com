{{-- add BTN --}}
<script>
    // Add Button Handler
    $(document).on('click', "#add_{{ $config['singular_key'] }}_modal", function(e) {
        e.preventDefault();
        const button = $(this);
        button.attr("data-kt-indicator", "on");
        const url = button.attr('href');

        ModalRender.render({
            url: url,
            button: button,
            modalId: '#kt_modal_general',
            modalBootstrap: new bootstrap.Modal(document.querySelector('#kt_modal_general')),
            formId: '#{{ $config['singular_key'] }}_modal_form',
            dataTableId: datatableArticleContents,
            submitButtonName: "[data-kt-modal-action='submit_{{ $config['singular_key'] }}']",

            callBackFunction: () => {
                console.log('Modal callback function called - Add button');

                // Add a small delay to ensure DOM is fully rendered
                setTimeout(() => {
                    console.log('Initializing repeater after timeout');

                    // Check if jQuery repeater is available
                    if (typeof $.fn.repeater === 'undefined') {
                        console.error('jQuery repeater plugin is not loaded!');
                        return;
                    }

                    // Check if the repeater element exists
                    const repeaterElement = $('.repeater');
                    console.log('Found repeater elements:', repeaterElement.length);

                    if (repeaterElement.length === 0) {
                        console.error('Repeater element .repeater not found!');
                        return;
                    }

                    try {
                        // Initialize repeater
                        console.log('Initializing article content repeater');
                        repeaterElement.repeater({
                            show: function() {
                                console.log('Article content repeater show');
                                $(this).slideDown();
                            },

                            hide: function(deleteElement) {
                                if (confirm('{{ t('Are you sure you want to delete this element?') }}')) {
                                    $(this).slideUp(deleteElement);
                                }
                            },

                            ready: function(setIndexes) {
                                console.log('Repeater ready callback');
                                // $dragAndDrop.on('drop', setIndexes);
                            },
                            isFirstItemUndeletable: true
                        });
                        console.log('Article content repeater initialized successfully');
                    } catch (error) {
                        console.error('Error initializing repeater:', error);
                    }
                }, 200); // 200ms delay
            },

            onFormSuccessCallBack: (response) => {
                console.log('Form submission completed successfully:', response);
            }
        });
    });
</script>
{{-- Update BTN --}}
<script>
    $(document).on('click', ".btn_update_{{ $config['singular_key'] }}", function(e) {
        e.preventDefault();
        const button = $(this);
        button.attr("data-kt-indicator", "on");
        const url = button.attr('href');
        ModalRender.render({
            url: url,
            button: button,
            modalId: '#kt_modal_general',
            modalBootstrap: new bootstrap.Modal(document.querySelector('#kt_modal_general')),
            formId: '#{{ $config['singular_key'] }}_modal_form',
            dataTableId: datatableArticleContents,
            submitButtonName: "[data-kt-modal-action='submit_{{ $config['singular_key'] }}']",

            callBackFunction: () => {
                console.log('Modal callback function called - Update button');

                // Add a small delay to ensure DOM is fully rendered
                setTimeout(() => {
                    console.log('Initializing repeater after timeout');

                    // Check if jQuery repeater is available
                    if (typeof $.fn.repeater === 'undefined') {
                        console.error('jQuery repeater plugin is not loaded!');
                        return;
                    }

                    // Check if the repeater element exists
                    const repeaterElement = $('.repeater');
                    console.log('Found repeater elements:', repeaterElement.length);

                    if (repeaterElement.length === 0) {
                        console.error('Repeater element .repeater not found!');
                        return;
                    }

                    try {
                        // Initialize repeater
                        console.log('Initializing article content repeater');
                        repeaterElement.repeater({
                            show: function() {
                                console.log('Article content repeater show');
                                $(this).slideDown();
                            },

                            hide: function(deleteElement) {
                                if (confirm('{{ t('Are you sure you want to delete this element?') }}')) {
                                    $(this).slideUp(deleteElement);
                                }
                            },

                            ready: function(setIndexes) {
                                console.log('Repeater ready callback');
                                // $dragAndDrop.on('drop', setIndexes);
                            },
                            isFirstItemUndeletable: true
                        });
                        console.log('Article content repeater initialized successfully');
                    } catch (error) {
                        console.error('Error initializing repeater:', error);
                    }
                }, 200); // 200ms delay
            },

            onFormSuccessCallBack: (response) => {
                console.log('Extra actions completed');
            }
        });
    });
</script>
{{-- Delete BTN --}}
<script>
    $(document).on('click', '.btn_delete_' + "{{ $config['singular_key'] }}", function(e) {
        e.preventDefault();
        const URL = $(this).attr('href');
        const itemModelName = $(this).attr('data-' + "{{ $config['singular_key'] }}" + '-name');
        Swal.fire({
            html: "Are you sure you want to delete " + itemModelName + "?",
            icon: "warning",
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: "Yes, delete!",
            cancelButtonText: "No, cancel",
            customClass: {
                confirmButton: "btn fw-bold btn-danger",
                cancelButton: "btn fw-bold btn-active-light-primary"
            }
        }).then(function(result) {
            if (result.value) {
                $.ajax({
                    type: "DELETE",
                    url: URL,
                    dataType: "json",
                    success: function(response) {
                        datatableArticleContents.ajax.reload(null, false);
                        // datatableSupervisor.ajax.reload(null, false);

                        Swal.fire({
                            text: response.message,
                            icon: "success",
                            showConfirmButton: false,
                            timer: 1500
                        });
                    },




                    complete: function() {

                        // datatableArticleContents.ajax.reload(null, false);
                        // refreshPageFilters();

                    },
                    error: function(response, textStatus,
                        errorThrown) {
                        toastr.error(response
                            .responseJSON
                            .message);
                    },
                });

            } else if (result.dismiss === 'cancel') {}

        });
    });
</script>
