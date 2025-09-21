<script>
    var selectedItemsModelsRows = [];
    var selectedItemModelsData = [];

    const columnDefs = [{
            data: 'id',
            name: 'id',
            visible: false,
            searchable: false
        },
        {
            data: function(row, type, set) {
                if (type === 'display') {
                    if (row.name && row.name['en']) {
                        return row.name['en'];
                    } else {
                        return 'NA'; // Placeholder text if the value is missing
                    }
                }

                return '';
            },
            createdCell: function(td, cellData, rowData, row, col) {
                $(td).css({

                    "color": "blue",
                    "cursor": "pointer"
                });
            },
            name: `name->${'en'}`, // Use template literals for dynamic values
            // name: `name->${'en'}`, // Use template literals for dynamic values
        },
        {
            data: function(row, type, set) {
                if (type === 'display') {
                    if (row.name && row.name['ar']) {
                        return row.name['ar'];
                    } else {
                        return 'NA'; // Placeholder text if the value is missing
                    }
                }
                return '';
            },
            name: `name->${'ar'}`,

            // Use template literals for dynamic values
            // name: `name->${'en'}`, // Use template literals for dynamic values
        },
        {
            data: 'email',
            name: 'email',
        },
        {
            data: 'mobile',
            name: 'mobile',
        },

        {
            data: 'permissions',
            name: 'permissions.name',
            searchable: true,
            render: function(data, type, row, meta) {
                if (type === 'display') {
                    var template = '';
                    // console.log(row);

                    if (Array.isArray(row.roles) && row.roles.length > 0) {
                        console.log(row.roles);
                        row.roles.forEach(element => {
                            template +=
                                '<span class="badge badge-light-success fs-7 m-1"">' +
                                element
                                .name + '</span>';
                        });
                    }

                    if (Array.isArray(data) && data.length > 0) {
                        data.forEach(element => {
                            template +=
                                '<span class="badge badge-light-primary fs-7 m-1"">' +
                                element
                                .name + '</span>';
                        });
                    }

                    return template;
                }
                return data;
            },
        },
        // {
        //     data: 'roles',
        //     name: 'roles.name',
        //     searchable: true,
        //     render: function(data, type) {
        //         if (type === 'display') {
        //             if (Array.isArray(data) && data.length > 0) {
        //                 return '<span class="badge badge-light-primary fs-7 m-1"">' + data[0]
        //                     .name + '</span>';
        //             }
        //             return data;
        //         }
        //         return data;
        //     },
        // },
        {
            data: 'active',
            name: 'active',
            render: function(data, type) {
                if (type === 'display') {
                    if (Boolean(data) == true)
                        return '<span class="badge badge-light-primary fs-7 m-1"">Active</span>';
                    else
                        return '<span class="badge badge-light-danger fs-7 m-1"">Disabled</span>';
                }
                return data;
            }
        },
        {
            data: 'last_login_at',
            name: 'last_login_at',
            render: function(data, type) {
                if (type === 'display') {
                    return '<div class="badge badge-light fw-bold">' + data + '</div>'
                }
                return data;
            }
        },
        {
            data: {
                _: 'created_at.display',
                sort: 'created_at.timestamp',
            },
            name: 'created_at',
        },
        {
            data: 'action',
            name: 'action',
            className: 'text-end',
            orderable: false,
            searchable: false
        }
    ];
    var datatable = createDataTable('#kt_table_items_model', columnDefs,
        "{{ route($_model::ui['route'] . '.index') }}", [
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

    $('#kt_table_items_model tbody').on('click', '.row-checkbox', function() {
        var $row = $(this).closest('tr');
        var rowData = datatable.row($row).data();
        var rowIndex = selectedItemsModelsRows.indexOf(rowData.id);

        if (this.checked && rowIndex === -1) {
            selectedItemsModelsRows.push(rowData.id);
        } else if (!this.checked && rowIndex !== -1) {
            //console.log(data);
            selectedItemsModelsRows.splice(rowIndex, 1);

        }

        $row.toggleClass('selected');
        datatable.row($row).select(this.checked);
        if (selectedItemsModelsRows.length == 0)
            $('#selectedItemsModelsRowsCount').html("");
        else
            $('#selectedItemsModelsRowsCount').html("(" + selectedItemsModelsRows.length + ")");


        $('[name="selectedCaptin"]').val(selectedItemsModelsRows.join(','));

    });

    // Restore selected rows when page changes
    datatable.on('draw.dt', function() {
        datatable.rows().every(function(rowIdx, tableLoop, rowLoop) {
            var rowData = this.data();
            if (selectedItemsModelsRows.includes(rowData.id)) {
                this.select();
            }
        });
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
