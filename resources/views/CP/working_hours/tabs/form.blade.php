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
                            @foreach (Day::cases() as $day)
                                <option value="{{ $day->value }}">{{ $day->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="start_time">{{ t('Start Time') }}</label>
                        <input type="time" name="start_time" class="form-control" value="{{ old('start_time') }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="end_time">{{ t('End Time') }}</label>
                        <input type="time" name="end_time" class="form-control" value="{{ old('end_time') }}" required>
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



        // Initialize Select2 for restaurant dropdown
        if (typeof KTSelect2 !== 'undefined') {
            KTSelect2.init();
        }
    });
</script>
