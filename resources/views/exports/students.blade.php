<table dir="{{ direction() }}">
    <thead>
        <tr>
            <th>{{ api('Name (English)') }}</th>
            <th>{{ api('Name (Arabic)') }}</th>
            <th>{{ api('School') }}</th>
            <th>{{ api('Teacher') }}</th>
            {{-- <th>{{ api('Teacher Export Manual Excel') }}</th> --}}
            <th>{{ api('ID Number') }}</th>
            <th>{{ api('Phone Number') }}</th>
            <th>{{ api('Email') }}</th>
            <th>{{ api('Microsoft Email') }}</th>
            <th>{{ api('Guardian (English)') }}</th>
            <th>{{ api('Guardian (Arabic)') }}</th>
            <th>{{ api('Registration Status') }}</th>
            <th>{{ api('Class') }}</th>
            {{-- <th>{{ api('Grade Level') }}</th> --}}
            <th>{{ api('Mother Contact Number') }}</th>
            <th>{{ api('Transportation Method') }}</th>
            <th>{{ api('Madrasati Account') }}</th>
            <th>{{ api('Place of Birth') }}</th>
            <th>{{ api('Date of Birth') }}</th>
            <th>{{ api('Nationality') }}</th>
            <th>{{ api('Residence Permit Number') }}</th>
            <th>{{ api('Residence Permit Date') }}</th>
            <th>{{ api('Residence Permit Expiry Date') }}</th>
            <th>{{ api('Home Phone') }}</th>
            <th>{{ api('Relative Name') }}</th>
            <th>{{ api('Relative Contact Number') }}</th>
            <th>{{ api('Relative Address') }}</th>
            {{-- <th>{{ api('Source') }}</th> --}}
        </tr>
    </thead>
    <tbody>
        @foreach ($students as $student)
            <tr>
                <td>{{ $student->getTranslation('name', 'en') }}</td>
                <td>{{ $student->getTranslation('name', 'ar') }}</td>
                <td>{{ $student->school?->member?->name }}</td>
                <td>{{ $student->teacher?->member?->name }}</td>
                {{-- <td>{{ $student->teacher_export_manual_excel }}</td> --}}
                <td>'{{ $student->id_number }}'</td>
                <td>'{{ $student->phone_number }}'</td>
                <td>{{ $student->email }}</td>
                <td>{{ $student->microsoft_email }}</td>
                <td>{{ $student->guardian?->getTranslation('name', 'en') }}</td>
                <td>{{ $student->guardian?->getTranslation('name', 'ar') }}</td>
                <td>{{ $student->registration_status }}</td>
                <td>{{ $student->classroom?->name }}</td>
                {{-- <td>{{ $student->grade_level?->name }}</td> --}}
                <td>'{{ $student->mother_contact_number }}'</td>
                <td>{{ $student->transportation_method?->name }}</td>
                <td>{{ $student->madrasati_account_number }}</td>
                <td>{{ $student->place_of_birth }}</td>
                <td>{{ $student->date_of_birth?->format('Y-m-d') }}</td>
                <td>{{ $student->nationality?->name }}</td>
                <td>'{{ $student->residence_permit_number }}'</td>
                <td>{{ $student->residence_permit_date?->format('Y-m-d') }}</td>
                <td>{{ $student->residence_permit_expiry_date?->format('Y-m-d') }}</td>
                <td>'{{ $student->home_phone }}'</td>
                <td>{{ $student->relative_name }}</td>
                <td>'{{ $student->relative_contact_number }}'</td>
                <td>{{ $student->relative_address }}</td>
                {{-- <td>{{ $student->source?->name }}</td> --}}
            </tr>
        @endforeach
    </tbody>
</table>
