<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaskImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'image_path'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

}
