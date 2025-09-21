<table dir="{{ direction() }}">
    <thead>
        <tr>
            <th>{{ api('ID') }}</th>
            <th>{{ api('Attendable Type') }}</th>
            <th>{{ api('Attendable Name') }}</th>
            <th>{{ api('Status') }}</th>
            <th>{{ api('Classroom') }}</th>
            <th>{{ api('Attendance Date Time') }}</th>
            <th>{{ api('Hijri Date') }}</th>
            <th>{{ api('Tardiness Time') }}</th>
            <th>{{ api('Minutes Late') }}</th>
            <th>{{ api('Reason') }}</th>
            <th>{{ api('School') }}</th>
            <th>{{ api('Created By') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($attendanceRecords as $record)
            <tr>
                <td>{{ $record->id }}</td>
                <td>{{ class_basename($record->attendable_type) }}</td>
                <td>
                    @if ($record->attendable)
                        {{ $record->attendable->name ?? ($record->attendable->member->name ?? 'N/A') }}
                    @else
                        N/A
                    @endif
                </td>
                <td>{{ $record->status->name ?? 'N/A' }}</td>
                <td>{{ $record->classroom->name ?? 'N/A' }}</td>
                <td>{{ $record->attendance_date_time ? $record->attendance_date_time->format('d/m/Y H:i') : 'N/A' }}
                </td>
                <td>{{ $record->hijri_attendance_date ?? 'N/A' }}</td>
                <td>{{ $record->tardiness_time ?? 'N/A' }}</td>
                <td>{{ $record->total_minutes_late }}</td>
                <td>
                    @if (is_array($record->reason))
                        {{ app()->getLocale() === 'ar' ? $record->reason['ar'] ?? '' : $record->reason['en'] ?? '' }}
                    @else
                        {{ $record->reason ?? '' }}
                    @endif
                </td>
                <td>{{ $record->school->name ?? 'N/A' }}</td>
                <td>{{ $record->creator->name ?? 'N/A' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
