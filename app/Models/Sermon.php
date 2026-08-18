<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sermon extends Model
{
    protected $fillable = ['title', 'preacher', 'date', 'description', 'audio_url', 'thumbnail_url'];
}
