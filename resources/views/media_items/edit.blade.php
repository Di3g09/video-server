<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                Editar video
            </h2>
            {{-- Botón discreto para volver --}}
            <a href="{{ route('media-items.index') }}" class="text-sm text-gray-500 hover:text-indigo-600 flex items-center gap-1 transition-colors duration-200 w-fit">
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
                
                {{-- Encabezado visual --}}
                <div class="bg-indigo-600 h-2 w-full"></div>

                <div class="p-6 sm:p-10">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Editar información del contenido</h3>

                    <form action="{{ route('media-items.update', $mediaItem) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            
                            {{-- Título --}}
                            <div class="sm:col-span-4">
                                <label for="title" class="block text-sm font-medium leading-6 text-gray-900">Título del video</label>
                                <div class="mt-2">
                                    <input type="text" name="title" id="title" value="{{ old('title', $mediaItem->title) }}" 
                                        class="block w-full rounded-md border-0 py-2.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition duration-150"
                                        required>
                                </div>
                                @error('title')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Estado Activo --}}
                            <div class="sm:col-span-2 flex items-end pb-3">
                                <div class="relative flex gap-x-3 items-center">
                                    <div class="flex h-6 items-center">
                                        <input id="active" name="active" type="checkbox" value="1" {{ old('active', $mediaItem->active) ? 'checked' : '' }}
                                            class="h-5 w-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 cursor-pointer">
                                    </div>
                                    <div class="text-sm leading-6">
                                        <label for="active" class="font-medium text-gray-900 cursor-pointer">Activo</label>
                                    </div>
                                </div>
                            </div>

                            {{-- Sección de Archivo --}}
                            <div class="sm:col-span-6" x-data="videoUploader()">
                                <label class="block text-sm font-medium leading-6 text-gray-900">Archivo de video</label>

                                {{-- Info del archivo actual (Solo informativo) --}}
                                <div class="mt-2 mb-4 bg-blue-50 border border-blue-100 rounded-lg p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 bg-blue-100 rounded-full text-blue-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-blue-900">Archivo Actual: <span class="font-normal">{{ $mediaItem->filename }}</span></p>
                                            <div class="flex gap-3 text-xs text-blue-700 mt-0.5">
                                                <span>{{ $mediaItem->size_mb ? number_format($mediaItem->size_mb, 2) . ' MB' : 'Tamaño desconocido' }}</span>
                                                <span>&bull;</span>
                                                <span>{{ $mediaItem->duration_seconds ? $mediaItem->duration_seconds . ' segundos' : 'Duración desconocida' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-xs text-blue-600 italic bg-white/50 px-2 py-1 rounded">
                                        Sube uno nuevo solo si deseas reemplazarlo
                                    </div>
                                </div>
                                
                                {{-- Zona de Drag & Drop para REEMPLAZAR --}}
                                <div 
                                    class="mt-2 flex justify-center rounded-lg border border-dashed px-6 py-10 transition-all duration-200 relative"
                                    :class="{ 'border-indigo-500 bg-indigo-50 ring-2 ring-indigo-200': isDragging, 'border-gray-900/25 bg-gray-50 hover:bg-gray-100': !isDragging }"
                                    @dragover.prevent="isDragging = true"
                                    @dragleave.prevent="isDragging = false"
                                    @drop.prevent="handleDrop($event)"
                                >
                                    
                                    {{-- Vista por defecto: Dropzone --}}
                                    <div class="text-center" x-show="!previewUrl">
                                        <svg class="mx-auto h-12 w-12 text-gray-300" :class="{ 'text-indigo-500': isDragging }" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M1.5 6a2.25 2.25 0 012.25-2.25h16.5A2.25 2.25 0 0122.5 6v12a2.25 2.25 0 01-2.25 2.25H3.75A2.25 2.25 0 011.5 18V6zM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0021 18v-1.94l-2.69-2.689a1.5 1.5 0 00-2.12 0l-.88.879.97.97a.75.75 0 11-1.06 1.06l-5.16-5.159a1.5 1.5 0 00-2.12 0L3 16.061zm10.125-7.81a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0z" clip-rule="evenodd" />
                                        </svg>
                                        <div class="mt-4 flex text-sm leading-6 text-gray-600 justify-center">
                                            <label for="file-upload" class="relative cursor-pointer rounded-md font-semibold text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-offset-2 hover:text-indigo-500" :class="{ 'bg-transparent': isDragging, 'bg-white': !isDragging }">
                                                <span>Selecciona para reemplazar</span>
                                                <input id="file-upload" name="file" type="file" class="sr-only" accept="video/mp4" @change="updatePreview" x-ref="input">
                                            </label>
                                            <p class="pl-1">o arrastra</p>
                                        </div>
                                        <p class="text-xs leading-5 text-gray-600">MP4 hasta 200MB</p>
                                    </div>

                                    {{-- Vista Previa del NUEVO Video --}}
                                    <div x-show="previewUrl" class="relative w-full max-w-md mx-auto" style="display: none;">
                                        <div class="mb-2 text-xs font-semibold uppercase tracking-wide text-indigo-600 text-center">Nuevo video seleccionado:</div>
                                        <video class="w-full rounded-lg shadow-lg border border-gray-200" controls x-bind:src="previewUrl">
                                            Tu navegador no soporta videos HTML5.
                                        </video>
                                        
                                        {{-- Nombre del archivo --}}
                                        <p class="mt-2 text-center text-sm text-gray-500" x-text="fileName"></p>

                                        {{-- Botón Cancelar Subida (Vuelve a mostrar opción de carga) --}}
                                        <button type="button" @click="clearPreview" class="absolute -top-3 -right-3 bg-red-100 text-red-600 p-1.5 rounded-full hover:bg-red-200 shadow-sm transition-colors" title="Cancelar cambio de video">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>

                                </div>
                                @error('file')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Notas --}}
                            <div class="sm:col-span-6">
                                <label for="notes" class="block text-sm font-medium leading-6 text-gray-900">Notas adicionales</label>
                                <div class="mt-2">
                                    <textarea id="notes" name="notes" rows="3" 
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                    placeholder="Descripción corta o notas internas...">{{ old('notes', $mediaItem->notes) }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Botones de Acción --}}
                        <div class="flex items-center justify-end gap-x-4 pt-4 border-t border-gray-100">
                            <a href="{{ route('media-items.index') }}" class="text-sm font-semibold leading-6 text-gray-900 hover:text-gray-700">Cancelar</a>
                            <button type="submit" class="rounded-md bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all duration-200">
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Script de Alpine.js --}}
    <script>
        function videoUploader() {
            return {
                previewUrl: null,
                fileName: null,
                isDragging: false,
                
                updatePreview(event) {
                    const file = event.target.files[0];
                    this.processFile(file);
                },

                handleDrop(event) {
                    this.isDragging = false;
                    const files = event.dataTransfer.files;
                    if (files.length > 0) {
                        this.$refs.input.files = files;
                        this.processFile(files[0]);
                    }
                },

                processFile(file) {
                    if (file) {
                        if(file.type !== 'video/mp4') {
                            alert('Por favor, sube solo archivos MP4.');
                            this.clearPreview();
                            return;
                        }
                        this.fileName = file.name;
                        this.previewUrl = URL.createObjectURL(file);
                    }
                },

                clearPreview() {
                    this.previewUrl = null;
                    this.fileName = null;
                    this.$refs.input.value = ''; 
                }
            }
        }
    </script>
</x-app-layout>