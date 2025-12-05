<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    {{ __('Biblioteca de Videos') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Gestiona tus archivos multimedia</p>
            </div>

            <a href="{{ route('media-items.create') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 border border-transparent rounded-full font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm hover:shadow">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                    <path
                        d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                </svg>
                Nuevo video
            </a>
        </div>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Mensajes de éxito --}}
            @if (session('status'))
                <div class="mb-6 rounded-md bg-green-50 p-4 border border-green-200">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('status') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if ($mediaItems->count())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach ($mediaItems as $item)
                        {{-- Tarjeta de Video --}}
                        <div
                            class="group bg-white rounded-2xl shadow-sm hover:shadow-xl hover:shadow-indigo-100 transition-all duration-300 border border-gray-100 overflow-hidden flex flex-col h-full">

                            {{-- Cabecera Visual: VIDEO REAL COMO MINIATURA --}}
                            {{-- Nota: Asegúrate de que la ruta 'storage/' coincida con donde guardas los archivos --}}
                            <div
                                class="relative h-48 bg-gray-900 flex items-center justify-center group-hover:bg-gray-800 transition-colors duration-300 overflow-hidden">

                                {{-- Video Element --}}
                                <video src="{{ route('media-items.preview', $item) }}"
                                    class="absolute inset-0 w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700"
                                    muted loop playsinline preload="metadata" onmouseenter="this.play()"
                                    onmouseleave="this.pause();"></video>


                                {{-- Overlay sutil para mejorar contraste de badges --}}
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-black/10 to-transparent pointer-events-none">
                                </div>

                                {{-- Badge de Estado --}}
                                <div class="absolute top-3 right-3 z-10">
                                    @if ($item->active)
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-green-500/90 text-white shadow-sm backdrop-blur-sm">
                                            Activo
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-500/90 text-white shadow-sm backdrop-blur-sm">
                                            Inactivo
                                        </span>
                                    @endif
                                </div>

                                {{-- Icono de Play (aparece si el video no carga o como indicador) --}}
                                <div
                                    class="absolute z-0 pointer-events-none opacity-50 group-hover:opacity-0 transition-opacity duration-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="white" class="w-12 h-12">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z" />
                                    </svg>
                                </div>
                            </div>

                            {{-- Cuerpo de la tarjeta --}}
                            <div class="p-5 flex flex-col flex-grow">
                                <div class="flex-grow">
                                    <h3
                                        class="font-bold text-gray-900 text-lg leading-tight line-clamp-1 hover:text-indigo-600 transition-colors">
                                        {{ $item->title }}
                                    </h3>

                                    <p class="text-xs font-mono text-gray-500 mt-1 truncate"
                                        title="{{ $item->filename }}">
                                        {{ $item->filename }}
                                    </p>

                                    {{-- Metadata Pills --}}
                                    <div class="flex items-center gap-2 mt-4">
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-gray-50 text-xs font-medium text-gray-600 border border-gray-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                fill="currentColor" class="w-3 h-3 text-gray-400">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25V5z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ $item->duration_seconds ? gmdate('i:s', $item->duration_seconds) : 'N/D' }}
                                        </span>
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-gray-50 text-xs font-medium text-gray-600 border border-gray-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                fill="currentColor" class="w-3 h-3 text-gray-400">
                                                <path
                                                    d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.977A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z" />
                                                <path d="M9 13h2v5a1 1 0 11-2 0v-5z" />
                                            </svg>
                                            {{ $item->size_mb ? number_format($item->size_mb, 1) . ' MB' : 'N/D' }}
                                        </span>
                                    </div>

                                    @if ($item->notes)
                                        <p
                                            class="mt-3 text-xs text-gray-500 line-clamp-2 italic border-l-2 border-gray-200 pl-2">
                                            {{ $item->notes }}
                                        </p>
                                    @endif
                                </div>

                                {{-- Botones de Acción --}}
                                <div class="mt-5 pt-4 border-t border-gray-100 grid grid-cols-2 gap-3">
                                    <a href="{{ route('media-items.edit', $item) }}"
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

                                    <form method="POST" action="{{ route('media-items.destroy', $item) }}" 
                                    data-confirm="¿Eliminar este video? Si se encuentra en alguna playlist, también se eliminara."
                                    >
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

                {{-- Paginación mejorada --}}
                <div class="mt-8">
                    {{ $mediaItems->links() }}
                </div>
            @else
                {{-- Empty State --}}
                <div class="bg-white shadow-sm rounded-2xl p-12 text-center border border-dashed border-gray-300">
                    <div class="mx-auto h-24 w-24 bg-indigo-50 rounded-full flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-10 h-10 text-indigo-500">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                    </div>
                    <h3 class="mt-2 text-lg font-semibold text-gray-900">No hay videos aún</h3>
                    <p class="mt-1 text-sm text-gray-500">Comienza a subir contenido para verlo aquí.</p>
                    <div class="mt-6">
                        <a href="{{ route('media-items.create') }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            Subir mi primer video
                        </a>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
