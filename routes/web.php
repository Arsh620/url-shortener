<?php

use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShortUrlController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Invitation routes
Route::get('/invite/accept/{token}', [InvitationController::class, 'accept'])->name('invitations.accept');
Route::post('/invite/register/{token}', [InvitationController::class, 'register'])->name('invitations.register');

Route::middleware(['auth', 'role:superadmin,admin'])->group(function () {
    Route::get('/invite', [InvitationController::class, 'create'])->name('invitations.create');
    Route::post('/invite', [InvitationController::class, 'store'])->name('invitations.store');
});

// Public short URL resolver
Route::get('/s/{code}', [ShortUrlController::class, 'resolve'])->name('short-urls.resolve');

// Short URL routes
Route::middleware('auth')->group(function () {
    Route::get('/short-urls', [ShortUrlController::class, 'index'])->name('short-urls.index');
    Route::middleware('role:admin,member')->group(function () {
        Route::get('/short-urls/create', [ShortUrlController::class, 'create'])->name('short-urls.create');
        Route::post('/short-urls', [ShortUrlController::class, 'store'])->name('short-urls.store');
    });
});

require __DIR__.'/auth.php';
