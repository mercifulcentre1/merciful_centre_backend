<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('livestream_archives', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('platform', ['youtube', 'facebook'])->default('youtube');
            $table->string('video_id');
            $table->string('thumbnail_url')->nullable();
            $table->dateTime('stream_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('livestream_archives');
    }
};