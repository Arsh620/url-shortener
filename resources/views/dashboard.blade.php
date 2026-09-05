@extends('layouts.main')
@section('title', 'Dashboard')
@section('content')
    <h2>Dashboard</h2>
    <p style="margin-bottom:20px; color:#718096;">
        Welcome back, <strong>{{ auth()->user()->name }}</strong>!
        @if(auth()->user()->company)
            &nbsp;— Company: <strong>{{ auth()->user()->company->name }}</strong>
        @endif
    </p>

    <div style="display:flex; gap:16px; flex-wrap:wrap;">
        <a href="{{ route('short-urls.index') }}" class="btn btn-primary">📋 View Short URLs</a>

        @if(in_array(auth()->user()->role, ['admin', 'member']))
            <a href="{{ route('short-urls.create') }}" class="btn btn-success">+ Create Short URL</a>
        @endif

        @if(in_array(auth()->user()->role, ['superadmin', 'admin']))
            <a href="{{ route('invitations.create') }}" class="btn btn-primary">✉️ Invite User</a>
        @endif
    </div>
@endsection
