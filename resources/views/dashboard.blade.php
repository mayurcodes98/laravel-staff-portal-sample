<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Portal — Dashboard</title>
</head>
<body>
    <h1>Welcome, {{ auth()->user()->name }}</h1>
    <p>Role: {{ auth()->user()->role }}</p>

    @if (auth()->user()->isAdmin())
        <ul>
            <li><a href="{{ route('admin.documents.index') }}">Manage Documents</a></li>
            <li><a href="{{ route('admin.users.index') }}">Manage Users</a></li>
        </ul>
    @else
        <ul>
            <li><a href="{{ route('employee.documents.index') }}">My Documents</a></li>
        </ul>
    @endif

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Sign Out</button>
    </form>
</body>
</html>
