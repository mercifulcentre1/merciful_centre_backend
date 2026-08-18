<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['title', 'event_date', 'location', 'description', 'image_url'];
    protected function casts(): array
    {
        return [
            'event_date' => 'datetime',
        ];
    }
}
