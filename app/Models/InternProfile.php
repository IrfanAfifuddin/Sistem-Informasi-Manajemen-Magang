<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'university',
        'major',
        'start_date',
        'end_date',
        'mentor_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the user that owns this profile (the Intern).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the mentor assigned to this profile.
     */
    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }
}
