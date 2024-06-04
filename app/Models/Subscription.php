<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Subscription extends Model
{
    use HasRoles, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name_ru',
        'name_en',
        'description_ru',
        'description_en',
        'price_id',
        'day_count',
        'count_translation',
        'is_active'
    ];

    public function price(): BelongsTo
    {
        return $this->belongsTo(Price::class);
    }

    // Accessor for the price attribute
    public function getPriceAttribute()
    {
        return $this->price()->first()->price ?? 0;
    }

    // Mutator for the price attribute
    public function setPriceAttribute($value)
    {
        $price = $this->price()->first();
        if ($price) {
            $price->price = $value;
        }
    }
}
