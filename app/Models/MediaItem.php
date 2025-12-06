<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Playlist;

class MediaItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'filename',
        'storage_path',
        'duration_seconds',
        'size_mb',
        'active',
        'notes',
    ];

    protected $casts = [
        'active' => 'boolean',
        'duration_seconds' => 'integer',
        'size_mb' => 'float',
    ];

    public function playlistItems()
    {
        return $this->hasMany(PlaylistItem::class);
    }

    public function playlists()
    {
        return $this->belongsToMany(Playlist::class, 'playlist_items')
            ->withPivot('position');
    }

    public function isUsedInActiveDefaultPlaylist(): bool
    {
        return $this->playlists()
            ->where('playlists.is_default', true)
            ->where('playlists.active', true)
            ->exists();
    }
}
