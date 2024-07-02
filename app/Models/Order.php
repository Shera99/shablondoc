<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'template_id',
        'template_data_id',
        'company_address_id',
        'country_id',
        'language_id',
        'certification_signature_id',
        'document_name',
        'document_file',
        'email',
        'phone_number',
        'delivery_date',
        'print_date',
        'comment',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class . ':string',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    public function templateData(): BelongsTo
    {
        return $this->belongsTo(TemplateData::class);
    }

    public function companyAddress(): BelongsTo
    {
        return $this->belongsTo(CompanyAddress::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function certificationSignature(): BelongsTo
    {
        return $this->belongsTo(CertificationSignature::class);
    }
}
