<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    Contenido de la playlist
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Gestiona qué videos se reproducen y en qué orden.
                </p>
            </div>

            <a href="{{ route('playlists.index') }}"
               class="text-sm text-gray-500 hover:text-indigo-600 flex items-center gap-1 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Volver al listado
            </a>
        </div>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Tarjeta con información de la playlist --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8 flex flex-col sm:flex-row gap-6">
                <div class="flex-1">
                    <h3 class="text-xl font-semibold text-gray-900 flex items-center gap-3">
                        {{ $playlist->name }}

                        @if ($playlist->is_default)
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                                ✨ Default
                            </span>
                        @endif

                        @if ($playlist->active)
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 border border-green-200">
                                Activa
                            </span>
                        @else
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                Inactiva
                            </span>
                        @endif
                    </h3>

                    <p class="mt-1 text-xs text-gray-500 font-mono">
                        Slug: {{ $playlist->slug }}
                    </p>

                    @if ($playlist->description)
                        <p class="mt-3 text-sm text-gray-600">
                            {{ $playlist->description }}
                        </p>
                    @else
                        <p class="mt-3 text-sm text-gray-400 italic">
                            Sin descripción.
                        </p>
                    @endif
                </div>

                <div class="flex items-center sm:items-end">
                    <a href="{{ route('playlists.edit', $playlist) }}"
                       class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                            <path
                                d="M5.433 13.917l1.262-3.155A4 4 0 017.58 9.42l6.92-6.918a2.121 2.121 0 013 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 01-.65-.65z" />
                            <path
                                d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H10A.75.75 0 0010 3H4.75A2.75 2.75 0 002 5.75v9.5A2.75 2.75 0 004.75 18h9.5A2.75 2.75 0 0017 15.25V10a.75.75 0 00-1.5 0v5.25c0 .69-.56 1.25-1.25 1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5z" />
                        </svg>
                        Editar playlist
                    </a>
                </div>
            </div>

            {{-- Formulario de contenido --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    Seleccionar videos y definir orden
                </h3>

                @if ($mediaItems->count())
                    <form action="{{ route('playlists.items.update', $playlist) }}"
                          method="POST"
                          class="space-y-4"
                          x-data="playlistItems()"
                          x-ref="form"
                          @submit.prevent="handleSubmit">
                        @csrf
                        @method('PUT')

                        <div class="overflow-x-auto rounded-xl border border-gray-100">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Incluir
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Orden
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Título
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Duración
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Tamaño
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Estado
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100"
                                       x-ref="tbody"
                                       @dragover.prevent
                                       x-init="refreshOrderBadges()">
                                    @foreach ($mediaItems as $item)
                                        @php
                                            $existingPosition = $positions[$item->id] ?? null;

                                            $isVideoActive = $item->active;

                                            $oldInclude = old("items.$item->id.include");

                                            // Solo consideramos incluido si el video está activo
                                            $isIncluded =
                                                $isVideoActive &&
                                                (!is_null($oldInclude)
                                                    ? (bool) $oldInclude
                                                    : array_key_exists($item->id, $positions));

                                            $oldPosition = old("items.$item->id.position", $existingPosition);
                                        @endphp

                                        <tr
                                            class="hover:bg-gray-50 {{ !$isVideoActive ? 'opacity-50 bg-gray-50' : '' }}"
                                            data-id="{{ $item->id }}"
                                            draggable="{{ $isVideoActive ? 'true' : 'false' }}"
                                            @dragstart="onDragStart($event)"
                                            @dragenter.prevent="onDragEnter($event)"
                                            @drop.prevent="onDrop($event)">
                                            {{-- Incluir --}}
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-2">
                                                    {{-- Handle para arrastrar --}}
                                                    @if ($isVideoActive)
                                                        <span class="cursor-move text-gray-400 hover:text-gray-600"
                                                              title="Arrastrar para cambiar el orden">
                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                                 fill="currentColor" class="w-4 h-4">
                                                                <path d="M7 4a1 1 0 100-2 1 1 0 000 2zM7 11a1 1 0 100-2 1 1 0 000 2zM7 18a1 1 0 100-2 1 1 0 000 2zM13 4a1 1 0 100-2 1 1 0 000 2zM13 11a1 1 0 100-2 1 1 0 000 2zM13 18a1 1 0 100-2 1 1 0 000 2z" />
                                                            </svg>
                                                        </span>
                                                    @else
                                                        <span class="w-4"></span>
                                                    @endif

                                                    <label class="inline-flex items-center space-x-2">
                                                        <input type="checkbox"
                                                               name="items[{{ $item->id }}][include]"
                                                               value="1"
                                                               {{ $isIncluded ? 'checked' : '' }}
                                                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                                                               @disabled(!$isVideoActive)>
                                                    </label>
                                                </div>
                                            </td>

                                            {{-- Orden (badge visual + input hidden) --}}
                                            <td class="px-4 py-3">
                                                <div class="flex items-center">
                                                    <span
                                                        class="inline-flex items-center justify-center w-8 h-8 rounded-full text-xs font-semibold border bg-gray-50 text-gray-600"
                                                        data-role="order-label">
                                                        {{ $isIncluded && $oldPosition ? $oldPosition : '-' }}
                                                    </span>
                                                    <input type="hidden"
                                                           name="items[{{ $item->id }}][position]"
                                                           value="{{ $isIncluded ? $oldPosition : '' }}">
                                                </div>
                                            </td>

                                            {{-- Título --}}
                                            <td class="px-4 py-3">
                                                <div class="flex flex-col">
                                                    <span class="text-sm font-medium text-gray-900">
                                                        {{ $item->title }}
                                                    </span>
                                                    <span class="text-xs text-gray-400 truncate">
                                                        {{ $item->filename }}
                                                    </span>

                                                    @unless ($isVideoActive)
                                                        <span class="text-[11px] text-red-500 mt-1">
                                                            Video inactivo: no se reproducirá en el canal.
                                                        </span>
                                                    @endunless
                                                </div>
                                            </td>

                                            {{-- Duración --}}
                                            <td class="px-4 py-3 text-sm text-gray-600">
                                                {{ $item->duration_seconds ? gmdate('i:s', $item->duration_seconds) : 'N/D' }}
                                            </td>

                                            {{-- Tamaño --}}
                                            <td class="px-4 py-3 text-sm text-gray-600">
                                                {{ $item->size_mb ? number_format($item->size_mb, 1) . ' MB' : 'N/D' }}
                                            </td>

                                            {{-- Estado video --}}
                                            <td class="px-4 py-3">
                                                @if ($item->active)
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                                        Activo
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-50 text-gray-600 border border-gray-200">
                                                        Inactivo
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <p class="text-xs text-gray-500">
                            Consejo: marca los videos que quieras incluir y luego arrástralos para cambiar el orden
                            (1, 2, 3…). El orden se aplicará solo a los videos seleccionados.
                        </p>

                        <div class="flex items-center justify-end gap-x-4 pt-4 border-t border-gray-100">
                            <a href="{{ route('playlists.index') }}"
                               class="text-sm font-semibold leading-6 text-gray-900 hover:text-gray-700">
                                Cancelar
                            </a>
                            <button type="submit"
                                    class="rounded-md bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all duration-200">
                                Guardar contenido
                            </button>
                        </div>
                    </form>
                @else
                    <p class="text-sm text-gray-500">
                        No hay videos en la biblioteca todavía. Primero sube contenido en el módulo de videos.
                    </p>
                @endif
            </div>

        </div>
    </div>

    {{-- Alpine.js: lógica de drag & drop y numeración automática --}}
    <script>
        function playlistItems() {
            return {
                draggingRow: null,

                onDragStart(event) {
                    const row = event.currentTarget;
                    if (row.getAttribute('draggable') !== 'true') return;

                    this.draggingRow = row;
                    event.dataTransfer.effectAllowed = 'move';
                    event.dataTransfer.setData('text/plain', row.dataset.id);
                    row.classList.add('ring', 'ring-indigo-200', 'bg-indigo-50/70');
                },

                onDragEnter(event) {
                    const targetRow = event.currentTarget;
                    if (!this.draggingRow || this.draggingRow === targetRow) return;

                    const tbody = this.$refs.tbody;
                    const rows = Array.from(tbody.querySelectorAll('tr'));
                    const draggingIndex = rows.indexOf(this.draggingRow);
                    const targetIndex = rows.indexOf(targetRow);

                    if (draggingIndex < 0 || targetIndex < 0) return;

                    // Insertamos la fila arrastrada antes o después según la posición
                    if (draggingIndex < targetIndex) {
                        tbody.insertBefore(this.draggingRow, targetRow.nextSibling);
                    } else {
                        tbody.insertBefore(this.draggingRow, targetRow);
                    }
                },

                onDrop(event) {
                    if (!this.draggingRow) return;
                    this.draggingRow.classList.remove('ring', 'ring-indigo-200', 'bg-indigo-50/70');
                    this.draggingRow = null;
                    this.refreshOrderBadges();
                },

                refreshOrderBadges() {
                    const tbody = this.$refs.tbody;
                    let pos = 1;

                    tbody.querySelectorAll('tr').forEach(row => {
                        const checkbox = row.querySelector('input[type="checkbox"][name$="[include]"]');
                        const label = row.querySelector('[data-role="order-label"]');
                        const positionInput = row.querySelector('input[name$="[position]"]');

                        if (!label || !positionInput || !checkbox || checkbox.disabled) {
                            if (label) label.textContent = '-';
                            if (positionInput) positionInput.value = '';
                            return;
                        }

                        if (checkbox.checked) {
                            label.textContent = pos;
                            positionInput.value = pos;
                            pos++;
                        } else {
                            label.textContent = '-';
                            positionInput.value = '';
                        }
                    });
                },

                handleSubmit(event) {
                    // Antes de enviar, recalculamos las posiciones según el orden visual
                    this.refreshOrderBadges();
                    event.target.submit();
                }
            }
        }
    </script>
</x-app-layout>
