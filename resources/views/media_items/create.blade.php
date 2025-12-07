<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                Nuevo video
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
                
                {{-- Encabezado visual de la tarjeta (opcional para dar color) --}}
                <div class="bg-indigo-600 h-2 w-full"></div>

                <div class="p-6 sm:p-10">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Información del contenido</h3>

                    {{-- x-data para manejar el envío con progreso --}}
                    <form
                        action="{{ route('media-items.store') }}"
                        method="POST"
                        enctype="multipart/form-data"
                        class="space-y-8"
                        x-data="videoUploadForm()"
                        @submit.prevent="submitForm($event)"
                    >
                        @csrf

                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            
                            {{-- Título --}}
                            <div class="sm:col-span-4">
                                <label for="title" class="block text-sm font-medium leading-6 text-gray-900">Título del video</label>
                                <div class="mt-2">
                                    <input type="text" name="title" id="title" value="{{ old('title') }}" 
                                        class="block w-full rounded-md border-0 py-2.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition duration-150"
                                        placeholder="Ej. Información InovaTEC"
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
                                        <input id="active" name="active" type="checkbox" value="1" {{ old('active', true) ? 'checked' : '' }}
                                            class="h-5 w-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 cursor-pointer">
                                    </div>
                                    <div class="text-sm leading-6">
                                        <label for="active" class="font-medium text-gray-900 cursor-pointer">Activo</label>
                                    </div>
                                </div>
                            </div>

                            {{-- Componente de Subida de Video con Drag & Drop real (Alpine.js) --}}
                            <div class="sm:col-span-6" x-data="videoUploader()">
                                <label class="block text-sm font-medium leading-6 text-gray-900">Archivo de video</label>
                                
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
                                                <span>Sube un archivo</span>
                                                <input id="file-upload" name="file" type="file" class="sr-only" accept="video/mp4" @change="updatePreview" x-ref="input">
                                            </label>
                                            <p class="pl-1">o arrastra y suelta</p>
                                        </div>
                                        <p class="text-xs leading-5 text-gray-600">MP4 hasta 5GB</p>
                                    </div>

                                    {{-- Vista Previa del Video --}}
                                    <div x-show="previewUrl" class="relative w-full max-w-md mx-auto" style="display: none;">
                                        <video class="w-full rounded-lg shadow-lg border border-gray-200" controls x-bind:src="previewUrl">
                                            Tu navegador no soporta videos HTML5.
                                        </video>
                                        
                                        {{-- Nombre del archivo --}}
                                        <p class="mt-2 text-center text-sm text-gray-500" x-text="fileName"></p>

                                        {{-- Botón Eliminar --}}
                                        <button type="button" @click="clearPreview" class="absolute -top-3 -right-3 bg-red-100 text-red-600 p-1.5 rounded-full hover:bg-red-200 shadow-sm transition-colors" title="Quitar video">
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
                                    placeholder="Descripción corta o notas internas...">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Botones de Acción --}}
                        <div class="flex items-center justify-end gap-x-4 pt-4 border-t border-gray-100">
                            <a href="{{ route('media-items.index') }}"
                               class="text-sm font-semibold leading-6 text-gray-900 hover:text-gray-700"
                               x-bind:class="{ 'pointer-events-none opacity-40': isUploading }">
                                Cancelar
                            </a>
                            <button type="submit"
                                    class="rounded-md bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all duration-200 flex items-center gap-2"
                                    x-bind:disabled="isUploading">
                                <span x-show="!isUploading">Guardar Video</span>
                                <span x-show="isUploading" class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                         viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                              d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                    </svg>
                                    Subiendo...
                                </span>
                            </button>
                        </div>

                        {{-- Barra de progreso --}}
                        <div class="mt-4" x-show="isUploading">
                            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                <div class="h-2 bg-indigo-600 rounded-full transition-all duration-100"
                                     :style="`width: ${uploadProgress}%;`"></div>
                            </div>
                            <p class="mt-2 text-xs text-gray-500" x-text="uploadLabel"></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Script de Alpine.js para la lógica del preview y Drag&Drop --}}
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
                        // Asignamos los archivos soltados al input real para que el form los envíe
                        this.$refs.input.files = files;
                        this.processFile(files[0]);
                    }
                },

                processFile(file) {
                    if (file) {
                        // Validación básica de tipo (opcional, pero recomendada para UX inmediata)
                        if (file.type !== 'video/mp4') {
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
                    this.$refs.input.value = ''; // Resetea el input file real
                }
            }
        }

        // Componente Alpine para manejar el envío con progreso
        function videoUploadForm() {
            return {
                isUploading: false,
                uploadProgress: 0,
                uploadLabel: 'Preparando archivo...',

                submitForm(event) {
                    const form   = event.target;
                    const url    = form.action;
                    const method = form.method || 'POST';

                    const formData = new FormData(form);

                    this.isUploading    = true;
                    this.uploadProgress = 0;
                    this.uploadLabel    = '0%';

                    const xhr = new XMLHttpRequest();
                    xhr.open(method.toUpperCase(), url, true);

                    // Laravel suele usar esto para detectar peticiones AJAX
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                    // Progreso de subida
                    xhr.upload.addEventListener('progress', (e) => {
                        if (e.lengthComputable) {
                            const percent = Math.round((e.loaded / e.total) * 100);
                            this.uploadProgress = percent;
                            this.uploadLabel    = `Subiendo video... ${percent}%`;
                        } else {
                            this.uploadLabel = 'Subiendo video...';
                        }
                    });

                    // Al completar la petición, reemplazamos el documento
                    xhr.addEventListener('load', () => {
                        document.open();
                        document.write(xhr.responseText);
                        document.close();
                    });

                    xhr.addEventListener('error', () => {
                        this.isUploading = false;
                        this.uploadLabel = 'Error de red al subir el video.';

                        if (window.Swal) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Ocurrió un problema de conexión al subir el video.',
                            });
                        }
                    });

                    xhr.send(formData);
                }
            }
        }
    </script>
</x-app-layout>
