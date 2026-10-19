@php
    $user = auth()->user();
    $is = fn (string ...$patterns) => request()->routeIs(...$patterns);

    $esCoord = $user->hasRole('coordinador');      // coordinador o administrador
    $esEst = $user->hasRole('estudiante');         // estudiante o administrador
    $esEmp = $user->hasRole('empresa');            // empresa o administrador
@endphp

{{-- Logo --}}
<div class="flex h-16 shrink-0 items-center justify-between border-b border-gray-100 px-5">
    <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
        <x-application-logo class="h-9 w-9" />
        <span class="text-lg font-extrabold tracking-wide text-ucsc-700">UCSC</span>
    </a>
    <button type="button" @click="sidebarOpen = false" class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 lg:hidden" aria-label="Cerrar menú">
        <x-icon name="x" class="h-5 w-5" />
    </button>
</div>

<nav class="flex-1 space-y-0.5 overflow-y-auto px-3 py-4" aria-label="Menú principal">

    {{-- ===== Generales ===== --}}
    @if($esCoord)
        <x-sidebar-link :href="route('dashboard')" icon="dashboard" :active="$is('dashboard')">Dashboard</x-sidebar-link>

        <x-sidebar-group label="Gestión y Evaluación de Prácticas" icon="clipboard-check" :open="$is('gestion.*')">
            <x-sidebar-link child :href="route('gestion.practicas.index')" :active="$is('gestion.practicas.*')">Prácticas</x-sidebar-link>
            <x-sidebar-link child :href="route('gestion.evaluaciones.index')" :active="$is('gestion.evaluaciones.*')">Evaluaciones</x-sidebar-link>
            <x-sidebar-link child :href="route('gestion.postulaciones.index')" :active="$is('gestion.postulaciones.*')">Postulaciones</x-sidebar-link>
        </x-sidebar-group>

        <x-sidebar-group label="Administración" icon="database" :open="$is('admin.*')">
            @if($user->isAdmin())
                <x-sidebar-link child :href="route('admin.usuarios.index')" :active="$is('admin.usuarios.*')">Usuarios</x-sidebar-link>
            @endif
            <x-sidebar-link child :href="route('admin.carreras.index')" :active="$is('admin.carreras.*')">Carreras</x-sidebar-link>
            <x-sidebar-link child :href="route('admin.empresas.index')" :active="$is('admin.empresas.*')">Empresas</x-sidebar-link>
            <x-sidebar-link child :href="route('admin.estudiantes.index')" :active="$is('admin.estudiantes.*')">Estudiantes</x-sidebar-link>
            <x-sidebar-link child :href="route('admin.ofertas.index')" :active="$is('admin.ofertas.*')">Ofertas de práctica</x-sidebar-link>
        </x-sidebar-group>
    @endif

    {{-- ===== COORDINADOR ===== --}}
    @if($esCoord)
        <x-sidebar-section title="Coordinador">
            <x-sidebar-link :href="route('coordinador.dashboard')" icon="dashboard" sub="Coordinador" :active="$is('coordinador.dashboard')">Dashboard</x-sidebar-link>
            <x-sidebar-link :href="route('coordinador.gestion')" icon="clipboard-check" :active="$is('coordinador.gestion')">Gestión y Evaluación de Prácticas</x-sidebar-link>
        </x-sidebar-section>
    @endif

    {{-- ===== ESTUDIANTE ===== --}}
    @if($esEst)
        <x-sidebar-section title="Estudiante">
            <x-sidebar-link :href="route('estudiante.dashboard')" icon="squares" :active="$is('estudiante.dashboard')">Mi Dashboard</x-sidebar-link>
            <x-sidebar-link :href="route('estudiante.ofertas.index')" icon="search" :active="$is('estudiante.ofertas.*')">Explorar Ofertas</x-sidebar-link>
            <x-sidebar-link :href="route('estudiante.postulaciones.index')" icon="document" :active="$is('estudiante.postulaciones.*')">Mis Postulaciones</x-sidebar-link>
            <x-sidebar-link :href="route('estudiante.seguimiento')" icon="dashboard" :active="$is('estudiante.seguimiento')">Seguimiento</x-sidebar-link>
        </x-sidebar-section>
    @endif

    {{-- ===== EMPRESA ===== --}}
    @if($esEmp)
        <x-sidebar-section title="Empresa">
            <x-sidebar-link :href="route('empresa.dashboard')" icon="building" sub="Empresa" :active="$is('empresa.dashboard')">Dashboard</x-sidebar-link>
            <x-sidebar-link :href="route('empresa.ofertas.create')" icon="plus-circle" :active="$is('empresa.ofertas.*')">Publicar Oferta</x-sidebar-link>
        </x-sidebar-section>
    @endif
</nav>

{{-- Usuario --}}
<div class="shrink-0 border-t border-gray-100 p-4">
    <div class="flex items-center gap-3">
        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-ucsc-100 text-sm font-bold uppercase text-ucsc-700">
            {{ mb_substr($user->name, 0, 1) }}
        </span>
        <div class="min-w-0">
            <p class="truncate text-sm font-medium text-gray-900">{{ $user->name }}</p>
            <p class="truncate text-xs text-gray-500">{{ $user->roleLabel() }}</p>
        </div>
    </div>
</div>
