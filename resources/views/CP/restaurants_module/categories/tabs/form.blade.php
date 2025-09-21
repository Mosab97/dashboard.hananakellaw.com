<div class="card mb-5 mb-xl-10">
    <div class="card-header">
        <div class="card-title m-0">
            <h3 class="fw-bold m-0">{{ t($config['singular_name'] . ' Details') }}</h3>
        </div>
    </div>

    <div class="card mb-5 mb-xl-10">
        <div class="card-body p-9">
            <div class="row">
                {{-- Translatable Name Fields --}}
                @foreach (config('app.locales') as $locale)
                    <div class="col-md-4">
                        <div class="fv-row mb-7">
                            <label class="fw-semibold fs-6 mb-2">
                                {{ t('Name') }}
                                <small>({{ strtoupper($locale) }})</small>
                            </label>
                            <input type="text" name="name[{{ $locale }}]"
                                class="form-control form-control-solid mb-3 mb-lg-0 validate-required @error("name.$locale") is-invalid @enderror"
                                placeholder="{{ t('Enter Name in ' . strtoupper($locale)) }}"
                                value="{{ old("name.$locale", isset($_model) ? $_model->getTranslation('name', $locale) : '') }}" />
                            @error("name.$locale")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                @endforeach

            </div>

            <div class="row">
                {{-- <!-- Restaurant Selection -->
                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="fw-semibold fs-6 mb-2 required">{{ t('Restaurant') }}</label>
                        <select name="restaurant_id" id="restaurant_id"
                            class="form-select form-select-solid validate-required @error('restaurant_id') is-invalid @enderror"
                            data-control="select2" data-placeholder="{{ t('Select Restaurant') }}">
                            <option value="">{{ t('Select Restaurant') }}</option>
                            @if (isset($restaurants_list))
                                @foreach ($restaurants_list as $restaurant)
                                    <option value="{{ $restaurant->id }}"
                                        {{ old('restaurant_id', $_model->restaurant_id ?? '') == $restaurant->id ? 'selected' : '' }}>
                                        {{ $restaurant->getFormattedName() }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('restaurant_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div> --}}

                <!-- Order -->
                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="fw-semibold fs-6 mb-2">{{ t('Order') }}</label>
                        <input type="number" name="order" id="order"
                            class="form-control form-control-solid @error('order') is-invalid @enderror"
                            value="{{ old('order', $_model->order ?? '') }}"
                            placeholder="{{ t('Display order (optional)') }}" min="0">
                        @error('order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">{{ t('Leave empty to auto-assign the next available order') }}</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Category Image Upload -->
                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="fw-semibold fs-6 mb-2">{{ t('Category Image') }}</label>
                        <input type="file" name="image" id="image"
                            class="form-control form-control-solid @error('image') is-invalid @enderror"
                            accept="image/*">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if ($_model->exists && $_model->image)
                            <div class="mt-3">
                                <img src="{{ $_model->image_path }}" alt="Current Image" class="img-thumbnail"
                                    style="max-width: 100px; max-height: 100px;">
                                <p class="text-muted mt-1">{{ t('Current image') }}</p>
                            </div>
                        @endif
                        <div class="form-text">{{ t('Upload category image (optional, max 2MB)') }}</div>
                    </div>
                </div>

                <!-- Category Icon Upload -->
                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="fw-semibold fs-6 mb-2">{{ t('Category Icon') }}</label>
                        <input type="file" name="icon" id="icon"
                            class="form-control form-control-solid @error('icon') is-invalid @enderror"
                            accept="image/*">
                        @error('icon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if ($_model->exists && $_model->icon)
                            <div class="mt-3">
                                <img src="{{ $_model->getIconUrl() }}" alt="Current Icon" class="img-thumbnail"
                                    style="max-width: 50px; max-height: 50px;">
                                <p class="text-muted mt-1">{{ t('Current icon') }}</p>
                            </div>
                        @endif
                        <div class="form-text">{{ t('Upload category icon (optional, max 1MB)') }}</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Active Status -->
                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <div class="form-check form-switch form-check-custom form-check-solid">
                            <input class="form-check-input" type="checkbox" name="active" id="active" value="1"
                                {{ old('active', $_model->active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold fs-6" for="active">
                                {{ t('Active') }}
                            </label>
                        </div>
                        <div class="form-text">{{ t('Inactive categories will not be displayed to customers') }}</div>
                    </div>
                </div>

                @if ($_model->exists)
                    @if ($_model->image)
                        <!-- Delete image -->
                        <div class="col-md-6">
                            <div class="fv-row mb-7">
                                <div class="form-check form-switch form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" name="delete_image"
                                        id="delete_image" value="1"
                                        {{ old('delete_image', $_model->delete_image ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold fs-6" for="active">
                                        {{ t('Delete Image') }}
                                    </label>
                                </div>
                                <div class="form-text">{{ t('Delete image will delete the image from the database and the file from the server') }}
                                </div>
                            </div>
                        </div>
                    @endif
                    @if ($_model->icon)
                        <!-- Delete Icon -->
                        <div class="col-md-6">
                            <div class="fv-row mb-7">
                                <div class="form-check form-switch form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" name="delete_icon" id="delete_icon"
                                        value="1"
                                        {{ old('delete_icon', $_model->delete_icon ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold fs-6" for="active">
                                        {{ t('Delete Icon') }}
                                    </label>
                                </div>
                                <div class="form-text">{{ t('Delete icon will delete the icon from the database') }}
                                </div>
                            </div>
                        </div>
                    @endif
                @endif

            </div>

            <!-- Image Preview Section -->
            <div class="row" id="image-preview-section" style="display: none;">
                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="fw-semibold fs-6 mb-2">{{ t('Image Preview') }}</label>
                        <div class="border border-dashed border-gray-300 rounded p-3">
                            <img id="image-preview" src="" alt="Image Preview" class="img-fluid"
                                style="max-height: 200px;">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="fw-semibold fs-6 mb-2">{{ t('Icon Preview') }}</label>
                        <div class="border border-dashed border-gray-300 rounded p-3">
                            <img id="icon-preview" src="" alt="Icon Preview" class="img-fluid"
                                style="max-height: 100px;">
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle image file preview
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('image-preview').src = e.target.result;
                    document.getElementById('image-preview-section').style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });

        // Handle icon file preview
        document.getElementById('icon').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('icon-preview').src = e.target.result;
                    document.getElementById('image-preview-section').style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });

        // Initialize Select2 for restaurant dropdown
        if (typeof KTSelect2 !== 'undefined') {
            KTSelect2.init();
        }
    });
</script>
