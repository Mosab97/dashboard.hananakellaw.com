{{-- initializeFlatpickr --}}
{{-- <script>
    function initializeFlatpickr(form = null) {
        initFlatpickr('.date-flatpickr-dob-24', {
            maxDate: "today",
        });
        initFlatpickr('.date-flatpickr-min-today', {
            minDate: "today",
        });
        // Initialize time pickers with the class 'time-flatpickr' using the reusable initFlatpickr function
        initFlatpickr('.time-flatpickr', {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true
        });

    }

    // Reusable function to initialize flatpickr with default options
    function initFlatpickr(selector, options = {}) {
        // console.log('initFlatpickr called for selector:', selector);
        return $(selector).flatpickr({
            enableTime: false,
            dateFormat: "Y-m-d",
            allowInput: true,
            ...options
        });
    }

    $(function() {
        // console.log('Document ready, initializing date and time pickers');
        initializeFlatpickr();
    });
</script> --}}
