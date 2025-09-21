<script>
    const columnDefsproductSizes = [
        {
            data: 'id',
            name: 'id',
            orderable: false,
            searchable: false,
            render: function(data, type, row) {
                return data || 'N/A';
            }
        },
        {
            data: 'size_name',
            name: 'size_name',
            orderable: true,
            searchable: true,
            render: function(data, type, row) {
                return data || 'N/A';
            }
        },
        {
            data: 'price',
            name: 'price',
            orderable: false,
            searchable: true,
            render: function(data, type, row) {
                return data || 'N/A';
            }
        },


        {
            data: 'active',
            name: 'active',
            orderable: false,
            searchable: false,
            render: function(data, type, row) {
                return data ? '<span class="badge badge-light-success">Active</span>' : '<span class="badge badge-light-danger">Inactive</span>';
            }
        },
        // {
        //     data: 'created_at',
        //     name: 'created_at',
        //     orderable: true,
        //     searchable: false,
        //     render: function(data, type, row) {
        //         if (data && data.display) {
        //             return data.display;
        //         }
        //         return data || 'N/A';
        //     }
        // },

        {
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false,
            className: 'text-end'
        }
    ];
    var datatableproductSizes = createDataTable('#kt_table_{{ $config_product_sizes['singular_key'] }}', columnDefsproductSizes,
        "{{ route($config_product_sizes['full_route_name'] . '.index', ['product' => $product_id]) }}",
        [
            [0, "ASC"]
        ]);
    datatableproductSizes.on('draw', function() {
        KTMenu.createInstances();
    });
    datatableproductSizes.on('responsive-display', function() {
        KTMenu.createInstances();
    });





    // // Restore selected rows when page changes
    // datatableproductSizes.on('draw.dt', function() {
    //     datatableproductSizes.rows().every(function(rowIdx, tableLoop, rowLoop) {
    //         var rowData = this.data();
    //         if (selectedItemsModelsRows.includes(rowData.id)) {
    //             this.select();
    //         }
    //     });
    // });
    const filterSearchproductSizes = document.querySelector(
        '[data-kt-{{ $config_product_sizes['singular_key'] }}-table-filter="search"]');
    filterSearchproductSizes.onkeydown = debounce(keyPressCallback, 400);

    function keyPressCallback() {
        datatableproductSizes.draw();
    }
</script>
