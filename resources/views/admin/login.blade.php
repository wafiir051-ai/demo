<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title>Masuk — Admin {{ $biz->business_name }}</title>
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body class="loginbody">
<form class="logincard" method="post" action="{{ route('admin.login.attempt') }}">
    @csrf
    <h1>{{ $biz->business_name }}</h1>
    <p class="muted">Masuk ke dashboard admin</p>
    @if ($errors->any())<div class="alert err" role="alert">{{ $errors->first() }}</div>@endif
    <label>Email<input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"></label>
    <label>Password<input type="password" name="password" required autocomplete="current-password"></label>
    <label class="chk"><input type="checkbox" name="remember" value="1"> Ingat saya</label>
    <button class="btn" type="submit">Masuk</button>
    <p class="muted small">Dibuat oleh Goding — Build Your Digital Presence with Goding.</p>
</form>
</body>
</html>
