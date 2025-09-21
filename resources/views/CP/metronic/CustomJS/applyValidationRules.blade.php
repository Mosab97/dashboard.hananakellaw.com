<script>
    // Logger class to handle all logging operations
    class ValidationLogger {
        static log(message, data = null) {
            const timestamp = new Date().toISOString();
            // console.log(`[${timestamp}] ${message}`, data || '');
        }

        static error(message, error) {
            const timestamp = new Date().toISOString();
            console.error(`[${timestamp}] ERROR: ${message}`, error);
        }
    }

    // Validation rules configuration object
    const VALIDATION_RULES = {
        required: {
            rule: 'notEmpty',
            getMessage: (label) => `${label} ` + "{{ t('is required') }}"
        },
        number: {
            rule: 'numeric',
            options: {
                decimalSeparator: '.'
            },
            message: "{{ t('The value must be a valid number') }}"
        },
        min0: {
            rule: 'greaterThan',
            options: {
                min: 0
            },
            message: "{{ t('The value must be greater than or equal to 0') }}"
        },
        min1: {
            rule: 'greaterThan',
            options: {
                min: 1
            },
            message: "{{ t('The value must be greater than or equal to 1') }}"
        },
        email: {
            rule: 'emailAddress',
            message: "{{ t('The value must be a valid email address') }}"
        },
        date: {
            rule: 'date',
            message: "{{ t('The value must be a valid date') }}"
        },
        dateMin: {
            rule: 'date',
            options: {
                min: 'today'
            },
            message: "{{ t('The date must not be in the past') }}"
        },
        dateMax: {
            rule: 'date',
            options: {
                max: 'today'
            },
            message: "{{ t('The date must not be in the future') }}"
        },
        datetime: {
            rule: 'date',
            options: {
                format: 'YYYY-MM-DD HH:mm'
            },
            message: "{{ t('The value must be a valid date and time') }}"
        },
        phone: {
    rule: 'regexp',
    options: {
        regexp: '^\\+?[0-9]{9,12}$'
    },
    message: "{{ t('Please enter a valid phone number (9-12 digits)') }}"
},
    };

    // Validation class mapping
    const VALIDATION_CLASSES = {
        'validate-required': 'required',
        'validate-number': 'number',
        'validate-min-0': 'min0',
        'validate-min-1': 'min1',
        'validate-email': 'email',
        'date-picker': 'date',
        'date-picker-future': 'dateMin',
        'date-picker-dob': 'dateMax',
        'date-picker-past': 'dateMax',
        'date-time-picker': 'datetime',
        'validate-phone': 'phone',

    };

    class FormValidationManager {
        constructor(form, validatorFields = {}) {
            try {
                ValidationLogger.log('Initializing FormValidationManager');
                this.form = form;
                this.validatorFields = validatorFields;
                this.validator = this.initializeValidator();
                ValidationLogger.log('FormValidationManager initialized successfully');
            } catch (error) {
                ValidationLogger.error('Failed to initialize FormValidationManager', error);
                throw error;
            }
        }

        initializeValidator() {
            try {
                ValidationLogger.log('Initializing form validator');
                const validator = FormValidation.formValidation(
                    this.form, {
                        fields: this.validatorFields,
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
                ValidationLogger.log('Form validator initialized successfully');
                return validator;
            } catch (error) {
                ValidationLogger.error('Failed to initialize validator', error);
                throw error;
            }
        }

        applyValidationRules() {
            try {
                ValidationLogger.log('Starting to apply validation rules');
                $(this.form).find('input, select, textarea').each((index, element) => {
                    try {
                        const field = $(element);
                        const fieldName = field.attr('name');

                        if (!fieldName) {
                            ValidationLogger.log('Skipping field without name attribute', element);
                            return;
                        }

                        ValidationLogger.log(`Processing field: ${fieldName}`);
                        const validators = this.buildValidatorsForField(field);

                        if (Object.keys(validators).length > 0) {
                            ValidationLogger.log(`Adding validators for field: ${fieldName}`, validators);
                            this.validator.addField(fieldName, {
                                validators
                            });
                        }
                    } catch (fieldError) {
                        ValidationLogger.error(`Error processing field at index ${index}`, fieldError);
                    }
                });

                ValidationLogger.log('Validation rules applied successfully');
                return this.validator;
            } catch (error) {
                ValidationLogger.error('Failed to apply validation rules', error);
                throw error;
            }
        }

        buildValidatorsForField(field) {
            try {
                const fieldName = field.attr('name');
                ValidationLogger.log(`Building validators for field: ${fieldName}`);

                const validators = {};
                const fieldClasses = field[0].className.split(/\s+/);

                fieldClasses.forEach(className => {
                    try {
                        if (VALIDATION_CLASSES[className]) {
                            const ruleKey = VALIDATION_CLASSES[className];
                            const rule = VALIDATION_RULES[ruleKey];
                            const label = this.getFieldLabel(field);

                            ValidationLogger.log(`Applying rule: ${ruleKey} for field: ${fieldName}`);

                            // Special handling for date fields
                            if (rule.rule === 'date') {
                                validators[rule.rule] = {
                                    message: rule.getMessage ? rule.getMessage(label) :
                                        `Please select a valid ${label.toLowerCase()}`,
                                    format: rule.options?.format || 'YYYY-MM-DD',
                                    ...rule.options
                                };

                                // Add required validator if the field has validate-required class
                                if (fieldClasses.includes('validate-required')) {
                                    validators.notEmpty = {
                                        message: `${label} ` + "{{ t('is required') }}"
                                    };
                                }
                            } else {
                                validators[rule.rule] = {
                                    message: rule.rule === 'notEmpty' ?
                                        rule.getMessage(label) : rule.message,
                                    ...rule.options
                                };
                            }

                            ValidationLogger.log(`Validator added for ${fieldName} with rule ${ruleKey}`,
                                validators[rule.rule]);
                        }
                    } catch (ruleError) {
                        ValidationLogger.error(`Error applying validation rule: ${className}`, ruleError);
                    }
                });

                return validators;
            } catch (error) {
                ValidationLogger.error('Failed to build validators for field', error);
                throw error;
            }
        }

        getFieldLabel(field) {
            try {
                const fieldName = field.attr('name');
                ValidationLogger.log(`Getting label for field: ${fieldName}`);

                // Try to get label from the closest label element
                const label = field.closest('.fv-row').find('label').text().trim();

                // Remove optional text in parentheses and any trailing colon
                const processedLabel = label.replace(/\s*\([^)]*\)/g, '').replace(/:$/, '');

                ValidationLogger.log(`Label found for ${fieldName}: ${processedLabel}`);
                return processedLabel;
            } catch (error) {
                ValidationLogger.error('Failed to get field label', error);
                throw error;
            }
        }

        getValidator() {
            try {
                ValidationLogger.log('Retrieving validator instance');
                return this.validator;
            } catch (error) {
                ValidationLogger.error('Failed to get validator', error);
                throw error;
            }
        }
    }
</script>
