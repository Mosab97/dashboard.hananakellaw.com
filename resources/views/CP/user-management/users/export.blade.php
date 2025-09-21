<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Employee Name</th>
            <th>Mobile</th>
            <th>Email</th>
            <th>Role</th>
            <th>Active</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
            <tr>
                <td>{{ ++$loop->index }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->mobile }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ @$user->roles->pluck('name')->implode(',') }}</td>
                <td>{{ $user->status ? 'true' : 'false' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
