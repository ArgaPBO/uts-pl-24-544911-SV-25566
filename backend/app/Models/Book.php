<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'publish_year',
    ];

    protected function casts(): array
    {
        return [
            'publish_year' => 'integer',
        ];
    }
}