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
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'member_id',
        'identity_number',
        'phone',
        'faculty',
        'department',
        'study_program',
        'level',
        'status',
        'registered_at',
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
            'registered_at' => 'date',
        ];
    }

    public function loans()
    {
        return $this->hasMany(Loan::class, 'member_id');
    }

    public function visits()
    {
        return $this->hasMany(Visit::class, 'member_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
