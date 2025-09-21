<script>
    class FlatpickrManager {
        static instance = null;

        static CONFIG_PRESETS = {
            default: {
                selector: '.date-picker',
                config: {
                    enableTime: false,
                    dateFormat: "Y-m-d",
                    allowInput: true
                }
            },
            dateTime: {
                selector: '.date-time-picker',
                config: {
                    enableTime: true,
                    dateFormat: "Y-m-d H:i",
                    allowInput: true
                }
            },
            dob: {
                selector: '.date-picker-dob',
                config: {
                    enableTime: false,
                    dateFormat: "Y-m-d",
                    allowInput: true,
                    maxDate: "today"
                }
            },
            future: {
                selector: '.date-picker-future',
                config: {
                    enableTime: false,
                    dateFormat: "Y-m-d",
                    allowInput: true,
                    minDate: "today"
                }
            },
            time: {
                selector: '.time-picker',
                config: {
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",
                    time_24hr: true,
                    allowInput: true
                }
            }
        };

        constructor(form = null) {
            if (FlatpickrManager.instance && !form) {
                return FlatpickrManager.instance;
            }

            this.form = form;
            this.instances = new Map();
            this.defaultOptions = {
                altFormat: "Y-m-d",
                allowInput: true,
                clearButton: true,
                locale: {
                    firstDayOfWeek: 1
                }
            };

            if (!form) {
                FlatpickrManager.instance = this;
            }
        }

        static getInstance(form = null) {
            if (form) {
                return new FlatpickrManager(form);
            }

            if (!FlatpickrManager.instance) {
                FlatpickrManager.instance = new FlatpickrManager();
            }
            return FlatpickrManager.instance;
        }

        cleanup() {
            const elements = this.form ?
                this.form.querySelectorAll('.flatpickr-input') :
                document.querySelectorAll('.flatpickr-input');

            elements.forEach(element => {
                const instance = this.instances.get(element);
                if (instance) {
                    instance.destroy();
                    this.instances.delete(element);
                }
            });
        }

        initializeAll() {
            this.cleanup();

            Object.values(FlatpickrManager.CONFIG_PRESETS).forEach(preset => {
                this.initializePreset(preset);
            });
            return this;
        }

        initializePreset({
            selector,
            config
        }) {
            const elements = this.form ?
                this.form.querySelectorAll(selector) :
                document.querySelectorAll(selector);

            elements.forEach(element => {
                if (this.instances.has(element)) return;

                const finalConfig = {
                    ...this.defaultOptions,
                    ...config,
                    onChange: (selectedDates, dateStr, instance) => {
                        this.handleDateChange(element, selectedDates, dateStr, instance);
                        if (config.onChange) {
                            config.onChange(selectedDates, dateStr, instance);
                        }
                    }
                };

                try {
                    const instance = flatpickr(element, finalConfig);
                    this.instances.set(element, instance);
                } catch (error) {
                    console.error(`Failed to initialize Flatpickr for element:`, element, error);
                }
            });
        }

        handleDateChange(element, selectedDates, dateStr, instance) {
            element.dispatchEvent(new Event('change', {
                bubbles: true
            }));
            element.dispatchEvent(new Event('input', {
                bubbles: true
            }));
        }

        static initialize(form = null) {
            return FlatpickrManager.getInstance(form).initializeAll();
        }

        static addPreset(name, selector, config) {
            FlatpickrManager.CONFIG_PRESETS[name] = {
                selector,
                config
            };
        }

        getInstance(element) {
            return this.instances.get(element);
        }

        destroy() {
            this.instances.forEach(instance => instance.destroy());
            this.instances.clear();
        }
    }

    // Global initialization
    $(document).ready(function() {
        // Initialize global flatpickr instances (non-modal)
        const globalFlatpickrManager = FlatpickrManager.initialize();

        // Handle dynamic content updates
        $(document).on('content:updated', function(e, context) {
            if (context) {
                FlatpickrManager.initialize(context);
            } else {
                globalFlatpickrManager.initializeAll();
            }
        });
    });
</script>
