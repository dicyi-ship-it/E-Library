<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'guest_name',
        'identity_number',
        'visitor_type',
        'attendance_source',
        'purpose',
        'check_in_at',
        'check_out_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'check_in_at' => 'datetime',
            'check_out_at' => 'datetime',
        ];
    }

    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }
}
