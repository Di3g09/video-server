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
}
