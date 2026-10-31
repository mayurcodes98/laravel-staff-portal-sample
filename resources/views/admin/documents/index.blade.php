<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Documents — Admin</title></head>
<body>
    <h1>All Documents</h1>
    <a href="{{ route('admin.documents.create') }}">Upload New Document</a>

    @if (session('success'))
        <p style="color:green">{{ session('success') }}</p>
    @endif

    <table border="1" cellpadding="4">
        <thead>
            <tr><th>Title</th><th>Department</th><th>Uploaded By</th><th>Size</th><th>Actions</th></tr>
        </thead>
        <tbody>
            @foreach ($documents as $doc)
                <tr>
                    <td>{{ $doc->title }}</td>
                    <td>{{ $doc->department?->name }}</td>
                    <td>{{ $doc->uploader?->name }}</td>
                    <td>{{ number_format($doc->file_size / 1024, 1) }} KB</td>
                    <td>
                        <form method="POST" action="{{ route('admin.documents.destroy', $doc) }}">
                            @csrf @method('DELETE')
                            <button onclick="return confirm('Delete?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $documents->links() }}
</body>
</html>
