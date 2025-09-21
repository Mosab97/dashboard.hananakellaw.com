<table>
    <thead>
        <tr>
            <th>{{ __('ID') }}</th>
            <th>{{ __('web.Student Name') }}</th>
            <th>{{ __('web.Class') }}</th>
            <th>{{ __('web.Type') }}</th>
            <th>{{ __('web.Notes') }}</th>
            <th>{{ __('web.Teachers') }}</th>
            <th>{{ __('web.Created By') }}</th>
            <th>{{ __('web.Created At') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($callRequests as $request)
        <tr>
            <td>{{ $request->id }}</td>
            <td>{{ $request->student?->name }}</td>
            <td>{{ $request->student?->classroom?->name }}</td>
            <td>{{ $request->type?->name }}</td>
            <td>{{ $request->notes }}</td>
            <td>{{ $request->teachers->pluck('member.name')->implode(', ') }}</td>
            <td>{{ $request->creator?->name }}</td>
            <td>{{ $request->created_at?->format('Y-m-d H:i') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>