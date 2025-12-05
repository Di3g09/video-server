<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('playlist_id')
                ->constrained('playlists')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            
            $table->string('days_of_week', 50);

            $table->time('start_time'); 
            $table->time('end_time');   

            $table->date('start_date')->nullable(); 
            $table->date('end_date')->nullable();   

            $table->boolean('enabled')->default(true);

            $table->timestamps();

            $table->index('enabled');
            $table->index('start_time');
            $table->index('end_time');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
