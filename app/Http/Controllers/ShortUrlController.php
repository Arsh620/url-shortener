<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShortUrlController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Each role sees a different scope of URLs:
        // SuperAdmin sees all URLs across all companies
        // Admin sees only URLs belonging to their own company
        // Member sees only URLs they personally created
        $urls = match($user->role) {
            'superadmin' => ShortUrl::with('user')->latest()->get(),
            'admin'      => ShortUrl::with('user')->where('company_id', $user->company_id)->latest()->get(),
            'member'     => ShortUrl::with('user')->where('user_id', $user->id)->latest()->get(),
        };

        return view('short_urls.index', compact('urls'));
    }

    // Show the create short URL form
    public function create()
    {
        return view('short_urls.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'original_url' => 'required|url',
        ]);

        $user = auth()->user();

        // Generate a random 6-character short code
        // company_id is stored so admin can filter URLs by company
        ShortUrl::create([
            'user_id'      => $user->id,
            'company_id'   => $user->company_id,
            'original_url' => $request->original_url,
            'short_code'   => Str::random(6),
        ]);

        return redirect()->route('short-urls.index')->with('success', 'Short URL created!');
    }

    // Publicly accessible route — no auth required
    // Resolves the short code and redirects to the original URL
    public function resolve($code)
    {
        $shortUrl = ShortUrl::where('short_code', $code)->firstOrFail();
        return redirect($shortUrl->original_url);
    }
}
