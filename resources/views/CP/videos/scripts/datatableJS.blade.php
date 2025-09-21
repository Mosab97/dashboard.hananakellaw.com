<script>
    var selectedItemsModelsRows = [];
    var selectedItemModelsData = [];
    const columnDefs = [
        {
            data: 'id',
            name: 'id',
            render: function(data, type, row) {
                return data || 'N/A';
            }
        },
        {
            data: 'title',
            name: 'title',
            orderable: true,
            searchable: true,
            render: function(data, type, row) {
                return data || 'N/A';
            }
        },
        {
            data: 'path',
            name: 'path',
            orderable: false,
            searchable: true,
            render: function(data, type, row) {
                return data || 'N/A';
            }
        },
        {
            data: 'description',
            name: 'description',
            orderable: false,
            searchable: true,
            render: function(data, type, row) {
                return data || 'N/A';
            }
        },
        {
            data: 'active',
            name: 'active',
            orderable: true,
            searchable: true,

        },
        {
            data: 'created_at',
            name: 'created_at',
            orderable: true,
            searchable: false,
            render: function(data, type, row) {
                if (data && data.display) {
                    return data.display;
                }
                return data || 'N/A';
            }
        },
        {
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false,
            className: 'text-end'
        }
    ];
    var datatable = createDataTable('#kt_table_items_model', columnDefs,
        "{{ route($config['full_route_name'] . '.index') }}", [
            [0, "ASC"]
        ]);
    datatable.on('draw', function() {
        KTMenu.createInstances();
    });
    datatable.on('responsive-display', function() {
        KTMenu.createInstances();
    });


    $('#kt_table_items_model').find('#select-all').on('click', function() {
        $('#kt_table_items_model').find('.row-checkbox').click();
    });




    const filterSearch = document.querySelector('[data-kt-table-filter="search"]');
    filterSearch.onkeydown = debounce(keyPressCallback, 400);

    function keyPressCallback() {
        datatable.draw();
    }
</script>


<script>
    $(document).on('click', '#filterBtn', function(e) {
        e.preventDefault();
        datatable.ajax.reload();
    });

    $(document).on('click', '#resetFilterBtn', function(e) {
        e.preventDefault();
        $('#filter-form').trigger('reset');
        $('.datatable-input').each(function() {
            if ($(this).hasClass('filter-selectpicker')) {
                $(this).val('');
                $(this).trigger('change');
            }
            if ($(this).hasClass('flatpickr-input')) {
                const fp = $(this)[0]._flatpickr;
                fp.clear();
            }
        });
        datatable.ajax.reload();
    });

    $(document).on('click', '#exportBtn', function(e) {
        e.preventDefault();
        const url = $(this).data('export-url');
        console.log(url);
        const myUrlWithParams = new URL(url);

        const parameters = filterParameters();
        //myUrlWithParams.searchParams.append('params',JSON.stringify( parameters))
        Object.keys(parameters).map((key) => {
            myUrlWithParams.searchParams.append(key, parameters[key]);
        });
        console.log(myUrlWithParams);
        window.open(myUrlWithParams, "_blank");

    });
</script>
