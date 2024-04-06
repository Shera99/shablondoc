<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TranslationDirection extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_language_id',
        'target_language_id'
    ];

    public function sourceLanguage(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Language::class, 'source_language_id', 'id');
    }

    public function targetLanguage(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Language::class, 'target_language_id', 'id');
    }
}
