<script>
    class RegularFormHandler {
        constructor(formSelector, submitButtonSelector, options = {}) {
            this.formSelector = formSelector;
            this.submitButtonSelector = submitButtonSelector;
            this.form = null;
            this.submitButton = null;
            this.validator = null;
            this.validationManager = null;
            this.hasFileUploads = options.hasFileUploads || false;
        }

        init() {
            try {
                if (!this.findElements()) {
                    return false;
                }

                // Check if form has file inputs
                if (this.form.querySelector('input[type="file"]')) {
                    this.hasFileUploads = true;

                    // Ensure enctype is set properly
                    if (this.form.getAttribute('enctype') !== 'multipart/form-data') {
                        this.form.setAttribute('enctype', 'multipart/form-data');
                        console.info('Setting enctype="multipart/form-data" for file uploads');
                    }
                }

                this.initializeValidation();
                this.initializeDatePickers();
                this.setupSubmitHandler();
                return true;
            } catch (error) {
                console.error('Error initializing FormHandler:', error);
                return false;
            }
        }

        findElements() {
            // Same as before
            this.form = document.querySelector(this.formSelector);
            this.submitButton = document.querySelector(this.submitButtonSelector);

            if (!this.form) {
                console.error(`Form not found for selector: ${this.formSelector}`);
                return false;
            }

            if (!this.submitButton) {
                console.error(`Submit button not found for selector: ${this.submitButtonSelector}`);
                return false;
            }

            return true;
        }

        initializeDatePickers() {
            // Same as before
            // Initialize past date pickers
            const pastDatePickers = this.form.querySelectorAll('.date-picker-past');
            pastDatePickers.forEach(element => {
                flatpickr(element, {
                    dateFormat: "Y-m-d",
                    maxDate: "today",
                    allowInput: true
                });
            });

            // Initialize future date pickers
            const futureDatePickers = this.form.querySelectorAll('.date-picker-future');
            futureDatePickers.forEach(element => {
                flatpickr(element, {
                    dateFormat: "Y-m-d",
                    minDate: "today",
                    allowInput: true
                });
            });

            // Initialize regular date pickers
            const datePickers = this.form.querySelectorAll(
                '.date-picker:not(.date-picker-past):not(.date-picker-future)');
            datePickers.forEach(element => {
                flatpickr(element, {
                    dateFormat: "Y-m-d",
                    allowInput: true
                });
            });
        }

        initializeValidation() {
            // Same as before
            this.validationManager = new FormValidationManager(this.form);
            this.validator = this.validationManager.applyValidationRules();
            return this.validator;
        }

        setupSubmitHandler() {
            // Same as before
            this.submitButton.addEventListener('click', (e) => this.handleSubmit(e));
        }

        async handleSubmit(e) {
            e.preventDefault();

            try {
                if (this.validator) {
                    const status = await this.validator.validate();
                    if (status === 'Valid') {
                        await this.processFormSubmission();
                    } else {
                        this.showValidationError();
                    }
                } else {
                    console.warn('No validator provided, proceeding with submission.');
                    await this.processFormSubmission();
                }
            } catch (error) {
                console.error('Error during form submission:', error);
                this.showSubmissionError();
            }
        }

        async processFormSubmission() {
            try {
                // Special handling for forms with file uploads
                if (this.hasFileUploads) {
                    // Process file inputs before submitting
                    this.handleFileInputs();
                }

                // Submit the form
                this.form.submit();
            } catch (error) {
                console.error('Error submitting form:', error);
                this.showSubmissionError();
            }
        }

        handleFileInputs() {
            // Handle avatar_remove field which needs special processing
            const fileInputs = this.form.querySelectorAll('input[type="file"]');

            fileInputs.forEach(fileInput => {
                const name = fileInput.getAttribute('name');
                const removeInput = this.form.querySelector(`input[name="${name}_remove"]`);

                if (removeInput) {
                    // If file is selected, ensure remove flag is unset
                    if (fileInput.files && fileInput.files.length > 0) {
                        removeInput.value = '0';
                    }

                    // Check if the remove button was clicked
                    const removeBtn = fileInput.closest('[data-kt-image-input]')?.querySelector(
                        '[data-kt-image-input-action="remove"]');
                    if (removeBtn && removeBtn.classList.contains('active')) {
                        removeInput.value = '1';
                    }
                }
            });
        }

        showValidationError() {
            // Same as before
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

        showSubmissionError() {
            // Same as before
            Swal.fire({
                text: "An error occurred during form submission. Please try again.",
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: "Ok, got it!",
                customClass: {
                    confirmButton: "btn btn-primary"
                }
            });
        }

        static initialize(formSelector, submitButtonSelector, options = {}) {
            const handler = new RegularFormHandler(formSelector, submitButtonSelector, options);
            return handler.init();
        }
    }
</script>
