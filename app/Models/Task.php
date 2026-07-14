<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'mentor_id',
        'intern_id',
        'title',
        'description',
        'attachment_path',
        'due_date',
        'status',
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    /**
     * Get the mentor who created the task.
     */
    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    /**
     * Get the intern assigned to this task.
     */
    public function intern()
    {
        return $this->belongsTo(User::class, 'intern_id');
    }

    /**
     * Get all submissions for this task.
     */
    public function submissions()
    {
        return $this->hasMany(Submission::class, 'task_id');
    }

    /**
     * Get the latest submission for this task.
     */
    public function latestSubmission()
    {
        return $this->hasOne(Submission::class, 'task_id')->latestOfMany();
    }
}
