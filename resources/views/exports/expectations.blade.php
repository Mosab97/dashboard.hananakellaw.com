<table>
    <thead>
        <tr>
            <th>{{ __('ID') }}</th>
            <th>{{ __('web.Teacher') }}</th>
            <th>{{ __('web.Class') }}</th>
            <th>{{ __('web.Session') }}</th>
            <th>{{ __('web.Date') }}</th>
            <th>{{ __('web.Time') }}</th>
            <th>{{ __('web.Created By') }}</th>
            <th>{{ __('web.Created At') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($expectations as $expectation)
        <tr>
            <td>{{ $expectation->id }}</td>
            <td>{{ $expectation->teacher?->member?->name }}</td>
            <td>{{ $expectation->classroom?->name }}</td>
            <td>{{ $expectation->session?->name }}</td>
            <td>{{ $expectation->date?->format('Y-m-d') }}</td>
            <td>{{ $expectation->time?->format('H:i') }}</td>
            <td>{{ $expectation->creator?->name }}</td>
            <td>{{ $expectation->created_at?->format('Y-m-d H:i') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
