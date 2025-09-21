{{-- @dd([
    '_model' => $_model->id,
    'product' => $_model->product_id,
],$config, $_model,$config['full_route_name'] . '.edit') --}}
@if (Auth::user()->can($config['permissions']['edit']) && $_model->product_id)
    <div class="d-flex align-items-center gap-2">
        {{-- Edit Button --}}
        <a href="{{ route($config['full_route_name'] . '.edit', [
            '_model' => $_model->id,
            'product' => $_model->product_id,
        ]) }}"
            class="btn btn-icon btn-active-light-primary w-30px h-30px btn_update_{{ $config['singular_key'] }}"
            data-bs-toggle="tooltip" title="{{ t('Edit ' . $config['singular_name']) }}">
            <span class="svg-icon svg-icon-3">
                <i class="fa-solid fa-pen-to-square"></i>
            </span>
        </a>


        {{-- Delete Button --}}
        <a href="{{ route($config['full_route_name'] . '.delete', ['_model' => $_model->id, 'product' => $_model->product_id]) }}"
            class="btn btn-icon btn-active-light-danger w-30px h-30px btn_delete_{{ $config['singular_key'] }}"
            data-bs-toggle="tooltip" data-{{ $config['singular_key'] }}-name="{{ $_model->name }}"
            title="{{ t('Remove ' . $config['singular_name']) }}">
            <i class="fa-solid fa-trash"></i>
        </a>


    </div>
@endif
