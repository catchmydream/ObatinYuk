<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'ObatinYuk — Kesehatan Digital' }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen font-sans antialiased bg-base-200/50">

    {{-- NAVBAR --}}
    <x-nav sticky class="glass-effect z-50">
        <x-slot:brand>
            <a href="/" class="flex items-center gap-2 text-2xl font-extrabold text-primary decoration-transparent">
                <x-icon name="o-heart" class="w-8 h-8 text-secondary" />
                Obatin<span class="text-base-content">Yuk</span>
            </a>
        </x-slot:brand>

        <x-slot:actions>
            @if(!request()->is('login') && !request()->is('register'))
                <span class="hidden lg:block w-72" style="margin-right: 2.5rem; display: block;">
                    @livewire('global-search')
                </span>
            @endif
            <a href="/konsultasi" class="btn btn-primary text-white rounded-xl shadow-sm shadow-blue-500/20 gap-1.5">
                <x-icon name="o-sparkles" class="w-5 h-5 text-amber-300" />
                <span>Konsultasi AI</span>
            </a>

            <a href="/katalog" class="btn btn-ghost">
                <x-icon name="o-building-storefront" class="w-5 h-5" />
                <span class="hidden sm:inline">Katalog</span>
            </a>

            @auth
                @php
                    $cartCount = \App\Models\CartItem::where('user_id', Auth::id())->sum('quantity');
                @endphp
                
                <a href="/keranjang" class="btn btn-ghost indicator">
                    <x-icon name="o-shopping-cart" class="w-5 h-5" />
                    @if($cartCount > 0)
                        <span class="indicator-item badge badge-secondary badge-sm">{{ $cartCount > 9 ? '9+' : $cartCount }}</span>
                    @endif
                </a>

                <x-dropdown>
                    <x-slot:trigger>
                        <x-button icon="o-user" class="btn-circle btn-ghost" />
                    </x-slot:trigger>
                    
                    <x-menu-item title="Pesanan Saya" icon="o-archive-box" link="/pesanan-saya" />
                    @if(Auth::user()->role === 'admin')
                        <x-menu-item title="Admin Panel" icon="o-cog-6-tooth" link="/admin" />
                    @endif
                    <x-menu-separator />
                    <form action="/logout" method="POST">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 hover:bg-base-200 text-error flex items-center gap-2">
                            <x-icon name="o-arrow-right-on-rectangle" class="w-4 h-4" />
                            Keluar
                        </button>
                    </form>
                </x-dropdown>
            @else
                <a href="/login" class="btn btn-ghost">Masuk</a>
                <a href="/register" class="btn btn-primary text-white rounded-xl">Daftar</a>
            @endauth
        </x-slot:actions>
    </x-nav>

    {{-- MAIN CONTENT --}}
    <x-main full-width>
        <x-slot:content class="!p-0">
            {{ $slot }}
        </x-slot:content>
    </x-main>

    {{-- FOOTER --}}
    <footer class="footer p-10 bg-base-100 text-base-content border-t border-base-300 mt-10">
        <aside>
            <x-icon name="o-heart" class="w-10 h-10 text-secondary" />
            <p class="font-bold text-lg">ObatinYuk<br/><span class="text-sm font-normal text-base-content/70">Kesehatan digital masa depan.</span></p>
        </aside>
        <nav>
            <h6 class="footer-title">Layanan</h6>
            <a class="link link-hover">Konsultasi AI</a>
            <a class="link link-hover">Beli Obat</a>
        </nav>
        <nav>
            <h6 class="footer-title">Perusahaan</h6>
            <a class="link link-hover">Tentang Kami</a>
            <a class="link link-hover">Kontak</a>
        </nav>
    </footer>



    <x-toast />
    @livewireScripts
</body>
</html>
