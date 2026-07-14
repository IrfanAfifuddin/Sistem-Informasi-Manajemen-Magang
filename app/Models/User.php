<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The booted method of the model.
     */
    protected static function booted()
    {
        static::deleting(function ($user) {
            if ($user->id == 1) {
                abort(403, 'Akses Ditolak: Akun Master Admin tidak dapat dihapus dari sistem.');
            }
        });

        static::updating(function ($user) {
            if ($user->id == 1 && auth()->check() && auth()->id() !== 1) {
                abort(403, 'Akses Ditolak: Hanya Master Admin itu sendiri yang boleh mengubah profilnya.');
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'role',
        'is_first_login',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_first_login' => 'boolean',
        ];
    }

    /**
     * Get the profile associated with the user (for Interns).
     */
    public function internProfile()
    {
        return $this->hasOne(InternProfile::class, 'user_id');
    }

    /**
     * Get the profiles of interns mapped to this user (for Mentors).
     */
    public function internProfilesForMentor()
    {
        return $this->hasMany(InternProfile::class, 'mentor_id');
    }

    /**
     * Get the tasks assigned to the user (for Interns).
     */
    public function tasks()
    {
        return $this->hasMany(Task::class, 'intern_id');
    }

    /**
     * Get the tasks created by the user (for Mentors).
     */
    public function createdTasks()
    {
        return $this->hasMany(Task::class, 'mentor_id');
    }

    /**
     * Get the submissions made by the user (for Interns).
     */
    public function submissions()
    {
        return $this->hasMany(Submission::class, 'intern_id');
    }
}
