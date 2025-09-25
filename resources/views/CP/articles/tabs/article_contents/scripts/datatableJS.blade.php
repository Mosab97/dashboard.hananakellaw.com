<script>
    var selectedItemModelsData = [];
    const columnDefsArticleContents = [{
            data: 'title',
            name: 'title',
            render: function(data, type, row) {
                return data || 'NA';
            }
        },
        {
            data: 'features',
            name: 'features',
            render: function(data, type, row) {
                return data || 'NA';
            }
        },
        {
            data: 'active',
            name: 'active',
        },

        {
            data: 'created_at',
            name: 'created_at',
            render: function(data, type, row) {
                return data?.display || 'NA';
            }
        },
        {
            data: 'action',
            name: 'action',
            className: 'text-end',
            orderable: false,
            searchable: false
        }
    ];
    var datatableArticleContents = createDataTable('#kt_table_article_contents', columnDefsArticleContents,
        "{{ route($config['full_route_name'] . '.index', ['article' => $_model->id]) }}",
        [
            [0, "ASC"]
        ]);
    datatableArticleContents.on('draw', function() {
        KTMenu.createInstances();
    });
    datatableArticleContents.on('responsive-display', function() {
        KTMenu.createInstances();
    });





    const filterSearchArticleContents = document.querySelector(
        '[data-kt-{{ $config['singular_key'] }}-table-filter="search"]');
    filterSearchArticleContents.onkeydown = debounce(keyPressCallback, 400);

    function keyPressCallback() {
        datatableArticleContents.draw();
    }
</script>
