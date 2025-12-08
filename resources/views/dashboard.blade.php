<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Panel principal
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Resumen del sistema de videos del ITSCC.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Tarjetas de métricas rápidas --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100">
                    <div class="p-5">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                            Videos totales
                        </p>
                        <p class="mt-2 text-3xl font-semibold text-gray-900">
                            {{ $totalVideos }}
                        </p>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100">
                    <div class="p-5">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                            Videos activos
                        </p>
                        <p class="mt-2 text-3xl font-semibold text-emerald-600">
                            {{ $activeVideos }}
                        </p>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100">
                    <div class="p-5">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                            Playlists
                        </p>
                        <p class="mt-2 text-3xl font-semibold text-indigo-600">
                            {{ $totalPlaylists }}
                        </p>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100">
                    <div class="p-5">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                            Playlist predeterminada
                        </p>
                        @if($defaultPlaylist)
                            <p class="mt-2 text-sm font-semibold text-gray-900">
                                {{ $defaultPlaylist->name }}
                            </p>
                            <p class="mt-1 text-xs text-gray-500">
                                Estado: <span class="text-emerald-600 font-medium">Activa</span>
                            </p>
                        @else
                            <p class="mt-2 text-sm text-gray-500">
                                No hay playlist default activa.
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Bloque principal: últimos videos subidos --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100">
                <div class="px-6 py-4 flex items-center justify-between border-b border-gray-100">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">
                            Últimos videos subidos
                        </h3>
                        <p class="mt-1 text-xs text-gray-500">
                            Vista rápida de los últimos contenidos agregados al sistema.
                        </p>
                    </div>

                    <a href="{{ route('media-items.index') }}"
                       class="inline-flex items-center gap-1 text-xs font-medium text-indigo-600 hover:text-indigo-700">
                        Ver todos los videos
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                  d="M10.293 3.293a1 1 0 011.414 0L16.414 8l-4.707 4.707a1 1 0 01-1.414-1.414L12.586 9H4a1 1 0 110-2h8.586l-2.293-2.293a1 1 0 010-1.414z"
                                  clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>

                @if($latestVideos->count())
                    <div class="px-6 py-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase tracking-wider text-xs">
                                        Título
                                    </th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase tracking-wider text-xs">
                                        Duración
                                    </th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase tracking-wider text-xs">
                                        Tamaño
                                    </th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase tracking-wider text-xs">
                                        Estado
                                    </th>
                                    <th class="px-4 py-2 text-right font-medium text-gray-500 uppercase tracking-wider text-xs">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach($latestVideos as $video)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2">
                                            <div class="flex flex-col">
                                                <span class="font-medium text-gray-900">
                                                    {{ $video->title }}
                                                </span>
                                                <span class="text-xs text-gray-400 truncate">
                                                    {{ $video->filename }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-2 text-gray-700">
                                            {{ $video->duration_seconds ? gmdate('i:s', $video->duration_seconds) : 'N/D' }}
                                        </td>
                                        <td class="px-4 py-2 text-gray-700">
                                            {{ $video->size_mb ? number_format($video->size_mb, 1) . ' MB' : 'N/D' }}
                                        </td>
                                        <td class="px-4 py-2">
                                            @if($video->active)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    Activo
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-50 text-gray-600 border border-gray-200">
                                                    Inactivo
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-right">
                                            <a href="{{ route('media-items.edit', $video) }}"
                                               class="text-xs font-medium text-indigo-600 hover:text-indigo-700">
                                                Editar
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="px-6 py-6">
                        <p class="text-sm text-gray-500">
                            Aún no hay videos cargados en el sistema. Empieza subiendo tu primer contenido desde el menú
                            <span class="font-medium text-gray-700">Altas → Videos</span>.
                        </p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
