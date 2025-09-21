<table>
    <thead>
        <tr>
            <th>{{ __('ID') }}</th>
            <th>{{ __('web.Shift Type') }}</th>
            <th>{{ __('web.Teachers') }}</th>
            <th>{{ __('web.Date') }}</th>
            <th>{{ __('web.Created By') }}</th>
            <th>{{ __('web.Created At') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($shifts as $shift)
        <tr>
            <td>{{ $shift->id }}</td>
            <td>{{ $shift->type?->name }}</td>
            <td>{{ $shift->teachers->pluck('member.name')->implode(', ') }}</td>
            <td>{{ $shift->date?->format('Y-m-d') }}</td>
            <td>{{ $shift->creator?->name }}</td>
            <td>{{ $shift->created_at?->format('Y-m-d H:i') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>