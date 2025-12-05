<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_items', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150);
            $table->string('filename', 255);
            $table->string('storage_path', 255)->nullable();
            $table->integer('duration_seconds')->nullable(); 
            $table->decimal('size_mb', 8, 2)->nullable();     
            $table->boolean('active')->default(true);
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_items');
    }
};
