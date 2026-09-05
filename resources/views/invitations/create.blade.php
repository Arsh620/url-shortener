@extends('layouts.main')
@section('title', 'Invite User')
@section('content')
    <h2>Invite User</h2>

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}<br><br>
            <strong>Invitation Link:</strong><br>
            <div style="display:flex; align-items:center; gap:10px; margin-top:6px;">
                <input type="text" id="inv-link" value="{{ session('invitation_link') }}" readonly style="width:100%; margin:0;">
                <button onclick="copyLink()" class="btn btn-primary" style="white-space:nowrap;">Copy Link</button>
            </div>
            <span id="copied-msg" style="color:green; font-size:12px; display:none;">✅ Copied!</span>
        </div>
        <script>
            function copyLink() {
                var input = document.getElementById('inv-link');
                input.select();
                document.execCommand('copy');
                document.getElementById('copied-msg').style.display = 'inline';
            }
        </script>
    @endif

    <form method="POST" action="{{ route('invitations.store') }}">
        @csrf

        @if(auth()->user()->role === 'superadmin')
        <label>Company Name</label>
        <input type="text" name="company_name" placeholder="Enter new company name" required>
        @error('company_name') <span class="error">{{ $message }}</span> @enderror
        @endif

        <label>Email</label>
        <input type="email" name="email" placeholder="user@example.com" required>
        @error('email') <span class="error">{{ $message }}</span> @enderror

        <label>Role</label>
        <select name="role">
            @if(auth()->user()->role === 'superadmin')
                <option value="admin">Admin</option>
            @else
                <option value="admin">Admin</option>
                <option value="member">Member</option>
            @endif
        </select>
        @error('role') <span class="error">{{ $message }}</span> @enderror

        <button type="submit" class="btn btn-success">Send Invitation</button>
        <a href="{{ route('dashboard') }}" class="btn btn-primary" style="margin-left:8px;">Cancel</a>
    </form>
@endsection
