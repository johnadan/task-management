<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\{User, TaskImage};

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'status',
        'is_published',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(TaskImage::class);
    }
}
