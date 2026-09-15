<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prácticas UCSC - @yield('title', 'Panel')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900 antialiased overflow-hidden flex h-screen">

    <div id="sidebar-overlay"
        class="fixed inset-0 bg-slate-900/60 z-40 hidden lg:hidden backdrop-blur-sm transition-opacity"
        onclick="toggleSidebar()"></div>

    <aside id="sidebar"
        class="fixed inset-y-0 left-0 w-64 bg-white flex flex-col z-50 transform -translate-x-full lg:translate-x-0 lg:static lg:shrink-0 transition-transform duration-300 ease-in-out border-r border-slate-200 shadow-sm lg:shadow-none">

        <div class="h-20 flex items-center justify-center border-b border-slate-100 bg-white shrink-0 px-4 relative">
            <img src="https://upload.wikimedia.org/wikipedia/commons/7/7a/UCSC,_Universidad_Cat%C3%B3lica_de_la_Sant%C3%ADsima_Concepci%C3%B3n.png"
                alt="Logo UCSC" class="h-12 object-contain">
            <button onclick="toggleSidebar()"
                class="absolute right-4 lg:hidden text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-3 overflow-y-auto">

            @role('Administrador')
                <a href="#"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('admin.dashboard*') ? 'text-white bg-slate-900 shadow-sm' : 'text-slate-700 hover:bg-slate-100' }}">
                    <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.dashboard*') ? 'text-white' : 'text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                    <span class="leading-tight">Dashboard<br><span
                            class="text-xs {{ request()->routeIs('admin.dashboard*') ? 'text-slate-300' : 'text-slate-500' }} font-normal"></span></span>
                </a>

                <a href="#"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('admin.practices*') ? 'text-white bg-slate-900 shadow-sm' : 'text-slate-700 hover:bg-slate-100' }}">
                    <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.practices*') ? 'text-white' : 'text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                        </path>
                    </svg>
                    <span class="leading-tight">Gestión y Evaluación de Prácticas</span>
                </a>

                <a href="#"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('admin.administration*') ? 'text-white bg-slate-900 shadow-sm' : 'text-slate-700 hover:bg-slate-100' }}">
                    <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.administration*') ? 'text-white' : 'text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4">
                        </path>
                    </svg>
                    <span>Administración</span>
                </a>
            @endrole

            @role('Coordinador')
                <a href="#"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('coordinator.dashboard*') ? 'text-white bg-slate-900 shadow-sm' : 'text-slate-700 hover:bg-slate-100' }}">
                    <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('coordinator.dashboard*') ? 'text-white' : 'text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                    <span class="leading-tight">Dashboard<br><span
                            class="text-xs {{ request()->routeIs('coordinator.dashboard*') ? 'text-slate-300' : 'text-slate-500' }} font-normal">Coordinador</span></span>
                </a>

                <a href="#"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('coordinator.practices*') ? 'text-white bg-slate-900 shadow-sm' : 'text-slate-700 hover:bg-slate-100' }}">
                    <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('coordinator.practices*') ? 'text-white' : 'text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                        </path>
                    </svg>
                    <span class="leading-tight">Gestión y Evaluación de Prácticas</span>
                </a>
            @endrole

            @role('Estudiante')
                <a href="#"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('student.dashboard') ? 'text-white bg-slate-900 shadow-sm' : 'text-slate-700 hover:bg-slate-100' }}">
                    <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('student.dashboard') ? 'text-white' : 'text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                        </path>
                    </svg>
                    <span>Mi Dashboard</span>
                </a>

                <a href="#"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('student.offers*') ? 'text-white bg-slate-900 shadow-sm' : 'text-slate-700 hover:bg-slate-100' }}">
                    <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('student.offers*') ? 'text-white' : 'text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <span>Explorar Ofertas</span>
                </a>

                <a href="#"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('student.applications*') ? 'text-white bg-slate-900 shadow-sm' : 'text-slate-700 hover:bg-slate-100' }}">
                    <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('student.applications*') ? 'text-white' : 'text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    <span>Mis Postulaciones</span>
                </a>

                <a href="#"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('student.tracking*') ? 'text-white bg-slate-900 shadow-sm' : 'text-slate-700 hover:bg-slate-100' }}">
                    <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('student.tracking*') ? 'text-white' : 'text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                    <span>Seguimiento</span>
                </a>
            @endrole

            @role('Empresa')
                <a href="#"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('company.dashboard*') ? 'text-white bg-slate-900 shadow-sm' : 'text-slate-700 hover:bg-slate-100' }}">
                    <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('company.dashboard*') ? 'text-white' : 'text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                    <span class="leading-tight">Dashboard<br><span
                            class="text-xs {{ request()->routeIs('company.dashboard*') ? 'text-slate-300' : 'text-slate-500' }} font-normal">Empresa</span></span>
                </a>

                <a href="#"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('company.offers*') ? 'text-white bg-slate-900 shadow-sm' : 'text-slate-700 hover:bg-slate-100' }}">
                    <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('company.offers*') ? 'text-white' : 'text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Publicar Oferta</span>
                </a>
            @endrole

        </nav>
    </aside>


    <div class="flex-1 flex flex-col min-w-0 overflow-hidden relative">

        <header
            class="h-20 bg-red-700 shadow-md flex items-center justify-between px-4 sm:px-8 z-10 shrink-0 sticky top-0 border-b border-red-800">
            <div class="flex items-center gap-4 w-full">
                <!-- Botón Menú Móvil -->
                <button onclick="toggleSidebar()"
                    class="p-2 -ml-2 text-white/80 hover:bg-red-800 hover:text-white rounded-lg lg:hidden transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

                <div class="relative hidden sm:block w-96 max-w-md group">
                    <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-red-300 transition-colors"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" placeholder="Buscar registros..."
                        class="pl-10 pr-4 py-2 bg-red-800/60 border border-red-600/60 text-white placeholder-red-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-white focus:bg-red-800 focus:border-transparent w-full transition-all shadow-inner">
                </div>
            </div>

            <div class="flex items-center gap-3">
                <!-- Contenedor de Notificaciones -->
                <div class="relative" id="notifications-container">
                    <button onclick="toggleNotifications()" type="button"
                        class="relative p-2 text-white/90 hover:text-white transition-colors focus:outline-none rounded-full hover:bg-red-800">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                            </path>
                        </svg>
                        <span
                            class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-amber-400 border-2 border-red-700 rounded-full"></span>
                    </button>

                    <div id="notifications-menu"
                        class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] border border-slate-100 z-50 overflow-hidden transform opacity-0 scale-95 transition-all duration-200 origin-top-right">
                        <div
                            class="px-4 py-3 border-b border-slate-100 flex justify-between items-center bg-slate-50/80">
                            <h3 class="text-sm font-bold text-slate-800">Notificaciones</h3>
                            <span
                                class="bg-red-100 text-red-700 text-[10px] uppercase font-bold px-2 py-0.5 rounded-full">1
                                Nueva</span>
                        </div>
                        <div class="max-h-80 overflow-y-auto">
                            <a href="#"
                                class="block px-4 py-3 hover:bg-slate-50 border-b border-slate-50 transition-colors bg-red-50/30">
                                <div class="flex gap-3">
                                    <div
                                        class="w-8 h-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center shrink-0 mt-0.5 font-bold">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-slate-800 font-semibold">Sistema actualizado</p>
                                        <p class="text-xs text-slate-600 mt-0.5 leading-relaxed">Los datos de las
                                            carreras han sido sincronizados.</p>
                                        <p class="text-[10px] font-medium text-red-600 mt-1">Hace 5 min</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="h-6 w-px bg-red-600 mx-1"></div>

                <div class="relative" id="user-menu-container">
                    <button onclick="toggleUserMenu()" type="button"
                        class="flex items-center gap-2 focus:outline-none rounded-full transition-shadow group">
                        <div
                            class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-600 to-indigo-700 text-white flex items-center justify-center font-bold text-sm shadow-sm group-hover:ring-2 group-hover:ring-white transition-all select-none">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}{{ strtoupper(substr(auth()->user()->last_name, 0, 1)) }}
                        </div>
                    </button>

                    <div id="user-menu"
                        class="hidden absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] border border-slate-100 z-50 overflow-hidden transform opacity-0 scale-95 transition-all duration-200 origin-top-right">
                        <!-- Cabecera de usuario -->
                        <div class="px-4 py-4 border-b border-slate-100 bg-slate-50/50">
                            <p class="text-sm font-bold text-slate-800">{{ auth()->user()->name }}
                                {{ auth()->user()->last_name }}</p>
                            <p class="text-xs text-slate-500 truncate mt-0.5">{{ auth()->user()->email }}</p>
                        </div>
                        <div class="py-1">
                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                @csrf
                                <button type="submit"
                                    class="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors text-left font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                        </path>
                                    </svg>
                                    Cerrar sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto bg-slate-50/50 p-4 sm:p-6 lg:p-8">
            <div class="max-w-[1400px] mx-auto">
                <!-- AQUI VA EL CONTENIDO DE CADA VISTA -->
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        function toggleMenu(menuId) {
            const menu = document.getElementById(menuId);
            const otherMenuId = menuId === 'notifications-menu' ? 'user-menu' : 'notifications-menu';
            const otherMenu = document.getElementById(otherMenuId);

            if (otherMenu && !otherMenu.classList.contains('hidden')) {
                otherMenu.classList.add('hidden', 'opacity-0', 'scale-95');
                otherMenu.classList.remove('opacity-100', 'scale-100');
            }

            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                setTimeout(() => {
                    menu.classList.remove('opacity-0', 'scale-95');
                    menu.classList.add('opacity-100', 'scale-100');
                }, 10);
            } else {
                menu.classList.remove('opacity-100', 'scale-100');
                menu.classList.add('opacity-0', 'scale-95');
                setTimeout(() => {
                    menu.classList.add('hidden');
                }, 200);
            }
        }

        function toggleNotifications() {
            toggleMenu('notifications-menu');
        }

        function toggleUserMenu() {
            toggleMenu('user-menu');
        }

        document.addEventListener('click', function(event) {
            const notiContainer = document.getElementById('notifications-container');
            const notiMenu = document.getElementById('notifications-menu');
            const userContainer = document.getElementById('user-menu-container');
            const userMenu = document.getElementById('user-menu');

            if (notiContainer && notiMenu && !notiContainer.contains(event.target) && !notiMenu.classList.contains(
                    'hidden')) {
                toggleMenu('notifications-menu');
            }
            if (userContainer && userMenu && !userContainer.contains(event.target) && !userMenu.classList.contains(
                    'hidden')) {
                toggleMenu('user-menu');
            }
        });
    </script>
</body>

</html>
