<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'playlist_id',
        'days_of_week',
        'start_time',
        'end_time',
        'start_date',
        'end_date',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'start_time' => 'datetime:H:i:s',
        'end_time' => 'datetime:H:i:s',
    ];

    public function playlist()
    {
        return $this->belongsTo(Playlist::class);
    }

    // Helper opcional: días como arreglo
    public function getDaysArrayAttribute()
    {
        return array_filter(array_map('trim', explode(',', $this->days_of_week)));
    }
}
