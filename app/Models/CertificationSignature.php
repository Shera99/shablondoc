<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CertificationSignature extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'country_id',
        'city_id',
        'language_id',
        'certification_signature_type_id',
        'file',
        'user',
        'view',
        'certification_text',
        'is_deleted',
    ];

    public function certificationSignatureType(): BelongsTo
    {
        return $this->belongsTo(CertificationSignatureType::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
