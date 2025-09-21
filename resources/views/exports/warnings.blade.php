<table>
    <thead>
        <tr>
            <th>{{ __('ID') }}</th>
            <th>{{ __('web.Teacher') }}</th>
            <th>{{ __('web.Title') }}</th>
            <th>{{ __('web.Description') }}</th>
            <th>{{ __('web.Date') }}</th>
            {{-- <th>{{ __('web.Status') }}</th> --}}
            <th>{{ __('web.Created By') }}</th>
            <th>{{ __('web.Created At') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($warnings as $warning)
        <tr>
            <td>{{ $warning->id }}</td>
            <td>{{ $warning->teacher?->member?->name }}</td>
            <td>{{ $warning->custom_title??  $warning->title?->name }}</td>
            <td>{{ $warning->description }}</td>
            <td>{{ $warning->date?->format('Y-m-d') }}</td>
            {{-- <td>{{ $warning->is_seen ? __('Seen') : __('Not Seen') }}</td> --}}
            <td>{{ $warning->creator?->name }}</td>
            <td>{{ $warning->created_at?->format('Y-m-d H:i') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>