<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateData extends Model
{
    use HasFactory;

    protected $fillable = [
        'data_json',
    ];

    protected $casts = [
        'data_json' => 'array',
    ];
}
