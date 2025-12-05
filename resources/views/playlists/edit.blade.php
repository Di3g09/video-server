<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                Editar playlist
            </h2>
            <a href="{{ route('playlists.index') }}"
               class="text-sm text-gray-500 hover:text-indigo-600 flex items-center gap-1 transition-colors duration-200 w-fit">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Volver al listado
            </a>
        </div>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="bg-white shadow-xl shadow-gray-200/50 rounded-2xl overflow-hidden border border-gray-100">
                <div class="bg-indigo-600 h-2 w-full"></div>

                <div class="p-6 sm:p-10">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Editar información de la playlist</h3>

                    <form action="{{ route('playlists.update', $playlist) }}" method="POST" class="space-y-8">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            {{-- Nombre --}}
                            <div class="sm:col-span-4">
                                <label for="name" class="block text-sm font-medium leading-6 text-gray-900">
                                    Nombre de la playlist
                                </label>
                                <div class="mt-2">
                                    <input type="text" name="name" id="name"
                                           value="{{ old('name', $playlist->name) }}"
                                           class="block w-full rounded-md border-0 py-2.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition duration-150"
                                           required>
                                </div>
                                @error('name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                                <p class="mt-1 text-xs text-gray-500">
                                    Slug actual: <span class="font-mono">{{ $playlist->slug }}</span>
                                </p>
                            </div>

                            {{-- Activa --}}
                            <div class="sm:col-span-2 flex items-end pb-3">
                                <div class="relative flex gap-x-3 items-center">
                                    <div class="flex h-6 items-center">
                                        <input id="active" name="active" type="checkbox" value="1"
                                               {{ old('active', $playlist->active) ? 'checked' : '' }}
                                               class="h-5 w-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 cursor-pointer">
                                    </div>
                                    <div class="text-sm leading-6">
                                        <label for="active" class="font-medium text-gray-900 cursor-pointer">
                                            Activa
                                        </label>
                                    </div>
                                </div>
                            </div>

                            {{-- Default --}}
                            <div class="sm:col-span-6">
                                <div class="flex items-start gap-3 rounded-lg bg-indigo-50 border border-indigo-100 px-4 py-3">
                                    <div class="pt-1">
                                        <input id="is_default" name="is_default" type="checkbox" value="1"
                                               {{ old('is_default', $playlist->is_default) ? 'checked' : '' }}
                                               class="h-4 w-4 rounded border-indigo-300 text-indigo-600 focus:ring-indigo-600 cursor-pointer mt-0.5">
                                    </div>
                                    <div class="text-sm text-indigo-900">
                                        <label for="is_default" class="font-medium cursor-pointer">
                                            Usar como playlist por defecto
                                        </label>
                                        <p class="mt-0.5 text-xs text-indigo-700">
                                            Solo puede haber una playlist por defecto. Si marcas esta opción, cualquier otra playlist que sea default dejará de serlo.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- Descripción --}}
                            <div class="sm:col-span-6">
                                <label for="description" class="block text-sm font-medium leading-6 text-gray-900">
                                    Descripción
                                </label>
                                <div class="mt-2">
                                    <textarea id="description" name="description" rows="3"
                                              class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                              placeholder="Descripción breve de la playlist (opcional)">{{ old('description', $playlist->description) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-x-4 pt-4 border-t border-gray-100">
                            <a href="{{ route('playlists.index') }}"
                               class="text-sm font-semibold leading-6 text-gray-900 hover:text-gray-700">
                                Cancelar
                            </a>
                            <button type="submit"
                                    class="rounded-md bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all duration-200">
                                Guardar cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
