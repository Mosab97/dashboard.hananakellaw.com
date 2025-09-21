<script>
    class ModalRender {
        constructor() {
            // Bind methods to preserve this context
            this.handleFormValidation = this.handleFormValidation.bind(this);
            this.handleFormSubmission = this.handleFormSubmission.bind(this);
            this.initializeModalElements = this.initializeModalElements.bind(this);
        }

        static async render({
            url,
            button,
            modalId,
            modalBootstrap,
            formId = null,
            dataTableId = null,
            submitButtonName = null,
            onFormSuccessCallBack = null,
            callBackFunction = null,
            repeaterOptions = null
        }) {
            try {
                // Fetch modal content
                const response = await $.ajax({
                    type: "GET",
                    url: url,
                    dataType: "json"
                });

                // Update modal content
                const $modal = $(modalId);
                $modal.find('.modal-dialog').html(response.createView);

                // Initialize trackers for cleanup
                let modalFlatpickrManager = null;
                let formHandler = null;
                let repeaterManager = null;

                // Handle modal shown event
                $modal.on('shown.bs.modal', function() {
                    // Initialize scrolling and image inputs
                    KTScroll.createInstances();
                    KTImageInput.createInstances();

                    if (formId) {
                        const form = document.querySelector(formId);

                        if (form && submitButtonName) {
                            try {
                                // Setup form success callback
                                const combinedCallback = async (response, form, modalBootstrap,
                                    dataTableId) => {
                                    // Handle response message
                                    if (response.message) {
                                        const color = response.color || 'success';
                                        toastr[color](response.message);
                                    }

                                    // Handle successful response
                                    if (response.status) {
                                        if (form?.reset) form.reset();
                                        if (modalBootstrap?.hide) modalBootstrap.hide();
                                        if (dataTableId?.ajax?.reload) dataTableId.ajax.reload(null,
                                            false);
                                    }

                                    // Handle attachments
                                    if (response.attachment) {
                                        ModalRender.handleAttachment(response.attachment);
                                    }

                                    // Execute additional callback if provided
                                    if (onFormSuccessCallBack) {
                                        await onFormSuccessCallBack(response, form, modalBootstrap,
                                            dataTableId);
                                    }
                                };

                                // Initialize form handler
                                formHandler = new FormValidationManager(form).applyValidationRules();

                                // Setup form submission
                                const submitButton = document.querySelector(submitButtonName);
                                if (submitButton) {
                                    submitButton.addEventListener('click', async (e) => {
                                        e.preventDefault();
                                        await ModalRender.handleFormSubmission(form,
                                            formHandler, submitButton, modalBootstrap,
                                            dataTableId, combinedCallback);
                                    });
                                }

                                // Initialize repeater if options provided
                                if (repeaterOptions) {
                                    // repeaterManager = new RepeaterManager({
                                    //     ...repeaterOptions,
                                    //     modalId: modalId,
                                    //     form: form,
                                    //     validator: formHandler
                                    // });
                                    // repeaterManager.initialize();
                                }

                                // Initialize date pickers
                                modalFlatpickrManager = FlatpickrManager.initialize(form);

                                // Initialize select2 elements
                                ModalRender.initializeSelect2Elements(modalId);

                            } catch (error) {
                                console.error('Error initializing form:', error);
                                handleAjaxErrors(error);
                            }
                        }
                    }

                    // Execute additional callback if provided
                    if (callBackFunction && typeof callBackFunction === 'function') {
                        callBackFunction();
                    }
                });

                // Handle modal cleanup
                $modal.on('hidden.bs.modal', function() {
                    if (modalFlatpickrManager) {
                        modalFlatpickrManager.destroy();
                    }
                    if (repeaterManager) {
                        repeaterManager.cleanup();
                    }
                    // Remove event listeners
                    $modal.off('shown.bs.modal hidden.bs.modal');
                });

                // Show modal
                modalBootstrap.show();

            } catch (error) {
                handleAjaxErrors(error);
            } finally {
                if (button) {
                    button.removeAttr('data-kt-indicator');
                }
            }
        }

        static async handleFormSubmission(form, validator, submitButton, modalBootstrap, dataTableId, callback) {
            try {
                // Set loading state
                submitButton.setAttribute('data-kt-indicator', 'on');
                submitButton.disabled = true;

                // Validate form
                const validationResult = await validator.validate();

                if (validationResult === 'Valid') {
                    // Prepare form data
                    const formData = new FormData(form);

                    // Submit form
                    const response = await $.ajax({
                        type: 'POST',
                        url: form.action,
                        data: formData,
                        processData: false,
                        contentType: false
                    });

                    // Execute callback
                    if (callback) {
                        await callback(response, form, modalBootstrap, dataTableId);
                    }
                } else {
                    // Show validation error
                    Swal.fire({
                        text: "Please correct the errors before submitting.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                }
            } catch (error) {
                handleAjaxErrors(error);
            } finally {
                // Reset loading state
                submitButton.removeAttribute('data-kt-indicator');
                submitButton.disabled = false;
            }
        }

        static handleAttachment(attachment) {
            const attachmentElement = $(document).find(`#${attachment.attachment_type_id}`);
            if (attachmentElement.length) {
                attachmentElement
                    .removeClass('d-none')
                    .attr('href', attachment.url);
            }
        }

        static initializeSelect2Elements(modalId) {
            $(modalId).find('[data-control="select2"]').each(function() {
                $(this).select2({
                    dropdownParent: $(modalId),
                    allowClear: true
                });
            });
        }
    }
</script>

{{-- <script>
    class RepeaterManager {
        constructor(options) {
            this.repeaterSelector = options.repeaterSelector;
            this.modalId = options.modalId;
            this.form = options.form;
            this.validator = options.validator;
            this.validationRules = options.validationRules || {};
            this.onShowCallback = options.onShowCallback;
            this.onHideCallback = options.onHideCallback;
            this.deletedItemsContainer = options.deletedItemsContainer;
        }

        initialize() {
            const self = this;

            // Check if repeater element exists
            const $repeater = $(this.repeaterSelector);
            if (!$repeater.length) {
                console.error('Repeater element not found:', this.repeaterSelector);
                return;
            }

            $repeater.repeater({
                initEmpty: false,
                defaultValues: {},
                show: function() {
                    console.log('Show triggered');
                    $(this).slideDown();
                    self.handleShow(this);
                    if (self.onShowCallback) {
                        self.onShowCallback(this);
                    }
                },
                hide: function(deleteElement) {
                    console.log('Hide triggered');
                    const item = $(this);
                    const itemId = item.attr('data-question-id');

                    if (itemId) {
                        $(self.deletedItemsContainer).append(
                            `<input type="hidden" name="deleted_questions[]" value="${itemId}">`
                        );
                    }

                    self.handleHide(this);
                    if (self.onHideCallback) {
                        self.onHideCallback(this);
                    }

                    $(this).slideUp(deleteElement);
                },
                ready: function() {
                    console.log('Repeater ready');
                    // Initialize existing items
                    $(this).find('[data-repeater-item]').each(function() {
                        self.handleShow(this);
                    });
                },
                repeaters: [{
                    selector: '.inner-repeater',
                    show: function() {
                        $(this).slideDown();
                    },
                    hide: function(deleteElement) {
                        $(this).slideUp(deleteElement);
                    }
                }]
            });

            // Log initialization
            console.log('Repeater initialized on:', this.repeaterSelector);
        }

        handleShow(element) {
            console.log('Handling show for element:', element);

            // Reinitialize Select2 for new items
            $(element).find('[data-control="select2"]').each((i, select) => {
                $(select).select2({
                    dropdownParent: $(this.modalId),
                    allowClear: true
                });
            });

            // Add validation for new fields
            this.addValidationRules(element);
        }

        handleHide(element) {
            console.log('Handling hide for element:', element);

            // Remove validation for deleted fields
            this.removeValidationRules(element);

            // Destroy Select2 instances
            $(element).find('[data-control="select2"]').each((i, select) => {
                $(select).select2('destroy');
            });
        }

        addValidationRules(element) {
            if (!this.validator) return;

            const fields = $(element).find('[data-validation]');
            fields.each((i, field) => {
                const fieldName = $(field).attr('name');
                const validationType = $(field).data('validation');

                if (this.validationRules[validationType]) {
                    this.validator.addField(fieldName, this.validationRules[validationType]);
                }
            });
        }

        removeValidationRules(element) {
            if (!this.validator) return;

            const fields = $(element).find('[data-validation]');
            fields.each((i, field) => {
                const fieldName = $(field).attr('name');
                this.validator.removeField(fieldName);
            });
        }
    }

    // Example usage for questions repeater
    class QuestionsRepeaterManager extends RepeaterManager {
        constructor(options) {
            super({
                ...options,
                validationRules: {
                    title: {
                        validators: {
                            notEmpty: {
                                message: 'Question title is required'
                            }
                        }
                    },
                    score: {
                        validators: {
                            notEmpty: {
                                message: 'Score is required'
                            },
                            numeric: {
                                message: 'Score must be a number'
                            },
                            between: {
                                min: 0,
                                max: 100,
                                message: 'Score must be between 0 and 100'
                            }
                        }
                    }
                }
            });
        }

        handleShow(element) {
            super.handleShow(element);

            // Get the current index
            const index = $(element).index();

            // Update input names with correct index and format
            $(element).find('input[name*="title"]').each(function() {
                const input = $(this);
                const locale = input.closest('.col-md-5').find('label small').text().replace(/[()]/g, '')
                    .toLowerCase();
                const newName = `questions[${index}][title][${locale}]`;
                input.attr('name', newName);
            });

            $(element).find('input[name="score"]').attr('name', `questions[${index}][score]`);
        }

        initialize() {
            const self = this;

            $(this.repeaterSelector).repeater({
                initEmpty: false,
                defaultValues: {},
                show: function() {
                    $(this).slideDown();
                    self.handleShow(this);
                    if (self.onShowCallback) {
                        self.onShowCallback(this);
                    }
                },
                hide: function(deleteElement) {
                    const item = $(this);
                    const itemId = item.attr('data-question-id');

                    if (itemId) {
                        $(self.deletedItemsContainer).append(
                            `<input type="hidden" name="deleted_questions[]" value="${itemId}">`
                        );
                    }

                    self.handleHide(this);
                    if (self.onHideCallback) {
                        self.onHideCallback(this);
                    }

                    $(this).slideUp(deleteElement);

                    // After removing an item, update all remaining items' indices
                    self.updateAllIndices();
                },
                ready: function() {
                    // Initialize existing items
                    $(this).find('[data-repeater-item]').each(function() {
                        self.handleShow(this);
                    });
                }
            });
        }

        updateAllIndices() {
            const items = $(this.repeaterSelector).find('[data-repeater-item]');
            items.each((index, element) => {
                $(element).find('input[name*="title"]').each(function() {
                    const input = $(this);
                    const locale = input.closest('.col-md-5').find('label small').text().replace(
                        /[()]/g, '').toLowerCase();
                    const newName = `questions[${index}][title][${locale}]`;
                    input.attr('name', newName);
                });

                $(element).find('input[name*="score"]').attr('name', `questions[${index}][score]`);
            });
        }
    }
</script> --}}

<script>
    class QuestionsRepeaterManager {
        constructor(options) {
            this.repeaterSelector = options.repeaterSelector;
            this.modalId = options.modalId;
            this.form = options.form;
            this.deletedItemsContainer = options.deletedItemsContainer;
        }

        initialize() {
            const self = this;

            $(this.repeaterSelector).repeater({
                initEmpty: false,
                defaultValues: {},
                show: function() {
                    $(this).slideDown();
                    self.handleShow(this);
                },
                hide: function(deleteElement) {
                    const item = $(this);
                    const itemId = item.data('question-id');

                    if (itemId) {
                        $(self.deletedItemsContainer).append(
                            `<input type="hidden" name="deleted_questions[]" value="${itemId}">`
                        );
                    }

                    $(this).slideUp(deleteElement);
                    self.updateAllIndices();
                },
                ready: function() {
                    $(this).find('[data-repeater-item]').each(function() {
                        self.handleShow(this);
                    });
                }
            });
        }

        handleShow(element) {
            const index = $(element).index();

            // Update input names with correct index and format
            $(element).find('input[name*="title"]').each(function() {
                const input = $(this);
                const locale = input.closest('.col-md-5').find('label small').text()
                    .replace(/[()]/g, '').toLowerCase();
                const newName = `questions[${index}][title][${locale}]`;
                input.attr('name', newName);
            });
        }

        updateAllIndices() {
            const items = $(this.repeaterSelector).find('[data-repeater-item]');
            items.each((index, element) => {
                $(element).find('input[name*="title"]').each(function() {
                    const input = $(this);
                    const locale = input.closest('.col-md-5').find('label small').text()
                        .replace(/[()]/g, '').toLowerCase();
                    const newName = `questions[${index}][title][${locale}]`;
                    input.attr('name', newName);
                });
            });
        }
    }
</script>
