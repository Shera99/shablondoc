<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'code',
        'convert',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'name' => 'string',
            'code' => 'string',
            'convert' => 'decimal:2',
            'status' => 'boolean',
        ];
    }
}
