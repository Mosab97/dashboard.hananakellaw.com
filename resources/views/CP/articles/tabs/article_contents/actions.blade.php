@if (Auth::user()->can($config['singular_key'] . '_edit'))
    <div class="d-flex align-items-center gap-2">
        {{-- Edit Button --}}
        <a href="{{ route($config['full_route_name'] . '.edit', [
            'article' => $_model->article_id,
            '_model' => $_model->id,
        ]) }}"
            class="btn btn-icon btn-active-light-primary w-30px h-30px btn_update_{{ $config['singular_key'] }}"
            data-bs-toggle="tooltip" title="{{ t('Edit ' . $config['singular_name']) }}">
            <span class="svg-icon svg-icon-3">
                <i class="fas fa-edit"></i>
            </span>
        </a>


        {{-- Delete Button --}}
        <a href="{{ route($config['full_route_name'] . '.delete', ['article' => $_model->article_id, '_model' => $_model->id]) }}"
            class="btn btn-icon btn-active-light-danger w-30px h-30px btn_delete_{{ $config['singular_key'] }}"
            data-bs-toggle="tooltip" data-{{ $config['singular_key'] }}-name="{{ $_model->title }}"
            title="{{ t('Remove ' . $config['singular_name']) }}">
            <span class="svg-icon svg-icon-3">
                <i class="fas fa-trash-alt"></i>

            </span>
        </a>


    </div>
@endif
