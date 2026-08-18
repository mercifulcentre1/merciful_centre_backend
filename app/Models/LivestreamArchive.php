<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LivestreamArchive extends Model
{
    protected $fillable = ['title', 'platform', 'video_id', 'thumbnail_url', 'stream_date'];
    protected function casts(): array
    {
        return [
            'stream_date' => 'datetime',
        ];
    }
}
