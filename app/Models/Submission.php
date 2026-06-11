<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'intern_id',
        'file_path',
        'link_url',
        'score',
        'feedback',
        'status',
    ];

    /**
     * Get the task associated with the submission.
     */
    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    /**
     * Get the intern who made this submission.
     */
    public function intern()
    {
        return $this->belongsTo(User::class, 'intern_id');
    }
}
