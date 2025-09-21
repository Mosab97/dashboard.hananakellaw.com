{{-- add_ --}}
<script>
    $(document).on('click', "#add_{{ $_model::ui['s_lcf'] }}_modal", function(e) {
        e.preventDefault();
        const button = $(this);
        button.attr("data-kt-indicator", "on");
        const url = button.attr('href');
        ModalRender.render({
            url: url,
            button: button,
            modalId: '#kt_modal_general',
            modalBootstrap: new bootstrap.Modal(document.querySelector('#kt_modal_general')),
            formId: '#{{ $_model::ui['s_lcf'] }}_modal_form',
            dataTableId: datatable,
            submitButtonName: "[data-kt-modal-action='submit_{{ $_model::ui['s_lcf'] }}']",
        });
    });
</script>
{{-- btn_update_ --}}
<script>
    $(document).on('click', ".btn_update_{{ $_model::ui['s_lcf'] }}", function(e) {
        e.preventDefault();
        const button = $(this);
        button.attr("data-kt-indicator", "on");
        const url = button.attr('href');
        ModalRender.render({
            url: url,
            button: button,
            modalId: '#kt_modal_general',
            modalBootstrap: new bootstrap.Modal(document.querySelector('#kt_modal_general')),
            formId: '#{{ $_model::ui['s_lcf'] }}_modal_form',
            dataTableId: datatable,
            submitButtonName: "[data-kt-modal-action='submit_{{ $_model::ui['s_lcf'] }}']",
        });
    });
</script>
{{-- btn_delete_ --}}
<script>
    $(document).on('click', '.btn_delete_' + "{{ $_model::ui['s_lcf'] }}", function(e) {
        e.preventDefault();
        const URL = $(this).attr('href');
        const itemModelName = $(this).attr('data-' + "{{ $_model::ui['s_ucf'] }}" + '-name');
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
                        datatable.ajax.reload(null, false);
                        Swal.fire({
                            text: response.message,
                            icon: "success",
                            showConfirmButton: false,
                            timer: 1500
                        });
                    },
                    complete: function() {},
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


<script>
    $(document).on('click', '#resetRole', function(e) {
        e.preventDefault();
        $('input[name="role_name"]').prop('checked', false);
    });
</script>
