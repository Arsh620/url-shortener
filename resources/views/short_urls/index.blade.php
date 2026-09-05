@extends('layouts.main')
@section('title', 'Short URLs')
@section('content')
    <h2>Short URLs</h2>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    @if(in_array(auth()->user()->role, ['admin', 'member']))
        <a href="{{ route('short-urls.create') }}" class="btn btn-success" style="margin-bottom:16px; display:inline-block;">+ Create New Short URL</a>
    @endif

    <table>
        <tr>
            <th>Original URL</th>
            <th>Short Link</th>
            <th>Created By</th>
            @if(auth()->user()->role === 'superadmin')
                <th>Role</th>
                <th>Company</th>
            @endif
            <th>Created At</th>
        </tr>
        @forelse($urls as $url)
        <tr>
            <td>{{ $url->original_url }}</td>
            <td><a href="{{ url('/s/' . $url->short_code) }}" target="_blank">{{ url('/s/' . $url->short_code) }}</a></td>
            <td>{{ $url->user->name }}</td>
            @if(auth()->user()->role === 'superadmin')
                <td><span class="badge badge-{{ $url->user->role }}">{{ $url->user->role }}</span></td>
                <td>{{ $url->user->company->name ?? 'N/A' }}</td>
            @endif
            <td>{{ $url->created_at->format('d M Y') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="6" style="text-align:center; color:#718096;">No short URLs found.</td>
        </tr>
        @endforelse
    </table>
@endsection
