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


class PlaylistController extends Controller
{
    public function __construct()
    {
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

        DB::transaction(function () use (&$playlist, $data, $isDefault, $active) {
            // Generar slug único
            $slug = $this->generateUniqueSlug($data['name']);

            $playlist = Playlist::create([
                'name'        => $data['name'],
                'slug'        => $slug,
                'description' => $data['description'] ?? null,
                'active'      => $active,
                'is_default'  => false, // ajustamos abajo si aplica
            ]);

            if ($isDefault) {
                // Dejar solo esta como default
                Playlist::where('id', '!=', $playlist->id)->update(['is_default' => false]);
                $playlist->is_default = true;
                $playlist->save();
            }
        });

        return redirect()
            ->route('playlists.index')
            ->with('success', 'Playlist creada correctamente.');
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

        DB::transaction(function () use (&$playlist, $data, $isDefault, $active) {
            // Regenerar slug solo si cambió el nombre
            $slug = $playlist->slug;
            if ($playlist->name !== $data['name']) {
                $slug = $this->generateUniqueSlug($data['name'], $playlist->id);
            }

            $playlist->update([
                'name'        => $data['name'],
                'slug'        => $slug,
                'description' => $data['description'] ?? null,
                'active'      => $active,
            ]);

            // Manejo de default único
            if ($isDefault) {
                Playlist::where('id', '!=', $playlist->id)->update(['is_default' => false]);
                $playlist->is_default = true;
                $playlist->save();
            } else {
                // Si desmarcan la casilla, esta playlist deja de ser default
                $playlist->is_default = false;
                $playlist->save();
            }
        });

        return redirect()
            ->route('playlists.index')
            ->with('success', 'Playlist actualizada correctamente.');
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
        // Traemos todos los videos disponibles
        $mediaItems = MediaItem::orderBy('title')->get();

        // Mapa: media_item_id => position actual en esta playlist
        $positions = $playlist->items()
            ->pluck('position', 'media_item_id')
            ->toArray();

        return view('playlists.items', compact('playlist', 'mediaItems', 'positions'));
    }


    public function updateItems(Request $request, Playlist $playlist)
    {
        // Validamos estructura básica
        $request->validate([
            'items' => ['nullable', 'array'],
            'items.*.include'  => ['nullable', 'boolean'],
            'items.*.position' => ['nullable', 'integer', 'min:1', 'max:10000'],
        ]);

        $itemsInput = $request->input('items', []);

        // Filtramos solo los que están marcados como "incluir"
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

        // Filtrar solo media_items que sigan activos
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


        // Si no se seleccionó ningún video, simplemente limpiamos la playlist
        DB::transaction(function () use ($playlist, $selected) {
            // Eliminamos todo el contenido actual
            $playlist->items()->delete();

            if ($selected->isEmpty()) {
                return;
            }

            // Normalizamos: ordenamos por position (nulos al final) y reasignamos 1..N
            $ordered = $selected->sortBy(function ($row) {
                return $row['position'] ?? PHP_INT_MAX;
            });

            $position = 1;
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

        return redirect()
            ->route('playlists.items.edit', $playlist)
            ->with('success', 'Contenido de la playlist actualizado correctamente.');
    }
}
