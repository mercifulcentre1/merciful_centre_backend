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
        Schema::create('livestream_settings', function (Blueprint $table) {
            $table->id();
            $table->enum('platform', ['youtube', 'facebook']);
            $table->string('channel_url');
            $table->string('stream_title')->nullable();
            $table->text('stream_description')->nullable();
            $table->boolean('is_live')->default(false);
            $table->dateTime('next_service_date')->nullable();
            $table->string('next_service_title')->nullable();
            $table->text('next_service_description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('livestream_settings');
    }
};