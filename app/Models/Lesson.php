<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function teachers()
    {
        return $this->belongsToMany(User::class, 'lesson_teacher', 'lesson_id', 'teacher_id')
            ->withTimestamps();
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'lesson_student', 'lesson_id', 'student_id')
            ->withTimestamps();
    }

    public function studies()
    {
        return $this->hasMany(Study::class, 'lesson_id', 'id');
    }
}
