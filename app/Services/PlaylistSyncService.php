<?php

namespace App\Services;

use App\Models\Playlist;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class PlaylistSyncService
{

    protected string $libraryPath;
    protected string $transmissionPath;

    public function __construct()
    {
        $this->libraryPath = rtrim(config('video.library_path'), DIRECTORY_SEPARATOR);
        $this->transmissionPath = rtrim(config('video.transmission_path'), DIRECTORY_SEPARATOR);
    }
    /**
     * Borra todos los .mp4 en la carpeta de transmisión.
     */
    public function clearTransmission(): void
    {
        $transmissionPath = config('video.transmission_path');

        if (! is_dir($this->transmissionPath)) {
            throw new \RuntimeException('La ruta de transmisión no existe: ' . $this->transmissionPath);
        }


        if (!is_writable($transmissionPath)) {
            throw new RuntimeException("La ruta de transmisión no es escribible: {$transmissionPath}");
        }

        foreach (glob(rtrim($transmissionPath, '/') . '/*.mp4') as $file) {
            @unlink($file);
        }
    }

    /**
     * Sincroniza la playlist default activa (si existe) hacia la carpeta de transmisión.
     *
     * - Si hay default activa: limpia carpeta y copia sus videos activos en orden.
     * - Si NO hay default activa: solo limpia carpeta (se queda vacío).
     *
     * Devuelve el número de archivos copiados.
     */
    public function syncDefault(): int
    {
        $playlist = Playlist::where('is_default', true)
            ->where('active', true)
            ->first();

        // Siempre limpiamos la carpeta primero
        $this->clearTransmission();

        if (!$playlist) {
            Log::info('PlaylistSync: no hay playlist default activa; transmisión vacía');
            return 0;
        }

        return $this->syncToTransmission($playlist);
    }

    /**
     * Copia los videos activos de una playlist específica a la carpeta de transmisión.
     * NO limpia la carpeta (eso lo hace syncDefault o quien la llame).
     */
    public function syncToTransmission(Playlist $playlist): int
    {
        $transmissionPath = config('video.transmission_path');
        $disk = Storage::disk('video_library');

        $items = $playlist->items()
            ->with(['mediaItem' => function ($q) {
                $q->where('active', true);
            }])
            ->orderBy('position')
            ->get();

        $copiados = 0;
        $position = 1;

        foreach ($items as $item) {
            $media = $item->mediaItem;

            if (!$media) {
                continue;
            }

            // asumimos que el campo en media_items es "filename"
            $relativePath = $media->filename;

            if (!$disk->exists($relativePath)) {
                Log::warning("PlaylistSync: archivo no encontrado en biblioteca", [
                    'media_item_id' => $media->id,
                    'path' => $relativePath,
                ]);
                continue;
            }

            $sourcePath = $disk->path($relativePath);

            $prefijo = str_pad($position, 3, '0', STR_PAD_LEFT) . '_';
            $destName = $prefijo . basename($sourcePath);
            $destPath = rtrim($transmissionPath, '/') . '/' . $destName;

            if (!@copy($sourcePath, $destPath)) {
                Log::error("PlaylistSync: error al copiar archivo", [
                    'source' => $sourcePath,
                    'dest' => $destPath,
                ]);
                throw new RuntimeException("No se pudo copiar el archivo {$sourcePath} a {$destPath}");
            }

            $position++;
            $copiados++;
        }

        Log::info("PlaylistSync: sincronización completada", [
            'playlist_id' => $playlist->id,
            'copiados' => $copiados,
        ]);

        return $copiados;
    }
}
