<table>
    <thead>
        <tr>
            <th>{{ __('ID') }}</th>
            <th>{{ __('web.Student Name') }}</th>
            <th>{{ __('web.Class') }}</th>
            <th>{{ __('web.Going To') }}</th>
            <th>{{ __('web.Hours') }}</th>
            <th>{{ __('web.Status') }}</th>
            <th>{{ __('web.Created By') }}</th>
            <th>{{ __('web.Created At') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($exitPermits as $permit)
        <tr>
            <td>{{ $permit->id }}</td>
            <td>{{ $permit->student?->name }}</td>
            <td>{{ $permit->student?->classroom?->name }}</td>
            <td>{{ $permit->goingTo?->name }}</td>
            <td>{{ $permit->hours }}</td>
            <td>{{ $permit->active ? __('Active') : __('Expired') }}</td>
            <td>{{ $permit->user?->name }}</td>
            <td>{{ $permit->created_at?->format('Y-m-d H:i') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>