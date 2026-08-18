<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LivestreamSetting extends Model
{
    protected $fillable = ['platform', 'channel_url', 'stream_title', 'stream_description', 'is_live', 'next_service_date', 'next_service_title', 'next_service_description'];
    protected function casts(): array
    {
        return [
            'is_live' => 'boolean',
            'next_service_date' => 'datetime',
        ];
    }
}
