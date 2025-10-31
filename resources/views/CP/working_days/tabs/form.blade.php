<div class="card mb-5 mb-xl-10">
    <div class="card-header">
        <div class="card-title m-0">
            <h3 class="fw-bold m-0">{{ t($config['singular_name'] . ' Details') }}</h3>
        </div>
    </div>

    <div class="card mb-5 mb-xl-10">
        <div class="card-body p-9">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="day">{{ t('Day') }}</label>
                        <select name="day" class="form-control" required>
                            @foreach (\App\Enums\Day::cases() as $day)
                                <option value="{{ $day->value }}"
                                {{ old('day', $_model->exists ? $_model->day->value : '') == $day->value ? 'selected' : '' }}
                                >{{ $day->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Repeater for working hours --}}
            <div class="row">
                <div class="col-md-12">
                    <label class="fw-semibold fs-6 mb-2">
                        {{ t('Working Hours') }}
                    </label>
                    <div class="form-group repeater">
                        <div data-repeater-list="hours" class="hours-repeater">
                            @php
                                $hours = old('hours', $_model->exists && $_model->workingDayHours && $_model->workingDayHours->count() > 0 ? $_model->workingDayHours->map(function($hour) {
                                    return [
                                        'start_time' => $hour->start_time->format('H:i'),
                                        'end_time' => $hour->end_time->format('H:i')
                                    ];
                                })->toArray() : [['start_time' => '', 'end_time' => '']]);
                            @endphp
                            @foreach ($hours ?? [['start_time' => '', 'end_time' => '']] as $hour)
                                <div data-repeater-item class="mb-2">
                                    <div class="row">
                                        <div class="col-5">
                                            <label class="fw-semibold fs-6 mb-2">{{ t('Start Time') }}</label>
                                            <input type="time" name="start_time" class="form-control" 
                                                value="{{ $hour['start_time'] ?? '' }}"
                                                placeholder="{{ t('Start Time') }}" required>
                                        </div>
                                        <div class="col-5">
                                            <label class="fw-semibold fs-6 mb-2">{{ t('End Time') }}</label>
                                            <input type="time" name="end_time" class="form-control" 
                                                value="{{ $hour['end_time'] ?? '' }}"
                                                placeholder="{{ t('End Time') }}" required>
                                        </div>
                                        <div class="col-2 d-flex align-items-center">
                                            <button type="button" data-repeater-delete
                                                class="btn btn-outline-danger btn-sm">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                        <button type="button" data-repeater-create class="btn btn-primary btn-sm">
                            <i class="feather icon-plus"></i> {{ t('Add Hour') }}
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@push('scripts')
    <script src="{{ asset('js/repeater/jquery.repeater.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('.repeater').repeater({
                show: function() {
                    $(this).slideDown();
                },

                hide: function(deleteElement) {
                    if (confirm('Are you sure you want to delete this element?')) {
                        $(this).slideUp(deleteElement);
                    }
                },

                ready: function(setIndexes) {
                    // $dragAndDrop.on('drop', setIndexes);
                },
                isFirstItemUndeletable: true
            })

        });
    </script>
@endpush
