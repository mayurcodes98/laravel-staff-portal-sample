<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Portal — Enter Code</title>
</head>
<body>
    <h1>Enter Verification Code</h1>

    @if ($errors->any())
        <ul style="color:red">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ route('otp.verify.store') }}">
        @csrf
        <input type="hidden" name="phone" value="{{ session('phone', old('phone')) }}">
        <label for="code">6-digit code</label><br>
        <input type="text" id="code" name="code" maxlength="6" inputmode="numeric" required><br><br>
        <button type="submit">Verify</button>
    </form>

    <p><a href="{{ route('otp.request.show') }}">Request a new code</a></p>
</body>
</html>
