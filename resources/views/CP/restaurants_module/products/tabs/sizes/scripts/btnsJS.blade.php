@php
    $prodsizeconfig = config('modules.restaurants.children.products.children.sizes');
@endphp
{{-- add BTN --}}
<script>
    // Add Button Handler
    $(document).on('click', "#add_{{ $prodsizeconfig['singular_key'] }}_modal", function(e) {
        e.preventDefault();
        const button = $(this);
        button.attr("data-kt-indicator", "on");
        const url = button.attr('href');

        ModalRender.render({
            url: url,
            button: button,
            modalId: '#kt_modal_general',
            modalBootstrap: new bootstrap.Modal(document.querySelector('#kt_modal_general')),
            formId: '#{{ $prodsizeconfig['singular_key'] }}_modal_form',
                dataTableId: datatableproductSizes,
            submitButtonName: "[data-kt-modal-action='submit_{{ $prodsizeconfig['singular_key'] }}']",

            onFormSuccessCallBack: (response) => {
                console.log('Form submission completed successfully:', response);
            }
        });
    });
</script>
{{-- Update BTN --}}
<script>
    $(document).on('click', ".btn_update_{{ $prodsizeconfig['singular_key'] }}", function(e) {
        e.preventDefault();
        const button = $(this);
        button.attr("data-kt-indicator", "on");
        const url = button.attr('href');
        ModalRender.render({
            url: url,
            button: button,
            modalId: '#kt_modal_general',
            modalBootstrap: new bootstrap.Modal(document.querySelector('#kt_modal_general')),
            formId: '#{{ $prodsizeconfig['singular_key'] }}_modal_form',
            dataTableId: datatableproductSizes,
            submitButtonName: "[data-kt-modal-action='submit_{{ $prodsizeconfig['singular_key'] }}']",

            onFormSuccessCallBack: (response) => {
                console.log('Extra actions completed');
            }
        });
    });
</script>
{{-- Delete BTN --}}
<script>
    $(document).on('click', '.btn_delete_' + "{{ $prodsizeconfig['singular_key'] }}", function(e) {
        e.preventDefault();
        const URL = $(this).attr('href');
        const itemModelName = $(this).attr('data-' + "{{ $prodsizeconfig['singular_key'] }}" + '-name');
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
                        datatableproductSizes.ajax.reload(null, false);
                        // datatableSupervisor.ajax.reload(null, false);

                        Swal.fire({
                            text: response.message,
                            icon: "success",
                            showConfirmButton: false,
                            timer: 1500
                        });
                    },




                    complete: function() {

                        // datatableSupervisor.ajax.reload(null, false);
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
