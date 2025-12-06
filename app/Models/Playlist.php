<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_default',
        'active',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'active' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(PlaylistItem::class)->orderBy('position');
    }

    public function mediaItems()
    {
        return $this->belongsToMany(MediaItem::class, 'playlist_items')
            ->withPivot('position')
            ->orderBy('playlist_items.position');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function scopeDefaultActive($query)
    {
        return $query->where('is_default', true)->where('active', true);
    }
    public function activeItems()
    {
        // items cuyo mediaItem está activo
        return $this->items()
            ->whereHas('mediaItem', function ($q) {
                $q->where('active', true);
            });
    }

    public function hasActiveItems(): bool
    {
        return $this->activeItems()->exists();
    }
}
