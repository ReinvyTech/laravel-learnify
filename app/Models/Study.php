<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Study extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function lessons()
    {
        return $this->belongsTo(Lesson::class, 'lesson_id', 'id');
    }
}
