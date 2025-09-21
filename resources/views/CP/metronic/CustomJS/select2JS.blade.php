<script>
    function getSelect2WithoutSearchOrPaginate(model, selector, placeholder = 'Select an option', searchBy = null,
        selectedId = null, operator = 'and') {
        // console.log('Starting Select2 without search/paginate', {
        //     model,
        //     selector,
        //     searchBy,
        //     selectedId,
        //     operator
        // });

        return new Promise((resolve, reject) => {
            $.ajax({
                url: "{{ route('getSelect2WithoutSearchOrPaginate') }}",
                type: 'POST',
                data: {
                    model: model,
                    searchBy: searchBy,
                    operator: operator
                },
                success: function(data) {
                    // console.log('Data fetched successfully', {
                    //     count: data.length
                    // });

                    // Clear and initialize select
                    $(selector).empty().append('<option></option>');

                    // Add options
                    $.each(data, function(key, item) {
                        const selected = item.id == selectedId ? 'selected' : '';
                        $(selector).append(
                            `<option value="${item.id}" ${selected}>${item.current_local_name}</option>`
                        );
                    });

                    $(selector).trigger('change');
                    resolve(data);
                },
                error: function(xhr, status, error) {
                    console.error('Select2 fetch error:', error);
                    handleAjaxErrors(xhr, status, error);
                    reject(xhr);
                }
            });
        });
    }
</script>
{{-- Select 2 functions --}}
<script>
    function getSelect2(model, selector, placeholder = 'Select an option', callback = null, parent_id = null) {


        $(selector).select2({
            placeholder: placeholder,
            allowClear: true,
            ajax: {
                url: "{{ route('getSelect2') }}",
                type: 'POST',
                dataType: 'json',
                delay: 500, // Increase delay to 500ms
                quietMillis: 500, // Add quiet period
                cache: true,
                data: function(params) {
                    // console.log('AJAX request parameters:', params);
                    return {
                        q: params.term,
                        page: params.page || 1,
                        model: model,
                        parent_id: parent_id,
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.items,
                        pagination: {
                            more: (params.page * 10) < data.total_count
                        }
                    };
                },
                error: function(jqXHR, status, error) {
                    // Handle error gracefully
                    console.error('Select2 search error:', error);
                    return {
                        results: [] // Return empty results on error
                    };
                },
                transport: function(params, success, failure) {
                    // Cancel previous request if it exists
                    if (this._request && this._request.abort) {
                        this._request.abort();
                    }
                    this._request = $.ajax(params);
                    return this._request.then(success, failure);
                }
            },
            minimumInputLength: 1,
            templateResult: formatItem,
            templateSelection: formatItemSelection
        });

        if (callback) {
            $(selector).on('select2:select', function(e) {
                let selectedId = $(this).val();
                // console.log('Item selected with ID:', selectedId);
                callback(selectedId, model);
            });
        }

        function formatItem(item) {
            // console.log('Formatting item:', item);

            if (item.loading) {
                // console.log('Item is loading');
                return item.text;
            }

            let $container = $(
                "<div class='select2-result-item clearfix'>" +
                "<div class='select2-result-item__meta'>" +
                "<div class='select2-result-item__title'></div>" +
                "</div>" +
                "</div>"
            );

            $container.find(".select2-result-item__title").text(item.name);
            // console.log('Formatted item container:', $container);
            return $container;
        }

        function formatItemSelection(item) {
            // console.log('Formatting item selection:', item);
            return item.name || item.text;
        }
    }
</script>

{{-- initializeObjectivesSelect    - Tags Input --}}
<script>
    // Maintain references to initialized selects
    const initializedSelects = new Map();

    function initializeObjectivesSelect(selector, options = {}) {
        // Check if already initialized
        if (initializedSelects.has(selector)) {
            return initializedSelects.get(selector);
        }

        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        function createAndSaveTag($select, term) {
            if (!term || term.length < 2) return;

            const newTag = {
                id: term,
                text: term,
                isNew: true
            };

            // Create and select the new option
            const $option = new Option(newTag.text, newTag.id, true, true);
            $select.append($option).trigger('change');

            const $tag = $select.next().find(`.select2-selection__choice:contains('${term}')`);

            // Add loading state
            $tag.addClass('loading');
            $tag.css('opacity', '0.7');

            $.ajax({
                url: "{{ route('store-objective') }}",
                method: 'POST',
                data: {
                    name: term,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    toastr.success('New objective added successfully');
                    $($option).data('saved', true);
                    $tag.removeClass('loading').css('opacity', '1');
                },
                error: function(xhr) {
                    $option.remove();
                    $select.trigger('change');

                    if (xhr.status === 422) {
                        toastr.error(xhr.responseJSON.errors.name[0]);
                    } else {
                        toastr.error('Failed to add objective');
                    }
                }
            });
        }

        const defaultOptions = {
            tags: true,
            tokenSeparators: [','],
            placeholder: "Type to add objectives...",
            allowClear: true,
            minimumInputLength: 0,
            maximumInputLength: 100,
            maximumSelectionLength: 10,
            selectOnClose: false,
            dropdownParent: options.dropdownParent || $('body'),
            language: {
                inputTooShort: function() {
                    return "Type to search objectives...";
                },
                searching: function() {
                    return "Searching...";
                },
                noResults: function() {
                    return "Press Enter or Tab to add as new objective";
                },
                loadingMore: function() {
                    return "Loading more results...";
                }
            },
            createTag: function(params) {
                return null;
            },
            ajax: {
                url: "{{ route('get-objectives') }}",
                type: 'POST',

                dataType: 'json',
                delay: 250,
                transport: debounce(function(params, success, failure) {
                    const $request = $.ajax(params);
                    return $request.then(success).fail(failure);
                }, 250),
                data: function(params) {
                    return {
                        q: params.term || '',
                        page: params.page || 1
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    const processedData = data.data.map(item => ({
                        id: item.name,
                        text: item.name,
                        isExisting: true
                    }));
                    return {
                        results: processedData,
                        pagination: {
                            more: data.pagination.more
                        }
                    };
                },
                cache: true
            }
        };

        const finalOptions = {
            ...defaultOptions,
            ...options
        };
        const $select = $(selector).select2(finalOptions);

        // Store reference
        initializedSelects.set(selector, $select);

        // Handle keydown events for Enter and Tab
        $select.on('select2:open', function() {
            const $search = $(finalOptions.dropdownParent).find('.select2-search__field').last();

            $search.off('keydown').on('keydown', function(e) {
                const term = $.trim($search.val());

                if (e.keyCode === 13 && term) {
                    e.preventDefault();
                    e.stopPropagation();
                    createAndSaveTag($select, term);
                    $search.val('').trigger('input');
                }

                if (e.keyCode === 9 && term) {
                    e.preventDefault();
                    e.stopPropagation();
                    createAndSaveTag($select, term);
                    $search.val('').trigger('input');
                }
            });
        });

        // Load initial data when dropdown is first opened
        $select.on('select2:open', function() {
            const $dropdown = $(finalOptions.dropdownParent).find('.select2-results__options');
            if (!$dropdown.data('initialized')) {
                $dropdown.data('initialized', true);

                const select2Instance = $(this).data('select2');
                select2Instance.dataAdapter.query({
                    term: '',
                    page: 1
                }, function(data) {
                    const results = data.results || [];
                    if (results.length) {
                        select2Instance.dataAdapter.current(function(currentData) {
                            const combined = currentData.concat(results);
                            select2Instance.dataAdapter.render(combined);
                        });
                    }
                });
            }
        });

        // Handle tag selection
        $select.on('select2:select', function(e) {
            const data = e.params.data;
            if (data.isExisting) {
                $(this).next().find('.select2-search__field').focus();
            }
        });

        // Handle scroll for pagination
        const handleScroll = debounce(function(e) {
            const $this = $(this);
            if ($this.scrollTop() + $this.innerHeight() >= $this[0].scrollHeight - 50) {
                const select2Instance = $select.data('select2');
                if (select2Instance.dataAdapter.loading) return;

                const params = select2Instance.dataAdapter.query.call(select2Instance.dataAdapter);
                if (params && params.term !== undefined) {
                    select2Instance.dataAdapter.query({
                        term: params.term,
                        page: (params.page || 1) + 1
                    }, function() {});
                }
            }
        }, 150);

        $(finalOptions.dropdownParent).find('.select2-results__options').on('scroll', handleScroll);

        // Clean up
        $select.on('select2:destroy', function() {
            $(finalOptions.dropdownParent).find('.select2-results__options').off('scroll', handleScroll);
            $(finalOptions.dropdownParent).find('.select2-search__field').off('keydown');
            initializedSelects.delete(selector);
        });

        return $select;
    }

    // Function to destroy select2 instance
    function destroyObjectivesSelect(selector) {
        if (initializedSelects.has(selector)) {
            const $select = initializedSelects.get(selector);
            $select.select2('destroy');
            initializedSelects.delete(selector);
        }
    }

    // Initialize modal objectives
    function initializeModalObjectives() {
        try {
            destroyObjectivesSelect('#kt_modal_general .objectives-select');

            const modalObjectivesSelect = initializeObjectivesSelect('#kt_modal_general .objectives-select', {
                dropdownParent: $('#kt_modal_general')
            });

            $('#kt_modal_general').one('hidden.bs.modal', function() {
                destroyObjectivesSelect('#kt_modal_general .objectives-select');
            });
        } catch (error) {
            console.error('Failed to initialize modal objectives:', error);
            toastr.error('Failed to initialize objectives selector');
        }
    }

    // Document ready initialization
    $(document).ready(function() {
        // Initialize main page objectives select
        const mainObjectivesSelect = initializeObjectivesSelect('#objectives-select');
    });

    // Debug event listeners
    $(document).on('select2:opening', '.objectives-select, #objectives-select', function(e) {
        const id = $(this).attr('id') || 'modal select';
        console.debug('Select2 dropdown opening for:', id);
    });

    $(document).on('select2:select', '.objectives-select, #objectives-select', function(e) {
        const id = $(this).attr('id') || 'modal select';
        console.debug('New objective selected for:', id, e.params.data);
    });

    $(document).on('select2:close', '.objectives-select, #objectives-select', function(e) {
        const id = $(this).attr('id') || 'modal select';
        console.debug('Select2 dropdown closed for:', id);
        console.debug('Current objectives:', $(this).val());
    });
</script>

<script>
    function initializeFormSelects() {
        $('.form-select-solid').each(function() {
            // Skip if this select is part of DataTables
            if ($(this).closest('.dataTables_wrapper').length > 0 ||
                $(this).closest('.dataTables_length').length > 0 ||
                $(this).parents('.dataTables_wrapper').length > 0 ||
                $(this).hasClass('datatable-input')) {
                // Add basic Bootstrap styling
                $(this)
                    .addClass('form-select form-select-sm')
                    .removeClass('form-select-solid')
                    .removeAttr('data-control');
                return;
            }

            $(this).select2({
                dropdownParent: $(this).closest('.fv-row'),
                width: '100%',
                allowClear: true,
                placeholder: $(this).find('option:first').text(),
                minimumResultsForSearch: 0,
                searchInputPlaceholder: 'Search...',
                dropdownCssClass: 'select2-dropdown-light',
                containerCssClass: 'select2-light',
            }).on('select2:select select2:unselect', function(e) {
                if (!$(this).val() && $(this).hasClass('validate-required')) {
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });
        });
    }

    // Initialize on document ready
    $(document).ready(function() {
        initializeFormSelects();
    });

    // Re-initialize when modal is shown
    $(document).on('shown.bs.modal', function() {
        initializeFormSelects();
    });

    // Re-initialize after form reset
    $('form').on('reset', function() {
        setTimeout(initializeFormSelects, 200);
    });

    // Initialize on document ready
    $(document).ready(function() {
        initializeFormSelects();
    });

    // Re-initialize when modal is shown
    $(document).on('shown.bs.modal', function() {
        initializeFormSelects();
    });

    // Re-initialize after form reset
    $('form').on('reset', function() {
        setTimeout(initializeFormSelects, 200);
    });
</script>






{{-- modelHandlers.js --}}
<script>
    // Base handler class that all model handlers will extend
    class Select2BaseModelHandler {
        constructor(config) {
            this.model = config.model;
            this.selector = config.selector;
            this.$select = null; // Initialize as null
            this.prefix = config.prefix;
            this.fields = config.fields;
            this.relatedFields = config.relatedFields || {};
            this.placeholder = config.placeholder;
            this.initialIdLogic = config.initialIdLogic;
            this.initialized = false;
        }

        initialize() {
            if (this.initialized) return;

            // Wait for DOM to be ready
            $(document).ready(() => {
                // Initialize the jQuery element after DOM is ready
                this.$select = $(this.selector);

                if (this.$select.length === 0) {
                    console.warn(`Select element not found for selector: ${this.selector}`);
                    return;
                }

                console.log(`Initializing ${this.prefix} selector`);
                this.initializeSelector();
                this.setupInitialData();

                this.initialized = true;
            });
        }

        initializeSelector() {
            try {
                if (!this.$select || this.$select.length === 0) {
                    console.warn(`Select element not found for ${this.prefix}`);
                    return;
                }

                getSelect2(
                    this.model,
                    this.$select,
                    this.placeholder,
                    this.setDetails.bind(this)
                );

                const initialId = this.getInitialId();
                if (initialId) {
                    // console.log(`Setting initial ${this.prefix} ID: ${initialId}`);
                    this.setDetails(initialId, this.model);
                }
            } catch (error) {
                console.error(`Error initializing ${this.prefix} selector:`, error);
            }
        }

        getInitialId() {
            return this.initialIdLogic; // Use the passed logic directly
        }

        setupInitialData() {
            const jsonData = `@json(isset($_model) ? $_model : null)`;
            // console.log(`Setting up ${this.prefix} data:`, jsonData);

            try {
                const data = JSON.parse(jsonData);
                const modelId = data?.[`${this.prefix}_id`];
                if (modelId) {
                    // console.log(`Setting ${this.prefix} from data:`, modelId);
                    this.setDetails(modelId, this.model);
                }
            } catch (error) {
                console.error('Error parsing JSON data:', error);
            }
        }

        setDetails(modelId, model) {
            // console.log(`Setting ${this.prefix} details. Model ID: ${modelId}, Model: ${model}`);

            if (!modelId) {
                // console.log('No model ID provided. Clearing form fields.');
                this.clearFormFields();
                return;
            }

            // console.log(`Fetching ${this.prefix} details from server`);
            $.ajax({
                url: "{{ route('getSelect2Details') }}",
                type: 'POST',
                data: {
                    model,
                    model_id: modelId
                },
                success: this.handleDataSuccess.bind(this),
                error: this.handleAjaxErrors.bind(this)
            });
        }

        handleDataSuccess(data) {
            // console.log(`${this.prefix} data received:`, data);
            const item = data.item;

            // Set main fields
            this.setModelFields(item);

            // Set related fields
            Object.entries(this.relatedFields).forEach(([relation, fields]) => {
                if (item[relation]) {
                    this.setRelatedFields(relation, item[relation], fields);
                }
            });

            // console.log(`${this.prefix} details set successfully`);
            this.updateSelect2Display(item);
        }

        setModelFields(item) {
            // console.log('Starting to set model fields:', {
            //     item,
            //     fields: this.fields
            // });

            this.fields.forEach(field => {
                try {
                    let value;
                    let fullFieldName;

                    // Check if field contains array notation
                    if (field.includes('[') && field.includes(']')) {
                        // Extract the base field name and the key
                        const matches = field.match(/([^\[]+)\[([^\]]+)\]/);
                        if (matches) {
                            const [_, baseField, key] = matches;
                            value = item[baseField]?.[key];
                            fullFieldName = `${this.prefix}_${field}`;
                        }
                    } else {
                        // Handle regular fields
                        value = item[field];
                        fullFieldName = `${this.prefix}_${field}`;
                    }

                    const isSelect = field.includes('_id');
                    const method = isSelect ? 'setSelectField' : 'setField';

                    // console.log('Setting field:', {
                    //     field,
                    //     value,
                    //     isSelect,
                    //     method,
                    //     fullFieldName
                    // });

                    this[method](fullFieldName, value);

                    // console.log(`Successfully set ${fullFieldName} to:`, value);
                } catch (error) {
                    console.error('Error setting field:', {
                        field,
                        error: error.message,
                        stackTrace: error.stack
                    });
                }
            });

            // console.log('Completed setting all model fields');
        }

        setRelatedFields(relation, data, fields) {
            // console.log(`Setting ${relation} details:`, data);
            fields.forEach(field => {
                this.setField(`${relation}_${field}`, data[field]);
            });
        }

        clearFormFields() {
            // Clear main fields
            this.fields.forEach(field => {
                this.setField(`${this.prefix}_${field}`, '');
            });

            // Clear related fields
            Object.entries(this.relatedFields).forEach(([relation, fields]) => {
                fields.forEach(field => {
                    this.setField(`${relation}_${field}`, '');
                });
            });
        }

        setField(name, value) {
            // Handle array notation in name attribute
            const fieldSelector = name.includes('[') ?
                `[name="${name}"]` :
                `[name="${name}"]`;

            const field = $(fieldSelector);

            if (field.length) {
                if (field.is('select')) {
                    field.val(value).trigger('change');
                } else if (field.is('textarea')) {
                    field.val(value).trigger('input');
                } else {
                    field.val(value).trigger('input');
                }
                // console.log(`Set field ${name} to:`, value);
            } else {
                console.warn(`Field ${name} not found using selector: ${fieldSelector}`);
            }
        }

        setSelectField(name, value) {
            this.setField(name, value);
        }

        updateSelect2Display(item) {
            // console.log('Updating select2 display with item:', item);

            let displayName;

            // Check if name is an object (translatable)
            if (item.name && typeof item.name === 'object') {
                // Use current locale or fallback to English
                const currentLocale = document.documentElement.lang || 'en';
                displayName = item.name[currentLocale] || item.name['en'] || Object.values(item.name)[0];

                // console.log('Handling translatable name:', {
                //     name: item.name,
                //     currentLocale,
                //     selectedName: displayName
                // });
            } else {
                // Handle regular name field
                displayName = item.name || `${item.id}`;
                // console.log('Handling regular name:', displayName);
            }

            // Create and append the option
            try {
                const option = new Option(displayName, item.id, true, true);
                this.$select.append(option).trigger('change');
                // console.log('Successfully updated select2 display');
            } catch (error) {
                console.error('Error updating select2 display:', error);
            }
        }

        handleAjaxErrors(response) {
            console.error('Ajax request failed:', response);
            toastr.error(response.responseJSON?.message || 'An error occurred while fetching data');
        }
    }
</script>
