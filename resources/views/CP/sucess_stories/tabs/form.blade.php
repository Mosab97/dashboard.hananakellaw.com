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
                            <input type="text" name="owner_name[{{ $locale }}]"
                                class="form-control form-control-solid mb-3 mb-lg-0 validate-required @error("owner_name.$locale") is-invalid @enderror"
                                placeholder="{{ t('Enter Name in ' . strtoupper($locale)) }}"
                                value="{{ old("owner_name.$locale", isset($_model) ? $_model->getTranslation('owner_name', $locale) : '') }}" />
                            @error("owner_name.$locale")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                @endforeach

            </div>
            {{-- Video Url --}}
            <div class="row">
                <div class="col-md-4">
                    <div class="fv-row mb-7">
                        <label class="fw-semibold fs-6 mb-2">{{ t('Video Url') }}</label>
                        <input type="text" name="url"
                            class="form-control form-control-solid mb-3 mb-lg-0 validate-required @error('url') is-invalid @enderror"
                            placeholder="{{ t('Enter Video Url') }}"
                            value="{{ old('url', isset($_model) ? $_model->url : '') }}" />
                        @error('url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Thumbnail Image Upload -->
                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="fw-semibold fs-6 mb-2">{{ t('Thumbnail Image') }}</label>
                        <input type="file" name="thumbnail" id="thumbnail"
                            class="form-control form-control-solid @error('thumbnail') is-invalid @enderror"
                            accept="image/*">
                        @error('thumbnail')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if ($_model->exists && $_model->thumbnail)
                            <a href="{{ $_model->thumbnail_path }}" target="_blank">
                                <img src="{{ $_model->thumbnail_path }}" alt="Current Thumbnail" class="img-thumbnail"
                                    style="max-width: 100px; max-height: 100px;">
                                <p class="text-muted mt-1">{{ t('Current thumbnail') }}</p>
                            </a>
                        @endif
                        <div class="form-text">{{ t('Upload thumbnail image (optional, max 2MB)') }}</div>
                    </div>
                </div>


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
                            <textarea name="description[{{ $locale }}]"
                                class="form-control form-control-solid mb-3 mb-lg-0
                                {{-- validate-required  --}}
                                @error("description.$locale") is-invalid @enderror"
                                placeholder="{{ t('Enter Description in ' . strtoupper($locale)) }}">{{ old("description.$locale", isset($_model) ? $_model->getTranslation('description', $locale) : '') }}</textarea>
                            @error("description.$locale")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                @endforeach
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
                        <div class="form-text">{{ t('Inactive success stories will not be displayed to customers') }}
                        </div>
                    </div>
                </div>

                @if ($_model->exists && $_model->thumbnail)
                    <!-- Delete thumbnail -->
                    <div class="col-md-6">
                        <div class="fv-row mb-7">
                            <div class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" name="delete_thumbnail"
                                    id="delete_thumbnail" value="1"
                                    {{ old('delete_thumbnail', $_model->delete_thumbnail ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold fs-6" for="active">
                                    {{ t('Delete Thumbnail') }}
                                </label>
                            </div>
                            <div class="form-text">
                                {{ t('Delete thumbnail will delete the thumbnail from the database') }}
                            </div>
                        </div>
                    </div>
                @endif

            </div>


        </div>
    </div>
</div>
