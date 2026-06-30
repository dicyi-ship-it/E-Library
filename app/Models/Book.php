<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'isbn',
        'ddc',
        'call_number',
        'author',
        'author_id',
        'publisher',
        'publisher_id',
        'publication_year',
        'edition',
        'language',
        'category',
        'rack',
        'location',
        'description',
        'cover_path',
        'stock_total',
        'stock_available',
        'status',
    ];

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function authorRecord()
    {
        return $this->belongsTo(Author::class, 'author_id');
    }

    public function publisherRecord()
    {
        return $this->belongsTo(Publisher::class, 'publisher_id');
    }
}
