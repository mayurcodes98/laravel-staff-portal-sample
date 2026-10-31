<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Upload Document — Admin</title></head>
<body>
    <h1>Upload Document</h1>

    @if ($errors->any())
        <ul style="color:red">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    @endif

    <form method="POST" action="{{ route('admin.documents.store') }}" enctype="multipart/form-data">
        @csrf
        <label>Title: <input type="text" name="title" value="{{ old('title') }}" required></label><br><br>
        <label>Department:
            <select name="department_id" required>
                <option value="">— select —</option>
                @foreach ($departments as $dept)
                    <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                        {{ $dept->name }}
                    </option>
                @endforeach
            </select>
        </label><br><br>
        <label>File (PDF, DOCX, XLSX — max 10 MB): <input type="file" name="file" required></label><br><br>
        <button type="submit">Upload</button>
    </form>
</body>
</html>
