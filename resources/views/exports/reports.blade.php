<table dir="{{ direction() }}">
    <thead>
        <tr>
            <th>{{ api('ID') }}</th>
            <th>{{ api('Reportable Type') }}</th>
            <th>{{ api('Reportable Name') }}</th>
            <th>{{ api('Subject') }}</th>
            <th>{{ api('Module Type') }}</th>
            <th>{{ api('Status') }}</th>
            <th>{{ api('Classroom') }}</th>
            <th>{{ api('Report Date Time') }}</th>
            <th>{{ api('Hijri Date') }}</th>
            <th>{{ api('Rating') }}</th>
            <th>{{ api('Reason') }}</th>
            <th>{{ api('School') }}</th>
            <th>{{ api('Created By') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($reports as $report)
            <tr>
                <td>{{ $report->id }}</td>
                <td>{{ class_basename($report->reportable_type) }}</td>
                <td>
                    @if ($report->reportable)
                        {{ $report->reportable->name ?? 'N/A' }}
                    @else
                        N/A
                    @endif
                </td>
                <td>
                    @if (is_array($report->subject))
                        {{ app()->getLocale() === 'ar' ? $report->subject['ar'] ?? '' : $report->subject['en'] ?? '' }}
                    @else
                        {{ $report->subject ?? '' }}
                    @endif
                </td>
                <td>{{ $report->moduleType->name ?? 'N/A' }}</td>
                <td>{{ $report->status->name ?? 'N/A' }}</td>
                <td>{{ $report->classroom->name ?? 'N/A' }}</td>
                <td>{{ $report->report_date_time ? $report->report_date_time->format('d/m/Y H:i') : 'N/A' }}</td>
                <td>{{ $report->hijri_report_date ?? 'N/A' }}</td>
                <td>{{ $report->rating ?? 'N/A' }}</td>
                <td>
                    @if (is_array($report->reason))
                        {{ app()->getLocale() === 'ar' ? $report->reason['ar'] ?? '' : $report->reason['en'] ?? '' }}
                    @else
                        {{ $report->reason ?? '' }}
                    @endif
                </td>
                <td>{{ $report->school->name ?? 'N/A' }}</td>
                <td>{{ $report->creator->name ?? 'N/A' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
