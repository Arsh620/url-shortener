<!DOCTYPE html>
<html>
<head>
    <title>@yield('title', 'URL Shortener')</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f4f6f8; color: #333; }
        nav { background: #2d3748; padding: 14px 30px; display: flex; justify-content: space-between; align-items: center; }
        nav a { color: #fff; text-decoration: none; margin-right: 16px; font-size: 14px; }
        nav a:hover { text-decoration: underline; }
        nav .user-info { color: #cbd5e0; font-size: 13px; }
        nav form { display: inline; }
        nav button { background: #e53e3e; color: #fff; border: none; padding: 6px 14px; border-radius: 4px; cursor: pointer; font-size: 13px; }
        .container { max-width: 960px; margin: 30px auto; padding: 0 20px; }
        .card { background: #fff; border-radius: 8px; padding: 24px; box-shadow: 0 1px 4px rgba(0,0,0,0.1); }
        h2 { margin-bottom: 20px; font-size: 22px; color: #2d3748; }
        .alert-success { background: #c6f6d5; color: #276749; padding: 10px 16px; border-radius: 4px; margin-bottom: 16px; }
        .alert-error { background: #fed7d7; color: #9b2c2c; padding: 10px 16px; border-radius: 4px; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #2d3748; color: #fff; padding: 10px 12px; text-align: left; font-size: 13px; }
        td { padding: 10px 12px; border-bottom: 1px solid #e2e8f0; font-size: 13px; }
        tr:hover td { background: #f7fafc; }
        .btn { display: inline-block; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-size: 13px; cursor: pointer; border: none; }
        .btn-primary { background: #3182ce; color: #fff; }
        .btn-primary:hover { background: #2b6cb0; }
        .btn-success { background: #38a169; color: #fff; }
        .btn-success:hover { background: #276749; }
        label { display: block; margin-bottom: 4px; font-size: 13px; font-weight: bold; }
        input, select { width: 100%; padding: 8px 10px; border: 1px solid #cbd5e0; border-radius: 4px; margin-bottom: 14px; font-size: 13px; }
        .error { color: #e53e3e; font-size: 12px; margin-top: -10px; margin-bottom: 10px; display: block; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .badge-superadmin { background: #553c9a; color: #fff; }
        .badge-admin { background: #2b6cb0; color: #fff; }
        .badge-member { background: #276749; color: #fff; }
    </style>
</head>
<body>
    <nav>
        <div>
            <a href="{{ route('dashboard') }}">🔗 URL Shortener</a>
            @auth
                <a href="{{ route('short-urls.index') }}">Short URLs</a>
                @if(in_array(auth()->user()->role, ['admin', 'member']))
                    <a href="{{ route('short-urls.create') }}">+ Create URL</a>
                @endif
                @if(in_array(auth()->user()->role, ['superadmin', 'admin']))
                    <a href="{{ route('invitations.create') }}">Invite User</a>
                @endif
            @endauth
        </div>
        @auth
        <div style="display:flex; align-items:center; gap:12px;">
            <span class="user-info">{{ auth()->user()->name }} &nbsp;<span class="badge badge-{{ auth()->user()->role }}">{{ auth()->user()->role }}</span></span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Logout</button>
            </form>
        </div>
        @endauth
    </nav>

    <div class="container">
        <div class="card">
            @yield('content')
        </div>
    </div>
</body>
</html>
