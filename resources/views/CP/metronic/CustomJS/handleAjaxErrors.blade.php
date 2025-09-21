<script>
    // ErrorTypes enum for consistent error categorization
    const ErrorTypes = {
        VALIDATION: 'VALIDATION',
        AUTHENTICATION: 'AUTHENTICATION',
        SERVER: 'SERVER',
        NETWORK: 'NETWORK',
        CLIENT: 'CLIENT'
    };

    // Single Responsibility: Handles UI element updates
    class UIStateManager {
        static resetValidationErrors() {
            $('.validation-error').remove();
        }

        static displayValidationError(fieldName, message) {
            const fieldElement = $(`[name="${fieldName}"]`);
            if (fieldElement.length > 0) {
                fieldElement.after(`<span class="validation-error text-danger">${message}</span>`);
            }
        }

        static resetLoadingState(submitButton) {
            if (submitButton) {
                submitButton.attr('data-kt-indicator', 'off');
                submitButton.prop('disabled', false);
            } else {
                const subBtn = $('button[data-kt-indicator="on"]');
                if (subBtn.length) {
                    subBtn.attr('data-kt-indicator', 'off');
                    subBtn.prop('disabled', false);
                }
            }
        }

        static resetDraggedElement(originalContainer, draggedElement) {
            if (originalContainer && draggedElement) {
                originalContainer.appendChild(draggedElement);
            }
        }

        static resetElementOpacity(element) {
            if (element) {
                element.style.opacity = '1';
            }
        }
    }

    // Single Responsibility: Handles error message formatting
    class ErrorMessageFormatter {
        static getValidationErrorMessage(response) {
            return response.message || 'There was a validation error. Please check your inputs and try again.';
        }

        static getAuthenticationErrorMessage() {
            return 'Session expired or unauthenticated. Redirecting to login.';
        }

        static getServerErrorMessage() {
            return 'A server error occurred. Please try again later.';
        }

        static getClientErrorMessage(response) {
            return response.message || 'Bad request. Please check your input.';
        }

        static getNetworkErrorMessage() {
            return 'Network error occurred. Please check your connection and try again.';
        }
    }

    // Single Responsibility: Handles error type classification
    class ErrorTypeClassifier {
        static getErrorType(status) {
            if (status === 422) return ErrorTypes.VALIDATION;
            if (status === 401 || status === 419) return ErrorTypes.AUTHENTICATION;
            if (status === 500) return ErrorTypes.SERVER;
            if (status === 400) return ErrorTypes.CLIENT;
            if (status === 0) return ErrorTypes.NETWORK;
            return ErrorTypes.SERVER;
        }
    }

    // Main Error Handler Class following Open/Closed Principle
    class AjaxErrorHandler {
        constructor(options = {}) {
            this.options = {
                elementToReset: null,
                originalContainer: null,
                draggedElement: null,
                submitButton: null,
                onValidationError: null,
                afterErrorHandle: null,
                ...options
            };
        }

        async handle(xhr, status, error) {
            console.error('Ajax request failed.', {
                status: xhr.status,
                error,
                response: xhr.responseJSON
            });

            const errorType = ErrorTypeClassifier.getErrorType(xhr.status);
            await this.handleByType(errorType, xhr);

            this.resetUIState();
            this.executeCallbacks(xhr, status, error);
        }

        async handleByType(errorType, xhr) {
            switch (errorType) {
                case ErrorTypes.VALIDATION:
                    await this.handleValidationError(xhr);
                    break;
                case ErrorTypes.AUTHENTICATION:
                    await this.handleAuthenticationError();
                    break;
                case ErrorTypes.SERVER:
                    this.handleServerError(xhr);
                    break;
                case ErrorTypes.CLIENT:
                    this.handleClientError(xhr);
                    break;
                case ErrorTypes.NETWORK:
                    this.handleNetworkError();
                    break;
            }
        }

        async handleValidationError(xhr) {
            UIStateManager.resetValidationErrors();
            const errors = xhr.responseJSON?.errors;

            if (errors && typeof errors === 'object') {
                Object.entries(errors).forEach(([field, messages]) => {
                    if (messages && Array.isArray(messages)) {
                        const fieldName = field.replace(/\.(\w+)/g, '[$1]');
                        UIStateManager.displayValidationError(fieldName, messages[0]);
                    }
                });
            }

            toastr.error(ErrorMessageFormatter.getValidationErrorMessage(xhr.responseJSON));

            if (this.options.onValidationError) {
                await this.options.onValidationError(errors);
            }
        }

        async handleAuthenticationError() {
            const currentUrl = window.location.href;
            toastr.error(ErrorMessageFormatter.getAuthenticationErrorMessage());
            window.location.href = `{{ route('login') }}?redirect=${encodeURIComponent(currentUrl)}`;
        }

        handleServerError(xhr) {
            const message = xhr.responseJSON?.message || ErrorMessageFormatter.getServerErrorMessage();
            toastr.error(message);
        }

        handleClientError(xhr) {
            const message = xhr.responseJSON?.message || ErrorMessageFormatter.getClientErrorMessage(xhr
                .responseJSON);
            toastr.error(message);
        }

        handleNetworkError() {
            toastr.error(ErrorMessageFormatter.getNetworkErrorMessage());
        }

        resetUIState() {
            UIStateManager.resetElementOpacity(this.options.elementToReset);
            UIStateManager.resetDraggedElement(this.options.originalContainer, this.options.draggedElement);
            UIStateManager.resetLoadingState(this.options.submitButton);
        }

        executeCallbacks(xhr, status, error) {
            if (this.options.afterErrorHandle) {
                this.options.afterErrorHandle(xhr, status, error);
            }
        }
    }

    // Usage example:
    const handleAjaxErrors = (xhr, status, error, options = {}) => {
        const errorHandler = new AjaxErrorHandler(options);
        errorHandler.handle(xhr, status, error).catch(console.error);
    };
</script>



{{-- handleAjaxErrors --}}
{{-- <script>
    // Enhanced AJAX error handler with UI update support
    const handleAjaxErrors = function(xhr, status, error, options = {}) {
        console.error('Ajax request failed. Status:', xhr.status, 'Error:', error);

        const {
            elementToReset = null,
                originalContainer = null,
                draggedElement = null,
                submitButton = null,
                onValidationError = null,
                afterErrorHandle = null
        } = options;

        // Handle different error statuses
        if (xhr.status === 401 || xhr.status === 419) {
            let currentUrl = window.location.href;
            console.error('User is unauthenticated or session expired.');
            toastr.error('Session expired or unauthenticated. Redirecting to login.');
            window.location.href = "{{ route('login') }}?redirect=" + encodeURIComponent(currentUrl);
            return;
        }

        // New error message handling logic
        let errorMessage = 'An unexpected error occurred. Please try again.';

        if (xhr.responseJSON) {
            if (xhr.status === 422) {
                // Clear previous validation errors
                $('.validation-error').remove();
                let errors = xhr.responseJSON.errors;
                console.log('Processing validation errors:', errors);

                // Process validation errors
                if (errors && typeof errors === 'object') {
                    for (const [field, messages] of Object.entries(errors)) {
                        if (messages && Array.isArray(messages)) {
                            let fieldName = field.replace(/\.(\w+)/g, '[$1]');
                            let fieldElement = $(`[name="${fieldName}"]`);
                            if (fieldElement.length > 0) {
                                fieldElement.after(
                                    `<span class="validation-error text-danger">${messages[0]}</span>`);
                            }
                        }
                    }
                }

                // Use server message if available, otherwise use default message
                errorMessage = xhr.responseJSON.message ||
                    'There was a validation error. Please check your inputs and try again.';

                // Call custom validation error handler if provided
                if (onValidationError && typeof onValidationError === 'function') {
                    onValidationError(errors);
                }
            } else {
                // Priority order for error messages remains the same for other status codes
                if (xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON.errors && xhr.responseJSON.errors.general) {
                    errorMessage = Array.isArray(xhr.responseJSON.errors.general) ?
                        xhr.responseJSON.errors.general[0] :
                        xhr.responseJSON.errors.general;
                } else if (xhr.status === 500) {
                    errorMessage = 'A server error occurred. Please try again later.';
                } else if (xhr.status === 400) {
                    errorMessage = xhr.responseJSON.message || 'Bad request. Please check your input.';
                }
            }
        }

        // Show error message only once
        toastr.error(errorMessage);

        // Reset element opacity if provided
        if (elementToReset) {
            elementToReset.style.opacity = '1';
        }

        // Revert dragged element if provided
        if (originalContainer && draggedElement) {
            originalContainer.appendChild(draggedElement);
        }

        // Reset submit button if provided
        if (submitButton) {
            submitButton.attr('data-kt-indicator', 'off');
            submitButton.prop('disabled', false);
        } else {
            // Legacy support for existing code
            var subBtn = $('button[data-kt-indicator="on"]');
            if (subBtn.length) {
                subBtn.attr('data-kt-indicator', 'off');
                subBtn.prop('disabled', false);
            }
        }

        // Execute after error callback if provided
        if (afterErrorHandle && typeof afterErrorHandle === 'function') {
            afterErrorHandle(xhr, status, error);
        }
    };
</script> --}}
