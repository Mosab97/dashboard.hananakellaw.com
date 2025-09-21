<table dir="{{ direction() }}">
    <thead>
        <tr>
            <th>{{ api('Name (English)') }}</th>
            <th>{{ api('Name (Arabic)') }}</th>
            <th>{{ api('Email') }}</th>
            <th>{{ api('Phone') }}</th>
            <th>{{ api('ID Number') }}</th>
            <th>{{ api('Age') }}</th>
            <th>{{ api('Graduation Date') }}</th>
            <th>{{ api('Location Description') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($teachers as $teacher)
            <tr>
                <td>{{ $teacher->member->getTranslation('name', 'en') }}</td>
                <td>{{ $teacher->member->getTranslation('name', 'ar') }}</td>
                <td>{{ $teacher->member->email }}</td>
                <td>{{ $teacher->member->full_phone }}</td>
                <td>{{ $teacher->id_number }}</td>
                <td>{{ $teacher->age }}</td>
                <td>{{ $teacher->graduation_date?->format('Y-m-d') }}</td>
                <td>{{ $teacher->getTranslation('location_description', 'en') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
