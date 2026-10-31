<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Portal — Sign In</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    <h1>Sign In</h1>
    <p>Enter your registered phone number to receive a one-time code.</p>

    @if (session('status'))
        <p style="color:green">{{ session('status') }}</p>
    @endif

    @if ($errors->any())
        <ul style="color:red">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ route('otp.request.store') }}">
        @csrf
        <label for="phone">Phone (E.164 format, e.g. +12125550100)</label><br>
        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required><br><br>
        <button type="submit">Send Code</button>
    </form>
</body>
</html>
