<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'UCSC Prácticas') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-100 font-sans text-gray-900 antialiased">
        <div x-data="{ sidebarOpen: false }" @keydown.escape.window="sidebarOpen = false" class="min-h-screen lg:flex">

            {{-- Fondo oscuro del menú en móvil --}}
            <div x-show="sidebarOpen" x-cloak x-transition.opacity @click="sidebarOpen = false"
                 class="fixed inset-0 z-30 bg-gray-900/50 lg:hidden" aria-hidden="true"></div>

            {{-- Sidebar --}}
            <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                   class="fixed inset-y-0 left-0 z-40 flex w-72 max-w-[85vw] flex-col border-r border-gray-200 bg-white transition-transform duration-200 lg:sticky lg:top-0 lg:h-screen lg:shrink-0 lg:translate-x-0">
                @include('layouts.sidebar')
            </aside>

            <div class="flex min-w-0 flex-1 flex-col">
                {{-- Barra superior --}}
                <header class="sticky top-0 z-20 flex h-16 items-center gap-3 bg-ucsc-700 px-3 text-white shadow sm:px-6">
                    <button type="button" @click="sidebarOpen = true" class="rounded-lg p-2 hover:bg-white/10 lg:hidden" aria-label="Abrir menú">
                        <x-icon name="bars" class="h-6 w-6" />
                    </button>

                    @isset($searchUrl)
                        <form method="GET" action="{{ $searchUrl }}" class="relative min-w-0 max-w-xl flex-1" role="search">
                            <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-white/70" />
                            <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar registros..."
                                   class="w-full rounded-lg border-0 bg-white/15 py-2 pl-9 pr-3 text-sm text-white placeholder-white/70 focus:bg-white/25 focus:ring-2 focus:ring-white/60">
                        </form>
                    @else
                        <div class="flex-1"></div>
                    @endisset

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button type="button" class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm hover:bg-white/10 focus:outline-none">
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-white/20 text-xs font-bold uppercase">
                                    {{ mb_substr(Auth::user()->name, 0, 1) }}
                                </span>
                                <span class="hidden text-left leading-tight sm:block">
                                    <span class="block max-w-[10rem] truncate font-medium">{{ Auth::user()->name }}</span>
                                    <span class="block text-xs text-white/70">{{ Auth::user()->roleLabel() }}</span>
                                </span>
                                <x-icon name="chevron-down" class="hidden h-4 w-4 sm:block" />
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </header>

                {{-- Encabezado opcional (páginas de Breeze como Perfil) --}}
                @isset($header)
                    <div class="border-b border-gray-200 bg-white">
                        <div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </div>
                @endisset

                <main class="flex-1">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
