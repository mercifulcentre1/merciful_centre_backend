<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $table = 'gallery';
    protected $fillable = ['title', 'description', 'image_url', 'category', 'uploaded_by'];

    public function uploader()
    {
        return $this->belongsTo(AdminUser::class, 'uploaded_by');
    }
}
