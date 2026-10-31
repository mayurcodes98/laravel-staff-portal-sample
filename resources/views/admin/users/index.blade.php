<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Users — Admin</title></head>
<body>
    <h1>Staff Directory</h1>
    <table border="1" cellpadding="4">
        <thead><tr><th>Name</th><th>Phone</th><th>Role</th><th>Departments</th></tr></thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user['name'] }}</td>
                    <td>{{ $user['phone'] }}</td>
                    <td>{{ $user['role'] }}</td>
                    <td>{{ collect($user['departments'] ?? [])->pluck('name')->join(', ') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
