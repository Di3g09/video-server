<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlaylistItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'playlist_id',
        'media_item_id',
        'position',
    ];

    public function playlist()
    {
        return $this->belongsTo(Playlist::class);
    }

    public function mediaItem()
    {
        return $this->belongsTo(MediaItem::class);
    }
}
