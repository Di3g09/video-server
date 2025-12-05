<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('playlist_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('playlist_id')
                ->constrained('playlists')
                ->onUpdate('cascade')
                ->onDelete('cascade'); 

            $table->foreignId('media_item_id')
                ->constrained('media_items')
                ->onUpdate('cascade')
                ->onDelete('restrict'); 

            $table->unsignedInteger('position'); 

            $table->timestamps();

            $table->unique(['playlist_id', 'media_item_id']);
            $table->index(['playlist_id', 'position']);        
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('playlist_items');
    }
};
