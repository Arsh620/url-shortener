@extends('layouts.main')
@section('title', 'Create Short URL')
@section('content')
    <h2>Create Short URL</h2>

    <form method="POST" action="{{ route('short-urls.store') }}">
        @csrf
        <label>Original URL</label>
        <input type="url" name="original_url" placeholder="https://example.com" required>
        @error('original_url') <span class="error">{{ $message }}</span> @enderror

        <button type="submit" class="btn btn-success">Create Short URL</button>
        <a href="{{ route('short-urls.index') }}" class="btn btn-primary" style="margin-left:8px;">Cancel</a>
    </form>
@endsection
