<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>My Documents</title></head>
<body>
    <h1>My Documents</h1>
    <table border="1" cellpadding="4">
        <thead><tr><th>Title</th><th>Department</th><th>Date</th><th>Download</th></tr></thead>
        <tbody>
            @foreach ($documents as $doc)
                <tr>
                    <td>{{ $doc->title }}</td>
                    <td>{{ $doc->department?->name }}</td>
                    <td>{{ $doc->created_at->format('Y-m-d') }}</td>
                    <td><a href="{{ route('employee.documents.download', $doc) }}">Download</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $documents->links() }}
</body>
</html>
