<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlaylistStoreRequest;
use App\Http\Requests\PlaylistUpdateRequest;
use App\Models\Playlist;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\MediaItem;
use App\Models\PlaylistItem;
use Illuminate\Http\Request;

use App\Services\PlaylistSyncService;


class PlaylistController extends Controller
{
    protected PlaylistSyncService $syncService;

    public function __construct(PlaylistSyncService $syncService)
    {
        $this->syncService = $syncService;
        //$this->middleware('auth');
    }

    // Listado de playlists
    public function index()
    {
        $playlists = Playlist::orderByDesc('is_default')
            ->orderBy('name')
            ->paginate(12);

        return view('playlists.index', compact('playlists'));
    }

    // Formulario de creación
    public function create()
    {
        return view('playlists.create');
    }

    // Guardar nueva playlist
    public function store(PlaylistStoreRequest $request)
    {
        $data = $request->validated();

        $isDefault = $request->boolean('is_default');
        $active    = $request->boolean('active');

        if ($isDefault && ! $active) {
            return back()
                ->withErrors([
                    'is_default' => 'Una playlist predeterminada debe estar activa. ' .
                        'Marca la casilla "Activa" antes de establecerla como default.',
                ])
                ->withInput();
        }

        // Aquí capturamos directamente la playlist retornada por la transacción
        $playlist = DB::transaction(function () use ($data, $isDefault, $active) {

            // Generar slug único
            $slug = $this->generateUniqueSlug($data['name']);

            // Crear playlist base
            $playlist = Playlist::create([
                'name'        => $data['name'],
                'slug'        => $slug,
                'description' => $data['description'] ?? null,
                'active'      => $active,
                'is_default'  => false, // se ajusta abajo si aplica
            ]);

            if ($isDefault) {
                // Dejar solo esta como default
                Playlist::where('id', '!=', $playlist->id)->update(['is_default' => false]);

                $playlist->is_default = true;
                $playlist->save();
            }

            // devolvemos la instancia creada/modificada
            return $playlist;
        });

        // ---- SINCRONIZACIÓN CON LA CARPETA DE TRANSMISIÓN ----
        try {
            if ($playlist->is_default && $playlist->active) {
                $copiados = $this->syncService->syncDefault();

                return redirect()
                    ->route('playlists.index')
                    ->with('success', "Playlist creada y sincronizada ({$copiados} videos copiados al canal).");
            } else {
                // Si no hay ninguna default activa ahora, limpiamos la carpeta
                if (!Playlist::where('is_default', true)->where('active', true)->exists()) {
                    $this->syncService->syncDefault(); // limpia carpeta
                }

                return redirect()
                    ->route('playlists.index')
                    ->with('success', 'Playlist creada correctamente.');
            }
        } catch (\Throwable $e) {
            return redirect()
                ->route('playlists.index')
                ->with('error', 'Playlist creada, pero hubo un problema al sincronizar: ' . $e->getMessage());
        }
    }

    // Formulario de edición
    public function edit(Playlist $playlist)
    {
        return view('playlists.edit', compact('playlist'));
    }

    // Actualizar playlist
    public function update(PlaylistUpdateRequest $request, Playlist $playlist)
    {
        $data = $request->validated();

        $isDefault = $request->boolean('is_default');
        $active    = $request->boolean('active');

        /**
         * 1) Regla: no se puede dejar el sistema sin playlist default activa
         * Si ESTA playlist es actualmente la default activa, entonces
         * NO permitimos:
         *  - quitarle el is_default, ni
         *  - desactivarla (active = false)
         * El cambio de default debe hacerse desde otra playlist.
         */


        // Regla: no se puede marcar como default si NO está activa
        if ($isDefault && ! $active) {
            return back()
                ->withErrors([
                    'is_default' => 'Una playlist predeterminada debe estar activa. ' .
                        'Marca la casilla "Activa" antes de establecerla como default.',
                ])
                ->withInput();
        }

        if ($playlist->is_default && $playlist->active) {
            if (! $isDefault || ! $active) {
                return back()
                    ->withErrors([
                        'is_default' => 'No se puede dejar el sistema sin una playlist predeterminada. ' .
                            'Primero establece otra playlist como default y luego modifica esta.',
                    ])
                    ->withInput();
            }
        }

        /**
         * 2) Si quieren marcarla como default (is_default = true),
         *    validar que tenga al menos UN video activo.
         */
        if ($isDefault) {
            $hasActiveItems = $playlist->hasActiveItems();

            if (! $hasActiveItems) {
                return back()
                    ->withErrors([
                        'is_default' => 'Para marcar esta playlist como predeterminada, ' .
                            'debe contener al menos un video activo.',
                    ])
                    ->withInput();
            }
        }

        // 3) Guardamos el estado anterior para saber si hay que resincronizar
        $beforeDefault = $playlist->is_default;
        $beforeActive  = $playlist->active;

        // 4) Transacción para actualizar playlist
        DB::transaction(function () use ($playlist, $data, $isDefault, $active) {

            // Regenerar slug solo si cambió el nombre
            $slug = $playlist->slug;
            if ($playlist->name !== $data['name']) {
                $slug = $this->generateUniqueSlug($data['name'], $playlist->id);
            }

            // Actualizar campos base
            $playlist->update([
                'name'        => $data['name'],
                'slug'        => $slug,
                'description' => $data['description'] ?? null,
                'active'      => $active,
            ]);

            // Manejo de default único
            if ($isDefault) {
                Playlist::where('id', '!=', $playlist->id)
                    ->update(['is_default' => false]);

                $playlist->is_default = true;
                $playlist->save();
            } else {
                // Si desmarcan la casilla y NO es la default única (caso anterior ya bloqueó),
                // esta playlist deja de ser default
                $playlist->is_default = false;
                $playlist->save();
            }
        });

        // 5) Ver si cambió algo relevante para la transmisión
        $defaultChanged = $beforeDefault !== $playlist->is_default;
        $activeChanged  = $beforeActive  !== $playlist->active;

        try {
            if ($defaultChanged || $activeChanged) {
                $copiados = $this->syncService->syncDefault();

                if ($copiados > 0) {
                    return redirect()
                        ->route('playlists.index')
                        ->with('success', "Cambios guardados.");
                }

                return redirect()
                    ->route('playlists.index')
                    ->with('success', 'Cambios guardados. No hay playlist default activa o no tiene videos activos; transmisión vacía.');
            }

            // Si no cambió default ni active, no es necesario resincronizar
            return redirect()
                ->route('playlists.index')
                ->with('success', 'Playlist actualizada correctamente.');
        } catch (\Throwable $e) {
            return redirect()
                ->route('playlists.index')
                ->with('error', 'Playlist actualizada, pero hubo un problema al sincronizar: ' . $e->getMessage());
        }
    }
    // Eliminar playlist
    public function destroy(Playlist $playlist)
    {
        $playlist->delete();

        return redirect()
            ->route('playlists.index')
            ->with('success', 'Playlist eliminada correctamente.');
    }

    // --- Helpers privados ---

    /**
     * Genera un slug único a partir del nombre.
     */
    protected function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug     = $baseSlug;
        $counter  = 2;

        while (
            Playlist::where('slug', $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    public function editItems(Playlist $playlist)
    {
        // 1) Items que YA están en la playlist, en el orden de 'position'
        $playlistItems = $playlist->items()
            ->with('mediaItem')
            ->orderBy('position')
            ->get();

        $inPlaylistIds = $playlistItems->pluck('media_item_id')->all();

        // 2) Videos que NO están en la playlist, ordenados por título
        $otherMediaItems = MediaItem::whereNotIn('id', $inPlaylistIds)
            ->orderBy('title')
            ->get();

        // 3) Armamos la colección final:
        //    primero los de la playlist (en su orden),
        //    luego el resto.
        $mediaItems = $playlistItems
            ->map(function ($playlistItem) {
                return $playlistItem->mediaItem;
            })
            ->filter() // por si acaso
            ->values()
            ->merge($otherMediaItems);

        // 4) Mapa: media_item_id => position actual en esta playlist
        $positions = $playlist->items()
            ->pluck('position', 'media_item_id')
            ->toArray();

        return view('playlists.items', compact('playlist', 'mediaItems', 'positions'));
    }



    public function updateItems(Request $request, Playlist $playlist)
    {
        // 1) Validamos estructura básica de lo que viene del formulario
        $request->validate([
            'items' => ['nullable', 'array'],
            'items.*.include'  => ['nullable', 'boolean'],
            'items.*.position' => ['nullable', 'integer', 'min:1', 'max:10000'],
        ]);

        $itemsInput = $request->input('items', []);

        // 2) Filtramos solo los que están marcados como "incluir"
        $selected = collect($itemsInput)
            ->filter(function ($row) {
                return isset($row['include']) && (int)$row['include'] === 1;
            })
            ->map(function ($row, $mediaItemId) {
                return [
                    'media_item_id' => (int) $mediaItemId,
                    'position'      => isset($row['position']) ? (int) $row['position'] : null,
                ];
            });

        // 3) Filtrar solo media_items que sigan activos
        $ids = $selected->pluck('media_item_id')->all();

        if (!empty($ids)) {
            $activeIds = MediaItem::whereIn('id', $ids)
                ->where('active', true)
                ->pluck('id')
                ->all();

            $selected = $selected->filter(function ($row) use ($activeIds) {
                return in_array($row['media_item_id'], $activeIds);
            });
        }

        /**
         * 4) Regla importante:
         *    Si la playlist es DEFAULT + ACTIVA,
         *    NO permitimos dejarla sin ningún video seleccionado.
         */
        if ($playlist->is_default && $playlist->active && $selected->isEmpty()) {
            return back()
                ->withErrors([
                    'items' => 'La playlist predeterminada debe tener al menos un video activo. ' .
                        'No puedes dejarla sin contenido.',
                ])
                ->withInput();
        }

        // 5) Guardamos cambios en los items dentro de una transacción
        DB::transaction(function () use ($playlist, $selected) {
            // Eliminamos todo el contenido actual
            $playlist->items()->delete();

            if ($selected->isEmpty()) {
                // Si no es default+activa, se permite dejarla vacía
                return;
            }

            // Normalizamos: ordenamos por position (nulos al final) y reasignamos 1..N
            $ordered = $selected->sortBy(function ($row) {
                return $row['position'] ?? PHP_INT_MAX;
            });

            $position    = 1;
            $dataToInsert = [];

            foreach ($ordered as $row) {
                $dataToInsert[] = [
                    'playlist_id'   => $playlist->id,
                    'media_item_id' => $row['media_item_id'],
                    'position'      => $position,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];
                $position++;
            }

            PlaylistItem::insert($dataToInsert);
        });

        /**
         * 6) Sincronizar carpeta de transmisión SI esta playlist
         *    es la default + activa (es decir, la que manda al canal).
         */
        if ($playlist->is_default && $playlist->active) {
            try {
                $copiados = $this->syncService->syncDefault();

                return redirect()
                    ->route('playlists.items.edit', $playlist)
                    ->with('success', "Contenido de la playlist actualizado y sincronizado ({$copiados} videos).");
            } catch (\Throwable $e) {
                return redirect()
                    ->route('playlists.items.edit', $playlist)
                    ->with('error', 'Contenido actualizado, pero hubo un problema al sincronizar: ' . $e->getMessage());
            }
        }

        // Si no es default, solo avisamos que se guardó el contenido
        return redirect()
            ->route('playlists.items.edit', $playlist)
            ->with('success', 'Contenido de la playlist actualizado correctamente.');
    }
}
