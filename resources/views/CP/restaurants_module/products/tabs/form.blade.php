<div class="card mb-5 mb-xl-10">
    <div class="card-header">
        <div class="card-title m-0">
            <h3 class="fw-bold m-0">{{ t($config['singular_name'] . ' Details') }}</h3>
        </div>
    </div>

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
            {{-- Translatable Description Fields --}}
            @foreach (config('app.locales') as $locale)
                <div class="col-md-4">
                    <div class="fv-row mb-7">
                        <label class="fw-semibold fs-6 mb-2">
                            {{ t('Description') }}
                            <small>({{ strtoupper($locale) }})</small>
                        </label>
                        <textarea name="description[{{ $locale }}]" id="description_{{ $locale }}"
                            class="form-control form-control-solid @error('description.' . $locale) is-invalid @enderror" rows="3"
                            placeholder="{{ t('Enter description in ' . strtoupper($locale)) }}">{{ old('description.' . $locale, $_model->getTranslation('description', $locale) ?? '') }}</textarea>
                        @error('description.' . $locale)
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row">
            <!-- Image Upload -->
            <div class="col-md-6">
                <div class="fv-row mb-7">
                    <label class="fw-semibold fs-6 mb-2">{{ t('Product Image') }}</label>
                    <input type="file" name="image" id="image"
                        class="form-control form-control-solid @error('image') is-invalid @enderror" accept="image/*">
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
                </div>
            </div>

            <!-- Price -->
            <div class="col-md-6">
                <div class="fv-row mb-7">
                    <label class="fw-semibold fs-6 mb-2 required">{{ t('Price') }}</label>
                    <input type="number" name="price" id="price" step="0.01" min="0"
                        class="form-control form-control-solid validate-required @error('price') is-invalid @enderror"
                        value="{{ old('price', $_model->price ?? '') }}" placeholder="{{ t('Enter product price') }}">
                    @error('price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Category -->
            <div class="col-md-4">
                <div class="fv-row mb-7">
                    <label class="fw-semibold fs-6 mb-2 required">{{ t('Category') }}</label>
                    <select name="category_id" id="category_id"
                        class="form-select form-select-solid validate-required @error('category_id') is-invalid @enderror">
                        <option value="">{{ t('Select Category') }}</option>
                        @foreach ($categories_list ?? [] as $category)
                            <option value="{{ $category->id }}"
                                {{ old('category_id', $_model->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- <!-- Restaurant -->
            <div class="col-md-4">
                <div class="fv-row mb-7">
                    <label class="fw-semibold fs-6 mb-2 required">{{ t('Restaurant') }}</label>
                    <select name="restaurant_id" id="restaurant_id"
                        class="form-select form-select-solid validate-required @error('restaurant_id') is-invalid @enderror">
                        <option value="">{{ t('Select Restaurant') }}</option>
                        @foreach ($restaurants_list ?? [] as $restaurant)
                            <option value="{{ $restaurant->id }}"
                                {{ old('restaurant_id', $_model->restaurant_id ?? '') == $restaurant->id ? 'selected' : '' }}>
                                {{ $restaurant->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('restaurant_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div> --}}

            <!-- Order -->
            <div class="col-md-4">
                <div class="fv-row mb-7">
                    <label class="fw-semibold fs-6 mb-2">{{ t('Display Order') }}</label>
                    <input type="number" name="order" id="order" min="0"
                        class="form-control form-control-solid @error('order') is-invalid @enderror"
                        value="{{ old('order', $_model->order ?? 0) }}" placeholder="{{ t('Enter display order') }}">
                    @error('order')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
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
                </div>
            </div>
            <!-- delete image    -->
            <div class="col-md-6">
                <div class="fv-row mb-7">
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" name="delete_image" id="delete_image" value="1"
                            {{ old('delete_image', $_model->delete_image ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold fs-6" for="delete_image">
                            {{ t('Delete Image') }}
                        </label>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
{{--
<!-- Product Sizes Section -->
<div class="card mb-5 mb-xl-10">
    <div class="card-header">
        <div class="card-title m-0">
            <h3 class="fw-bold m-0">{{ t('Product Sizes') }}</h3>
        </div>
        <div class="card-toolbar">
            <button type="button" class="btn btn-sm btn-primary" id="add-size-btn">
                <i class="ki-duotone ki-plus fs-2"></i>
                {{ t('Add Size') }}
            </button>
        </div>
    </div>

    <div class="card-body p-9">
        <div id="sizes-container">
            @if ($_model->exists && $_model->sizes && count($_model->sizes) > 0)
                @foreach ($_model->sizes as $index => $size)
                    <div class="size-item border rounded p-4 mb-4" data-index="{{ $index }}">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">{{ t('Size') }} #{{ $index + 1 }}</h5>
                            <button type="button" class="btn btn-sm btn-light-danger remove-size-btn">
                                <i class="ki-duotone ki-trash fs-6"></i>
                                {{ t('Remove') }}
                            </button>
                        </div>

                        <div class="row">
                            <!-- Size Name (Arabic) -->
                            <div class="col-md-3">
                                <div class="fv-row mb-7">
                                    <label
                                        class="fw-semibold fs-6 mb-2 required">{{ t('Size Name (Arabic)') }}</label>
                                    <input type="text" name="sizes[{{ $index }}][name][ar]"
                                        class="form-control form-control-solid validate-required"
                                        value="{{ old('sizes.' . $index . '.name.ar', $size['name']['ar'] ?? '') }}"
                                        placeholder="{{ t('Enter size name in Arabic') }}">
                                </div>
                            </div>

                            <!-- Size Name (English) -->
                            <div class="col-md-3">
                                <div class="fv-row mb-7">
                                    <label class="fw-semibold fs-6 mb-2">{{ t('Size Name (English)') }}</label>
                                    <input type="text" name="sizes[{{ $index }}][name][en]"
                                        class="form-control form-control-solid"
                                        value="{{ old('sizes.' . $index . '.name.en', $size['name']['en'] ?? '') }}"
                                        placeholder="{{ t('Enter size name in English') }}">
                                </div>
                            </div>

                            <!-- Size Price -->
                            <div class="col-md-2">
                                <div class="fv-row mb-7">
                                    <label class="fw-semibold fs-6 mb-2 required">{{ t('Price') }}</label>
                                    <input type="number" name="sizes[{{ $index }}][price]" step="0.01"
                                        min="0" class="form-control form-control-solid validate-required"
                                        value="{{ old('sizes.' . $index . '.price', $size['price'] ?? '') }}"
                                        placeholder="{{ t('0.00') }}">
                                </div>
                            </div>

                            <!-- Size Order -->
                            <div class="col-md-2">
                                <div class="fv-row mb-7">
                                    <label class="fw-semibold fs-6 mb-2">{{ t('Order') }}</label>
                                    <input type="number" name="sizes[{{ $index }}][order]" min="0"
                                        class="form-control form-control-solid"
                                        value="{{ old('sizes.' . $index . '.order', $size['order'] ?? 0) }}"
                                        placeholder="{{ t('0') }}">
                                </div>
                            </div>

                            <!-- Size Active -->
                            <div class="col-md-2">
                                <div class="fv-row mb-7">
                                    <label class="fw-semibold fs-6 mb-2">{{ t('Status') }}</label>
                                    <div class="form-check form-switch form-check-custom form-check-solid">
                                        <input class="form-check-input" type="checkbox"
                                            name="sizes[{{ $index }}][active]" value="1"
                                            {{ old('sizes.' . $index . '.active', $size['active'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold fs-6">
                                            {{ t('Active') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-5" id="no-sizes-message">
                    <div class="text-muted">
                        <i class="ki-duotone ki-information-5 fs-2x mb-3"></i>
                        <p class="fs-6">{{ t('No sizes added yet. Click "Add Size" to create product sizes.') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div> --}}
{{--
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let sizeIndex = {{ $_model->exists && $_model->sizes ? count($_model->sizes) : 0 }};

        // Add Size Button
        document.getElementById('add-size-btn').addEventListener('click', function() {
            addSizeItem();
        });

        // Remove Size Button Event Delegation
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-size-btn') || e.target.closest(
                '.remove-size-btn')) {
                e.preventDefault();
                const sizeItem = e.target.closest('.size-item');
                if (sizeItem) {
                    sizeItem.remove();
                    updateSizeNumbers();
                    toggleNoSizesMessage();
                }
            }
        });

        function addSizeItem() {
            const container = document.getElementById('sizes-container');
            const noSizesMessage = document.getElementById('no-sizes-message');

            if (noSizesMessage) {
                noSizesMessage.remove();
            }

            const sizeHtml = `
            <div class="size-item border rounded p-4 mb-4" data-index="${sizeIndex}">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">{{ t('Size') }} #${sizeIndex + 1}</h5>
                    <button type="button" class="btn btn-sm btn-light-danger remove-size-btn">
                        <i class="ki-duotone ki-trash fs-6"></i>
                        {{ t('Remove') }}
                    </button>
                </div>

                <div class="row">
                    <!-- Size Name (Arabic) -->
                    <div class="col-md-3">
                        <div class="fv-row mb-7">
                            <label class="fw-semibold fs-6 mb-2 required">{{ t('Size Name (Arabic)') }}</label>
                            <input type="text" name="sizes[${sizeIndex}][name][ar]"
                                class="form-control form-control-solid validate-required"
                                placeholder="{{ t('Enter size name in Arabic') }}">
                        </div>
                    </div>

                    <!-- Size Name (English) -->
                    <div class="col-md-3">
                        <div class="fv-row mb-7">
                            <label class="fw-semibold fs-6 mb-2">{{ t('Size Name (English)') }}</label>
                            <input type="text" name="sizes[${sizeIndex}][name][en]"
                                class="form-control form-control-solid"
                                placeholder="{{ t('Enter size name in English') }}">
                        </div>
                    </div>

                    <!-- Size Price -->
                    <div class="col-md-2">
                        <div class="fv-row mb-7">
                            <label class="fw-semibold fs-6 mb-2 required">{{ t('Price') }}</label>
                            <input type="number" name="sizes[${sizeIndex}][price]" step="0.01" min="0"
                                class="form-control form-control-solid validate-required"
                                placeholder="{{ t('0.00') }}">
                        </div>
                    </div>

                    <!-- Size Order -->
                    <div class="col-md-2">
                        <div class="fv-row mb-7">
                            <label class="fw-semibold fs-6 mb-2">{{ t('Order') }}</label>
                            <input type="number" name="sizes[${sizeIndex}][order]" min="0"
                                class="form-control form-control-solid"
                                value="0" placeholder=" 0">
                        </div>
                    </div>

                    <!-- Size Active -->
                    <div class="col-md-2">
                        <div class="fv-row mb-7">
                            <label class="fw-semibold fs-6 mb-2">{{ t('Status') }}</label>
                            <div class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox"
                                    name="sizes[${sizeIndex}][active]"
                                    value="1" checked>
                                <label class="form-check-label fw-semibold fs-6">
                                    {{ t('Active') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

            container.insertAdjacentHTML('beforeend', sizeHtml);
            sizeIndex++;
        }

        function updateSizeNumbers() {
            const sizeItems = document.querySelectorAll('.size-item');
            sizeItems.forEach((item, index) => {
                const title = item.querySelector('h5');
                if (title) {
                    title.textContent = `{{ t('Size') }} #${index + 1}`;
                }
                item.setAttribute('data-index', index);
            });
        }

        function toggleNoSizesMessage() {
            const container = document.getElementById('sizes-container');
            const sizeItems = container.querySelectorAll('.size-item');

            if (sizeItems.length === 0) {
                const noSizesHtml = `
                <div class="text-center py-5" id="no-sizes-message">
                    <div class="text-muted">
                        <i class="ki-duotone ki-information-5 fs-2x mb-3"></i>
                        <p class="fs-6">{{ t('No sizes added yet. Click "Add Size" to create product sizes.') }}</p>
                    </div>
                </div>
            `;
                container.innerHTML = noSizesHtml;
            }
        }

        // Initial check for no sizes message
        toggleNoSizesMessage();
    });
</script> --}}
