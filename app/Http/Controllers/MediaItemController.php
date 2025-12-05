<?php

namespace App\Http\Controllers;

use App\Http\Requests\MediaItemStoreRequest;
use App\Http\Requests\MediaItemUpdateRequest;
use App\Models\MediaItem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaItemController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index()
    {
        $mediaItems = MediaItem::orderByDesc('created_at')->paginate(12);

        return view('media_items.index', compact('mediaItems'));
    }

    public function create()
    {
        return view('media_items.create');
    }

    public function store(MediaItemStoreRequest $request)
    {
        $file = $request->file('file');

        $extension = $file->getClientOriginalExtension() ?: 'mp4';
        $baseName  = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $filename  = now()->format('YmdHis') . '_' . $baseName . '.' . $extension;

        // Guardar en disk video_library
        $file->storeAs('', $filename, 'video_library');

        $bytes  = $file->getSize();
        $sizeMb = $bytes ? round($bytes / (1024 * 1024), 2) : null;

        // Ruta absoluta en el disco video_library
        $absolutePath = Storage::disk('video_library')->path($filename);
        $duration     = $this->getVideoDurationSeconds($absolutePath);

        $mediaItem = MediaItem::create([
            'title'           => $request->input('title'),
            'filename'        => $filename,
            'storage_path'    => $absolutePath,
            'duration_seconds' => $duration,
            'size_mb'         => $sizeMb,
            'active'          => $request->boolean('active'),
            'notes'           => $request->input('notes'),
        ]);

        return redirect()
            ->route('media-items.index')
            ->with('success', 'Video creado correctamente.');
    }

    public function edit(MediaItem $mediaItem)
    {
        return view('media_items.edit', compact('mediaItem'));
    }

    public function update(MediaItemUpdateRequest $request, MediaItem $mediaItem)
    {
        $data = [
            'title'  => $request->input('title'),
            'notes'  => $request->input('notes'),
            'active' => $request->boolean('active'),
        ];

        if ($request->hasFile('file')) {
            $file = $request->file('file');

            // Borrar archivo anterior si existe
            if ($mediaItem->filename && Storage::disk('video_library')->exists($mediaItem->filename)) {
                Storage::disk('video_library')->delete($mediaItem->filename);
            }

            $extension = $file->getClientOriginalExtension() ?: 'mp4';
            $baseName  = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
            $filename  = now()->format('YmdHis') . '_' . $baseName . '.' . $extension;

            $file->storeAs('', $filename, 'video_library');

            $bytes  = $file->getSize();
            $sizeMb = $bytes ? round($bytes / (1024 * 1024), 2) : null;

            $absolutePath = Storage::disk('video_library')->path($filename);
            $duration     = $this->getVideoDurationSeconds($absolutePath);

            $data['filename']         = $filename;
            $data['storage_path']     = $absolutePath;
            $data['size_mb']          = $sizeMb;
            $data['duration_seconds'] = $duration;
        }

        $mediaItem->update($data);

        return redirect()
            ->route('media-items.index')
            ->with('success', 'Video actualizado correctamente.');
    }

    public function destroy(MediaItem $mediaItem)
    {
        // Dejamos que la BD (FK) impida borrar si se usa en playlist
        if ($mediaItem->filename && Storage::disk('video_library')->exists($mediaItem->filename)) {
            Storage::disk('video_library')->delete($mediaItem->filename);
        }

        $mediaItem->delete();

        return redirect()
            ->route('media-items.index')
            ->with('success', 'Video eliminado correctamente.');
    }

    protected function getVideoDurationSeconds(string $absolutePath): ?int
    {
        // Asumimos que ffprobe está en el PATH del sistema
        $cmd = 'ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 ' . escapeshellarg($absolutePath);

        $output = shell_exec($cmd);

        if ($output === null) {
            return null;
        }

        $seconds = (int) round((float) $output);

        return $seconds > 0 ? $seconds : null;
    }

    public function preview(MediaItem $mediaItem)
    {
        $disk = Storage::disk('video_library');

        if (! $disk->exists($mediaItem->filename)) {
            abort(404);
        }

        $path = $disk->path($mediaItem->filename);

        // Para administración es suficiente response()->file
        return response()->file($path, [
            'Content-Type' => 'video/mp4',
            'Accept-Ranges' => 'bytes',
        ]);
    }
}
