<script>
    class ModalRender {
        static fields = new Set();
        static validator = null;

        static #initializeValidator(form) {
            return FormValidation.formValidation(form, {
                fields: {},
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.fv-row',
                        eleInvalidClass: '',
                        eleValidClass: ''
                    })
                }
            });
        }

        static #applyValidationRules(form) {
            this.validator = this.#initializeValidator(form);

            $(form).find('input, select, textarea').each((index, element) => {
                const field = $(element);
                const fieldName = field.attr('name');

                if (!fieldName) return;

                if (field.hasClass('validate-required')) {
                    const label = field.closest('.fv-row').find('label').text().trim()
                        .replace(/\s*\([^)]*\)/g, '')
                        .replace(/:$/, '');

                    this.validator.addField(fieldName, {
                        validators: {
                            notEmpty: {
                                message: `${label} ` + "{{ t('is required') }}"
                            }
                        }
                    });
                    this.fields.add(fieldName);
                }

                if (field.hasClass('validate-number')) {
                    this.validator.addField(fieldName, {
                        validators: {
                            numeric: {
                                message: "{{ t('The value must be a valid number') }}",
                                decimalSeparator: '.'
                            }
                        }
                    });
                    this.fields.add(fieldName);
                }

                if (field.hasClass('validate-min-0')) {
                    this.validator.addField(fieldName, {
                        validators: {
                            greaterThan: {
                                min: 0,
                                message: "{{ t('The value must be greater than or equal to 0') }}"
                            }
                        }
                    });
                    this.fields.add(fieldName);
                }
            });

            return this.validator;
        }
        static cleanup() {
            if (this.validator) {
                try {
                    // Get the form element directly from the validator's element
                    const form = this.validator.form;
                    if (form) {
                        const currentFields = new Set();
                        // Find all form fields with names
                        $(form).find('input[name], select[name], textarea[name]').each((index, element) => {
                            currentFields.add($(element).attr('name'));
                        });

                        // Remove validation only for fields that exist
                        this.fields.forEach(fieldName => {
                            if (currentFields.has(fieldName)) {
                                try {
                                    this.validator.removeField(fieldName);
                                } catch (error) {
                                    console.log(`Skipping removal of field: ${fieldName}`, error);
                                }
                            }
                        });
                    }
                } catch (error) {
                    console.log('Error during cleanup:', error);
                }

                // Clear the fields set regardless of any errors
                this.fields.clear();
                this.validator = null;
            }
        }

        static async render({
            url,
            button,
            modalId,
            modalBootstrap,
            formId = null,
            dataTableId = null,
            submitButtonName = null,
            repeaterOptions = null,
            onFormSuccessCallBack = null,
            callBackFunction = null
        }) {
            try {
                console.log('Starting modal render with options:', {
                    url,
                    modalId,
                    formId
                });

                const response = await $.ajax({
                    type: "GET",
                    url: url,
                    dataType: "json"
                });

                const $modal = $(modalId);
                $modal.find('.modal-dialog').html(response.createView);

                let modalFlatpickrManager = null;
                let repeater = null;

                $modal.on('shown.bs.modal', function() {
                    console.log('Modal shown event triggered');

                    if (typeof KTScroll !== 'undefined') KTScroll.createInstances();
                    if (typeof KTImageInput !== 'undefined') KTImageInput.createInstances();

                    if (formId) {
                        const form = document.querySelector(formId);

                        if (form && submitButtonName) {
                            try {
                                const combinedCallback = async (response, form, modalBootstrap,
                                    dataTableId) => {
                                    if (response.message) {
                                        const color = response.color || 'success';
                                        toastr[color](response.message);
                                    }

                                    if (response.status) {
                                        if (form?.reset) form.reset();
                                        if (modalBootstrap?.hide) modalBootstrap.hide();
                                        if (dataTableId?.ajax?.reload) dataTableId.ajax.reload(null,
                                            false);
                                    }

                                    if (onFormSuccessCallBack) {
                                        await onFormSuccessCallBack(response, form, modalBootstrap,
                                            dataTableId);
                                    }
                                };

                                ModalRender.#applyValidationRules(form);

                                if (repeaterOptions) {
                                    repeater = new CustomRepeater({
                                        containerId: repeaterOptions.repeaterSelector,
                                        templateId: '#question_template',
                                        addButtonId: '#add_question',
                                        deletedItemsContainer: repeaterOptions
                                            .deletedItemsContainer,
                                        validator: ModalRender.validator,
                                        locales: ['en', 'ar'],
                                        onAdd: (item, index) => {
                                            console.log('Item added at index:', index);
                                        },
                                        onDelete: (item) => {
                                            console.log('Item deleted');
                                        },
                                        onUpdate: (items) => {
                                            console.log('Items updated, count:', items.length);
                                        }
                                    });
                                }

                                const submitButton = document.querySelector(submitButtonName);
                                if (submitButton) {
                                    submitButton.addEventListener('click', async (e) => {
                                        e.preventDefault();
                                        await ModalRender.handleFormSubmission(
                                            form,
                                            ModalRender.validator,
                                            submitButton,
                                            modalBootstrap,
                                            dataTableId,
                                            combinedCallback
                                        );
                                    });
                                }

                                modalFlatpickrManager = FlatpickrManager.initialize(form);
                                ModalRender.initializeSelect2Elements(modalId);

                            } catch (error) {
                                console.error('Error initializing form:', error);
                                handleAjaxErrors(error);
                            }
                        }
                    }

                    if (callBackFunction && typeof callBackFunction === 'function') {
                        callBackFunction();
                    }
                });

                $modal.on('hidden.bs.modal', function() {
                    console.log('Modal hidden event triggered - cleaning up resources');
                    if (modalFlatpickrManager) {
                        modalFlatpickrManager.destroy();
                    }
                    if (repeater) {
                        repeater.cleanup();
                    }
                    ModalRender.cleanup();
                    $modal.off('shown.bs.modal hidden.bs.modal');
                });

                modalBootstrap.show();

            } catch (error) {
                console.error('Error in modal render:', error);
                handleAjaxErrors(error);
            } finally {
                if (button) {
                    button.removeAttr('data-kt-indicator');
                }
            }
        }


        static async handleFormSubmission(form, validator, submitButton, modalBootstrap, dataTableId, callback) {
            try {
                console.log('Handling form submission');
                submitButton.setAttribute('data-kt-indicator', 'on');
                submitButton.disabled = true;

                // Only check total weight if the form has repeater items
                const hasRepeaterItems = form.querySelector('[data-repeater-item]') !== null;

                if (hasRepeaterItems) {
                    const weightInputs = form.querySelectorAll('input[name$="[weight]"]');
                    const totalWeight = Array.from(weightInputs)
                        .reduce((sum, input) => sum + (parseInt(input.value) || 0), 0);

                    if (totalWeight !== 100) {
                        Swal.fire({
                            text: `Total weight must equal 100. Current total: ${totalWeight}`,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                        submitButton.removeAttribute('data-kt-indicator');
                        submitButton.disabled = false;
                        return;
                    }
                }

                const validationResult = await validator.validate();
                console.log('Form validation result:', validationResult);

                if (validationResult === 'Valid') {
                    const formData = new FormData(form);
                    const response = await $.ajax({
                        type: 'POST',
                        url: form.action,
                        data: formData,
                        processData: false,
                        contentType: false
                    });

                    if (callback) {
                        await callback(response, form, modalBootstrap, dataTableId);
                    }
                } else {
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
                console.error('Error in form submission:', error);
                handleAjaxErrors(error);
            } finally {
                submitButton.removeAttribute('data-kt-indicator');
                submitButton.disabled = false;
            }
        }
        static initializeSelect2Elements(modalId) {
            console.log('Initializing Select2 elements');
            $(modalId).find('[data-control="select2"]').each(function() {
                $(this).select2({
                    dropdownParent: $(modalId),
                    allowClear: true
                });
            });
        }
    }
</script>




<script>
    class CustomRepeater {
        constructor(options) {
            this.containerId = options.containerId;
            this.container = document.querySelector(this.containerId);
            this.templateId = options.templateId;
            this.addButtonId = options.addButtonId;
            this.deletedItemsContainer = options.deletedItemsContainer;
            this.validator = options.validator;
            this.locales = options.locales || ['en', 'ar'];
            this.onAdd = options.onAdd || (() => {});
            this.onDelete = options.onDelete || (() => {});
            this.onUpdate = options.onUpdate || (() => {});

            this.itemsContainer = this.container.querySelector('[data-items-container]');
            this.template = document.querySelector(this.templateId).content;

            this.validationRules = {
                title: {
                    validators: {
                        notEmpty: {
                            message: 'Question title is required'
                        }
                    }
                },
                weight: {
                    validators: {
                        notEmpty: {
                            message: 'Weight is required'
                        },
                        numeric: {
                            message: 'Weight must be a number'
                        },
                        greaterThan: {
                            min: 1,
                            message: 'Weight must be greater than or equal to 1'
                        }
                    }
                }
            };

            // Initialize only if repeater options are present
            if (this.container && this.itemsContainer) {
                this.initialize();
            }
        }

        initialize() {
            // Set up event listeners
            const addButton = document.querySelector(this.addButtonId);
            if (addButton) {
                addButton.addEventListener('click', () => this.addItem());
            }

            // Initialize existing items
            this.updateAllIndices();
            this.setupValidation();

            // Setup delete event delegation
            this.itemsContainer.addEventListener('click', (e) => {
                if (e.target.matches('[data-delete-item]') || e.target.closest('[data-delete-item]')) {
                    const item = e.target.closest('[data-repeater-item]');
                    if (item) {
                        this.deleteItem(item);
                    }
                }
            });
        }

        addItem(data = null) {
            // Clone template
            const newItem = this.template.cloneNode(true);
            const itemWrapper = newItem.querySelector('[data-repeater-item]');

            // Set new index
            const index = this.getNextIndex();

            // Replace __INDEX__ placeholder in all input names
            itemWrapper.querySelectorAll('input').forEach(input => {
                if (input.name) {
                    input.name = input.name.replace(/__INDEX__/g, index);
                }
            });

            // Update language-specific inputs
            this.locales.forEach(locale => {
                const input = itemWrapper.querySelector(`[data-locale="${locale}"]`);
                if (input) {
                    input.classList.add('validate-required');
                    if (data?.title?.[locale]) {
                        input.value = data.title[locale];
                    }
                }
            });

            // Set weight field validation
            const weightInput = itemWrapper.querySelector('input[name^="questions"][name$="[weight]"]');
            if (weightInput) {
                weightInput.classList.add('validate-required', 'validate-number', 'validate-min-1');
            }

            // Add ID field if data has an ID
            if (data?.id) {
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = `questions[${index}][id]`;
                idInput.value = data.id;
                itemWrapper.appendChild(idInput);
            }

            // Add to container
            this.itemsContainer.appendChild(newItem);

            // Setup validation for new item
            if (this.validator) {
                this.addValidationRules(itemWrapper);
            }

            // Trigger callback
            this.onAdd(itemWrapper, index);

            return itemWrapper;
        }

        deleteItem(item) {
            const idInput = item.querySelector('input[name$="[id]"]');
            if (idInput) {
                // Find the form element
                const form = this.container.closest('form');
                if (!form) return;

                // Find or create the deleted items container inside the form
                let deletedItemsContainer = form.querySelector(this.deletedItemsContainer);
                if (!deletedItemsContainer) {
                    deletedItemsContainer = document.createElement('div');
                    deletedItemsContainer.id = this.deletedItemsContainer.replace('#', '');
                    deletedItemsContainer.style.display = 'none'; // Hide the container
                    form.appendChild(deletedItemsContainer); // Append to form instead of body
                }

                // Add hidden input for deleted item
                const deletedInput = document.createElement('input');
                deletedInput.type = 'hidden';
                deletedInput.name = 'deleted_questions[]';
                deletedInput.value = idInput.value;
                deletedItemsContainer.appendChild(deletedInput);
            }

            // Rest of the delete logic...
            if (this.validator) {
                item.querySelectorAll('input[name*="title"]').forEach(input => {
                    try {
                        this.validator.removeField(input.name);
                    } catch (error) {
                        console.log(`Error removing validation for field: ${input.name}`, error);
                    }
                });
            }

            item.remove();
            this.updateAllIndices();
            this.onDelete(item);
        }

        getNextIndex() {
            const items = this.itemsContainer.querySelectorAll('[data-repeater-item]');
            return items.length;
        }
        updateAllIndices() {
            const items = this.itemsContainer.querySelectorAll('[data-repeater-item]');
            items.forEach((item, index) => {
                // Update title inputs for each locale
                this.locales.forEach(locale => {
                    const input = item.querySelector(`[data-locale="${locale}"]`);
                    if (input) {
                        input.name = `questions[${index}][title][${locale}]`;
                    }
                });

                // Update weight input
                const weightInput = item.querySelector('input[name$="[weight]"]');
                if (weightInput) {
                    weightInput.name = `questions[${index}][weight]`;
                }

                // Update ID field if exists
                const idInput = item.querySelector('input[name$="[id]"]');
                if (idInput) {
                    idInput.name = `questions[${index}][id]`;
                }
            });

            // Trigger callback
            this.onUpdate(items);
        }

        addValidationRules(element) {
            if (!this.validator) return;

            // Add title validation
            element.querySelectorAll('input[name*="title"].validate-required').forEach(input => {
                this.validator.addField(input.name, this.validationRules.title);
            });

            // Add weight validation
            element.querySelectorAll('input[name$="[weight]"].validate-required').forEach(input => {
                this.validator.addField(input.name, this.validationRules.weight);
            });
        }

        setupValidation() {
            if (!this.validator) return;

            const items = this.itemsContainer.querySelectorAll('[data-repeater-item]');
            items.forEach(item => this.addValidationRules(item));

            // Add total weight validation only if there are items
            if (items.length > 0) {
                this.addTotalWeightValidation();
            }
        }

        addTotalWeightValidation() {
            if (!this.validator) return;

            // Add a hidden input to validate total weight
            let hiddenInput = this.container.querySelector('input[name="total_weight_validator"]');
            if (!hiddenInput) {
                hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'total_weight_validator';
                hiddenInput.value = '0';
                this.container.appendChild(hiddenInput);
            }

            // Enhanced validation with strict checking
            this.validator.addField('total_weight_validator', {
                validators: {
                    callback: {
                        message: 'Total weight must equal 100',
                        callback: (input) => {
                            const weightInputs = this.itemsContainer.querySelectorAll(
                                'input[name$="[weight]"]');
                            const totalWeight = Array.from(weightInputs)
                                .reduce((sum, input) => sum + (parseInt(input.value) || 0), 0);
                            hiddenInput.value = totalWeight;

                            if (totalWeight !== 100) {
                                this.validator.updateMessage('total_weight_validator', 'callback',
                                    `Total weight must equal 100. Current total: ${totalWeight}`);
                                this.updateTotalWeightDisplay(totalWeight);
                                return false;
                            }

                            this.updateTotalWeightDisplay(totalWeight);
                            return true;
                        }
                    }
                }
            });

            // Create total weight display
            this.createTotalWeightDisplay();

            // Add weight input validation
            this.itemsContainer.addEventListener('input', (e) => {
                if (e.target.matches('input[name$="[weight]"]')) {
                    this.validator.revalidateField('total_weight_validator');
                }
            });
        }

        createTotalWeightDisplay() {
            let totalDisplay = this.container.querySelector('.total-weight-display');
            if (!totalDisplay) {
                totalDisplay = document.createElement('div');
                totalDisplay.className = 'total-weight-display mt-5 fw-bold fs-6';
                this.addButtonId && document.querySelector(this.addButtonId).before(totalDisplay);
            }
        }

        updateTotalWeightDisplay(total) {
            const totalDisplay = this.container.querySelector('.total-weight-display');
            if (totalDisplay) {
                const color = total === 100 ? 'text-success' : 'text-danger';
                totalDisplay.className = `total-weight-display mt-5 fw-bold fs-6 ${color}`;
                totalDisplay.textContent = `Current Total Weight: ${total}%`;
            }
        }

        cleanup() {
            if (this.validator) {
                try {
                    // Clean up weight validator if it exists
                    if (this.validator.fields['total_weight_validator']) {
                        this.validator.removeField('total_weight_validator');
                    }

                    // Clean up field validations
                    this.itemsContainer.querySelectorAll('input[name*="title"], input[name$="[weight]"]')
                        .forEach(input => {
                            this.removeValidationField(input.name);
                        });

                    // Remove total weight display if it exists
                    const totalDisplay = this.container.querySelector('.total-weight-display');
                    if (totalDisplay) {
                        totalDisplay.remove();
                    }
                } catch (error) {
                    console.log('Error during cleanup:', error);
                }
            }
        }

        removeValidationField(fieldName) {
            try {
                if (this.validator.fields[fieldName]) {
                    this.validator.removeField(fieldName);
                }
            } catch (error) {
                console.log(`Skipping cleanup for field: ${fieldName}`, error);
            }
        }
    }
</script>
