<table dir="{{ direction() }}">
    <thead>
        <tr>
            <th>{{ api('Name (English)') }}</th>
            <th>{{ api('Name (Arabic)') }}</th>
            <th>{{ api('Class') }}</th>
            <th>{{ api('Class Number') }}</th>
            <th>{{ api('Grade Level') }}</th>
            <th>{{ api('Students Count') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($classrooms as $classroom)
            <tr>
                <td>{{ $classroom->getTranslation('name', 'en') }}</td>
                <td>{{ $classroom->getTranslation('name', 'ar') }}</td>
                <td>{{ $classroom->class }}</td>
                <td>{{ $classroom->class_number }}</td>
                <td>{{ $classroom->grade_level?->name }}</td>
                <td>{{ $classroom->students_count }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
