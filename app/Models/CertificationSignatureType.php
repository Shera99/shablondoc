<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CertificationSignatureType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    /**
     * @return HasMany
     */
    public function certificationSignatures(): HasMany
    {
        return $this->hasMany(CertificationSignature::class);
    }
}
