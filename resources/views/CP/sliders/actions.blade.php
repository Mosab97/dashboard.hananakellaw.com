@if (Auth::user()->can($config['singular_key'] . '_edit'))
    <div class="d-flex align-items-center gap-2">
           <!-- Edit Button -->
           <a href="{{ route($config['full_route_name'] . '.edit', ['_model' => $_model->id]) }}"
            class="btn btn-icon w-30px h-30px btn-sm btn_update_{{ $config['singular_key'] }} btn-hover-blue"
            style="background-color: rgba(147, 197, 253, 0.1); border-radius: 6px; transition: background-color 0.3s;"
            data-bs-toggle="tooltip" title="{{ t('Edit ' . $config['singular_name']) }}">
            <i class="fas fa-edit" style="color: #93C5FD; transition: color 0.3s;"></i>
        </a>

        <!-- Delete Button -->
        <a href="{{ route($config['full_route_name'] . '.delete', ['_model' => $_model->id]) }}"
            class="btn btn-icon w-30px h-30px btn-sm btn_delete_{{ $config['singular_key'] }} btn-hover-red"
            style="background-color: rgba(251, 113, 133, 0.1); border-radius: 6px; transition: background-color 0.3s;"
            data-bs-toggle="tooltip"
            data-{{ $config['singular_key'] }}-name="{{ $_model->member->name ?? 'Unknown' }}"
            title="{{ t('Remove ' . $config['singular_name']) }}">
            <i class="fas fa-trash-alt" style="color: #FB7185; transition: color 0.3s;"></i>
        </a>

    </div>
@endif
