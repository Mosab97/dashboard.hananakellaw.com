<div class="card mb-5 mb-xl-10">
    <div class="card-header">
        <div class="card-title m-0">
            <h3 class="fw-bold m-0">{{ t($config['singular_name'] . ' Details') }}</h3>
        </div>
    </div>

    <div class="card mb-5 mb-xl-10">
        <div class="card-body p-9">
            <div class="row">
                <!-- Restaurant Name (Arabic) -->
                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="fw-semibold fs-6 mb-2 required">{{ t('Restaurant Name (Arabic)') }}</label>
                        <input type="text" name="name[ar]" id="name_ar"
                            class="form-control form-control-solid validate-required @error('name.ar') is-invalid @enderror"
                            value="{{ old('name.ar', $_model->name['ar'] ?? '') }}"
                            placeholder="{{ t('Enter restaurant name in Arabic') }}">
                        @error('name.ar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Restaurant Name (English) -->
                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="fw-semibold fs-6 mb-2">{{ t('Restaurant Name (English)') }}</label>
                        <input type="text" name="name[en]" id="name_en"
                            class="form-control form-control-solid @error('name.en') is-invalid @enderror"
                            value="{{ old('name.en', $_model->name['en'] ?? '') }}"
                            placeholder="{{ t('Enter restaurant name in English') }}">
                        @error('name.en')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Description (Arabic) -->
                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="fw-semibold fs-6 mb-2">{{ t('Description (Arabic)') }}</label>
                        <textarea name="description[ar]" id="description_ar"
                            class="form-control form-control-solid @error('description.ar') is-invalid @enderror"
                            rows="3"
                            placeholder="{{ t('Enter description in Arabic') }}">{{ old('description.ar', $_model->description['ar'] ?? '') }}</textarea>
                        @error('description.ar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Description (English) -->
                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="fw-semibold fs-6 mb-2">{{ t('Description (English)') }}</label>
                        <textarea name="description[en]" id="description_en"
                            class="form-control form-control-solid @error('description.en') is-invalid @enderror"
                            rows="3"
                            placeholder="{{ t('Enter description in English') }}">{{ old('description.en', $_model->description['en'] ?? '') }}</textarea>
                        @error('description.en')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Logo Upload -->
                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="fw-semibold fs-6 mb-2">{{ t('Logo') }}</label>
                        <input type="file" name="logo" id="logo"
                            class="form-control form-control-solid @error('logo') is-invalid @enderror"
                            accept="image/*">
                        @error('logo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($_model->exists && $_model->logo)
                            <div class="mt-3">
                                <img src="{{ $_model->getLogoUrl() }}" alt="Current Logo" class="img-thumbnail" style="max-width: 100px; max-height: 100px;">
                                <p class="text-muted mt-1">{{ t('Current logo') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Slug -->
                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="fw-semibold fs-6 mb-2">{{ t('Slug') }}</label>
                        <input type="text" name="slug" id="slug"
                            class="form-control form-control-solid @error('slug') is-invalid @enderror"
                            value="{{ old('slug', $_model->slug ?? '') }}"
                            placeholder="{{ t('Auto-generated from name (leave empty)') }}">
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">{{ t('Leave empty to auto-generate from restaurant name') }}</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Phone -->
                <div class="col-md-4">
                    <div class="fv-row mb-7">
                        <label class="fw-semibold fs-6 mb-2">{{ t('Phone') }}</label>
                        <input type="text" name="phone" id="phone"
                            class="form-control form-control-solid @error('phone') is-invalid @enderror"
                            value="{{ old('phone', $_model->phone ?? '') }}"
                            placeholder="{{ t('Enter phone number') }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Email -->
                <div class="col-md-4">
                    <div class="fv-row mb-7">
                        <label class="fw-semibold fs-6 mb-2">{{ t('Email') }}</label>
                        <input type="email" name="email" id="email"
                            class="form-control form-control-solid @error('email') is-invalid @enderror"
                            value="{{ old('email', $_model->email ?? '') }}"
                            placeholder="{{ t('Enter email address') }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Website -->
                <div class="col-md-4">
                    <div class="fv-row mb-7">
                        <label class="fw-semibold fs-6 mb-2">{{ t('Website') }}</label>
                        <input type="url" name="website" id="website"
                            class="form-control form-control-solid @error('website') is-invalid @enderror"
                            value="{{ old('website', $_model->website ?? '') }}"
                            placeholder="{{ t('Enter website URL') }}">
                        @error('website')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Address -->
                <div class="col-md-12">
                    <div class="fv-row mb-7">
                        <label class="fw-semibold fs-6 mb-2">{{ t('Address') }}</label>
                        <textarea name="address" id="address"
                            class="form-control form-control-solid @error('address') is-invalid @enderror"
                            rows="3"
                            placeholder="{{ t('Enter restaurant address') }}">{{ old('address', $_model->address ?? '') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Service Options -->
                <div class="col-md-12">
                    <div class="fv-row mb-7">
                        <label class="fw-semibold fs-6 mb-2">{{ t('Available Services') }}</label>
                        <div class="d-flex flex-wrap gap-5">
                            <div class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" name="delivery_available" id="delivery_available"
                                    value="1" {{ old('delivery_available', $_model->delivery_available ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold fs-6" for="delivery_available">
                                    {{ t('Delivery Available') }}
                                </label>
                            </div>

                            <div class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" name="pickup_available" id="pickup_available"
                                    value="1" {{ old('pickup_available', $_model->pickup_available ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold fs-6" for="pickup_available">
                                    {{ t('Pickup Available') }}
                                </label>
                            </div>

                            <div class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" name="dine_in_available" id="dine_in_available"
                                    value="1" {{ old('dine_in_available', $_model->dine_in_available ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold fs-6" for="dine_in_available">
                                    {{ t('Dine-in Available') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Active Status -->
                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <div class="form-check form-switch form-check-custom form-check-solid">
                            <input class="form-check-input" type="checkbox" name="active" id="active"
                                value="1" {{ old('active', $_model->active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold fs-6" for="active">
                                {{ t('Active') }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Opening Hours Section -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card border-dashed border-gray-300">
                        <div class="card-header">
                            <h4 class="card-title">{{ t('Opening Hours') }}</h4>
                        </div>
                        <div class="card-body">
                            @php
                                $openingHours = old('opening_hours', $_model->opening_hours ?? []);
                            @endphp

                            @foreach($days_of_week as $dayKey => $dayName)
                                <div class="row align-items-center mb-4">
                                    <div class="col-md-2">
                                        <label class="fw-semibold fs-6">{{ $dayName }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-check">
                                            <input class="form-check-input day-closed-checkbox" type="checkbox"
                                                name="opening_hours[{{ $dayKey }}][closed]"
                                                id="closed_{{ $dayKey }}"
                                                value="1"
                                                data-day="{{ $dayKey }}"
                                                {{ isset($openingHours[$dayKey]['closed']) && $openingHours[$dayKey]['closed'] ? 'checked' : '' }}>
                                            <label class="form-check-label" for="closed_{{ $dayKey }}">
                                                {{ t('Closed') }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="time" name="opening_hours[{{ $dayKey }}][open]"
                                            id="open_{{ $dayKey }}"
                                            class="form-control form-control-solid day-time-input"
                                            value="{{ $openingHours[$dayKey]['open'] ?? '09:00' }}"
                                            {{ isset($openingHours[$dayKey]['closed']) && $openingHours[$dayKey]['closed'] ? 'disabled' : '' }}>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="time" name="opening_hours[{{ $dayKey }}][close]"
                                            id="close_{{ $dayKey }}"
                                            class="form-control form-control-solid day-time-input"
                                            value="{{ $openingHours[$dayKey]['close'] ?? '22:00' }}"
                                            {{ isset($openingHours[$dayKey]['closed']) && $openingHours[$dayKey]['closed'] ? 'disabled' : '' }}>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle day closed checkbox
    document.querySelectorAll('.day-closed-checkbox').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const day = this.dataset.day;
            const openInput = document.getElementById('open_' + day);
            const closeInput = document.getElementById('close_' + day);

            if (this.checked) {
                openInput.disabled = true;
                closeInput.disabled = true;
                openInput.value = '';
                closeInput.value = '';
            } else {
                openInput.disabled = false;
                closeInput.disabled = false;
                if (!openInput.value) openInput.value = '09:00';
                if (!closeInput.value) closeInput.value = '22:00';
            }
        });
    });

    // Auto-generate slug from Arabic name
    document.getElementById('name_ar').addEventListener('input', function() {
        const slugInput = document.getElementById('slug');
        if (!slugInput.value) {
            const slug = this.value
                .toLowerCase()
                .replace(/[\u0600-\u06FF\s]+/g, '-') // Replace Arabic characters and spaces with hyphens
                .replace(/[^\w\-]+/g, '') // Remove non-word chars
                .replace(/\-\-+/g, '-') // Replace multiple hyphens with single hyphen
                .replace(/^-+/, '') // Trim hyphens from start
                .replace(/-+$/, ''); // Trim hyphens from end
            slugInput.value = slug;
        }
    });
});
</script>
