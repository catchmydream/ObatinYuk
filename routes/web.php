<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/katalog', \App\Livewire\Katalog::class)->name('home');
Route::get('/search', \App\Livewire\Katalog::class)->name('search');
Route::get('/produk/{id}', \App\Livewire\ProductDetail::class)->name('produk.detail');
Route::get('/konsultasi', \App\Livewire\Konsultasi::class)->name('konsultasi');

Route::get('/login', \App\Livewire\Auth\Login::class)->name('login')->middleware('guest');
Route::get('/register', \App\Livewire\Auth\Register::class)->name('register')->middleware('guest');
Route::get('/forgot-password', \App\Livewire\Auth\ForgotPassword::class)->name('password.request')->middleware('guest');

Route::post('/logout', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/profil', \App\Livewire\Profile::class)->name('profil');
    Route::get('/keranjang', \App\Livewire\Keranjang::class)->name('keranjang');
    Route::get('/checkout', \App\Livewire\Checkout::class)->name('checkout');
    Route::get('/pesanan-saya', \App\Livewire\HistoriPesanan::class)->name('pesanan-saya');
    Route::post('/api/chatbot', [\App\Http\Controllers\ChatbotController::class, 'send'])->name('chatbot.send');
});
