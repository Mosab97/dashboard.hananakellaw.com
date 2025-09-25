{{-- Show regular validation errors (only if not showing member_id error) --}}
@if ($errors->any())
    <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
        <i class="ki-duotone ki-shield-tick fs-2hx text-danger me-4">
            <span class="path1"></span>
            <span class="path2"></span>
        </i>
        <div class="d-flex flex-column">
            <h4 class="mb-1 text-danger">{{ t('Something went wrong!') }}</h4>
            <span>{{ t('Please check your inputs:') }}</span>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

{{-- Show success message --}}
@if (session('success'))
    <div class="alert alert-success d-flex align-items-center p-5">
        <span class="svg-icon svg-icon-2hx svg-icon-success me-3">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="currentColor" />
                <path
                    d="M10.4343 12.4343L8.75 10.75C8.33579 10.3358 7.66421 10.3358 7.25 10.75C6.83579 11.1642 6.83579 11.8358 7.25 12.25L10.2929 15.2929C10.6834 15.6834 11.3166 15.6834 11.7071 15.2929L17.25 9.75C17.6642 9.33579 17.6642 8.66421 17.25 8.25C16.8358 7.83579 16.1642 7.83579 15.75 8.25L11.5657 12.4343C11.2533 12.7467 10.7467 12.7467 10.4343 12.4343Z"
                    fill="currentColor" />
            </svg>
        </span>
        <div class="d-flex flex-column">
            <h4 class="mb-1 text-success">{{ session('success') }}</h4>
        </div>
    </div>
@endif

{{-- Show single error message --}}
@if (session('error'))
    <div class="alert alert-danger d-flex align-items-center p-5">
        <span class="svg-icon svg-icon-2hx svg-icon-danger me-3">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path opacity="0.3"
                    d="M12 10.6L14.8 7.8C15.2 7.4 15.8 7.4 16.2 7.8C16.6 8.2 16.6 8.80002 16.2 9.20002L13.4 12L16.2 14.8C16.6 15.2 16.6 15.8 16.2 16.2C15.8 16.6 15.2 16.6 14.8 16.2L12 13.4L9.2 16.2C8.8 16.6 8.2 16.6 7.8 16.2C7.4 15.8 7.4 15.2 7.8 14.8L10.6 12L7.8 9.2C7.4 8.8 7.4 8.2 7.8 7.8C8.2 7.4 8.8 7.4 9.2 7.8L12 10.6Z"
                    fill="currentColor" />
            </svg>
        </span>
        <div class="d-flex flex-column">
            <h4 class="mb-1 text-danger">{{ session('error') }}</h4>
        </div>
    </div>
@endif
