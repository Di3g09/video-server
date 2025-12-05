<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    Listado de Playlists
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Administra las listas de reproducción que se usarán en el canal.
                </p>
            </div>

            <a href="{{ route('playlists.create') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 border border-transparent rounded-full font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm hover:shadow">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                    <path
                        d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                </svg>
                Nueva playlist
            </a>
        </div>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if ($playlists->count())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach ($playlists as $playlist)
                        <div
                            class="group bg-white rounded-2xl shadow-sm hover:shadow-xl hover:shadow-indigo-100 transition-all duration-300 border border-gray-100 overflow-hidden flex flex-col h-full">

                            {{-- Cabecera visual --}}
                            <div class="relative h-24 bg-gradient-to-r from-indigo-500 via-indigo-600 to-indigo-700">
                                {{-- Badges --}}
                                <div class="absolute top-3 left-3 flex flex-col gap-2">
                                    @if ($playlist->is_default)
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-md text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200 shadow-sm">
                                            ✨ Default
                                        </span>
                                    @endif

                                    @if ($playlist->active)
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-green-100 text-green-700 border border-green-200">
                                            Activa
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                            Inactiva
                                        </span>
                                    @endif
                                </div>

                                {{-- Ícono decorativo --}}
                                <div class="absolute inset-0 flex items-center justify-end pr-4 opacity-40">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white"
                                        class="w-12 h-12">
                                        <path
                                            d="M4.5 5.25A2.25 2.25 0 016.75 3H17.25A2.25 2.25 0 0119.5 5.25v13.5A2.25 2.25 0 0117.25 21H6.75A2.25 2.25 0 014.5 18.75V5.25zM9 8.25a.75.75 0 000 1.5h6a.75.75 0 000-1.5H9zM8.25 12a.75.75 0 01.75-.75h6a.75.75 0 010 1.5H9a.75.75 0 01-.75-.75zM9 14.25a.75.75 0 000 1.5h3a.75.75 0 000-1.5H9z" />
                                    </svg>
                                </div>
                            </div>

                            {{-- Cuerpo --}}
                            <div class="p-5 flex flex-col flex-grow">
                                <div class="flex-grow">
                                    <h3 class="font-bold text-gray-900 text-lg leading-tight line-clamp-1">
                                        {{ $playlist->name }}
                                    </h3>

                                    <p class="text-xs text-gray-400 font-mono mt-1">
                                        {{ $playlist->slug }}
                                    </p>

                                    @if ($playlist->description)
                                        <p class="mt-3 text-xs text-gray-600 line-clamp-3">
                                            {{ $playlist->description }}
                                        </p>
                                    @endif
                                </div>

                                {{-- Footer --}}
                                <div class="mt-5 pt-4 border-t border-gray-100 grid grid-cols-2 gap-3">

                                    <a href="{{ route('playlists.items.edit', $playlist) }}"
                                        class="flex items-center justify-center gap-1.5 px-3 py-2 text-sm font-medium text-sky-700 bg-sky-50 rounded-lg hover:bg-sky-100 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                            class="w-4 h-4">
                                            <path
                                                d="M4 4.75A2.75 2.75 0 016.75 2h6.5A2.75 2.75 0 0116 4.75v10.5A2.75 2.75 0 0113.25 18h-6.5A2.75 2.75 0 014 15.25V4.75zM7 6a1 1 0 000 2h6a1 1 0 100-2H7zm0 3.5a1 1 0 000 2h6a1 1 0 100-2H7z" />
                                        </svg>
                                        Contenido
                                    </a>

                                    <a href="{{ route('playlists.edit', $playlist) }}"
                                        class="flex items-center justify-center gap-1.5 px-3 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                            class="w-4 h-4">
                                            <path
                                                d="M5.433 13.917l1.262-3.155A4 4 0 017.58 9.42l6.92-6.918a2.121 2.121 0 013 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 01-.65-.65z" />
                                            <path
                                                d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H10A.75.75 0 0010 3H4.75A2.75 2.75 0 002 5.75v9.5A2.75 2.75 0 004.75 18h9.5A2.75 2.75 0 0017 15.25V10a.75.75 0 00-1.5 0v5.25c0 .69-.56 1.25-1.25 1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5z" />
                                        </svg>
                                        Editar
                                    </a>

                                    <form method="POST" action="{{ route('playlists.destroy', $playlist) }}"
                                        data-confirm="¿Eliminar esta playlist? Si tiene items, también se eliminarán.">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="w-full flex items-center justify-center gap-1.5 px-3 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition-all">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                fill="currentColor" class="w-4 h-4">
                                                <path fill-rule="evenodd"
                                                    d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            Borrar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $playlists->links() }}
                </div>
            @else
                <div class="bg-white shadow-sm rounded-2xl p-12 text-center border border-dashed border-gray-300">
                    <div class="mx-auto h-24 w-24 bg-indigo-50 rounded-full flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-10 h-10 text-indigo-500">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                    </div>
                    <h3 class="mt-2 text-lg font-semibold text-gray-900">No hay playlists aún</h3>
                    <p class="mt-1 text-sm text-gray-500">Crea una playlist para comenzar a organizar tus videos.</p>
                    <div class="mt-6">
                        <a href="{{ route('playlists.create') }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            Crear mi primera playlist
                        </a>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
