@extends('layouts.main')
@section('title', 'Accept Invitation')
@section('content')
    <h2>Accept Invitation</h2>

    <p style="margin-bottom:16px; color:#718096;">
        You have been invited as <span class="badge badge-{{ $invitation->role }}">{{ $invitation->role }}</span>
        &nbsp;— Email: <strong>{{ $invitation->email }}</strong>
    </p>

    <form method="POST" action="{{ route('invitations.register', $invitation->token) }}">
        @csrf

        <label>Name</label>
        <input type="text" name="name" placeholder="Your full name" required>
        @error('name') <span class="error">{{ $message }}</span> @enderror

        <label>Password</label>
        <input type="password" name="password" required>
        @error('password') <span class="error">{{ $message }}</span> @enderror

        <label>Confirm Password</label>
        <input type="password" name="password_confirmation" required>

        <button type="submit" class="btn btn-success">Register</button>
    </form>
@endsection
