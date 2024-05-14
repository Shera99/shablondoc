<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'template_id',
        'template_data_id',
        'company_address_id',
        'document_name',
        'document_file',
        'email',
        'phone_number',
        'delivery_date',
        'delivery_time',
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
}
