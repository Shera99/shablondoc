<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Observers\OrderObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([OrderObserver::class])]
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
        'mynumer',
        'delivery_date',
        'print_date',
        'comment',
        'status',
    ];

    protected $casts = [
        'status' => OrderStatus::class . ':string',
        'document_file' => 'array',
    ];

//    protected static function boot()
//    {
//        parent::boot();
//
//        static::updated(function ($order) {
//            if ($order->isDirty('status') && $order->status === 'completed') {
//                NewOrder::dispatch($order->id);
//                event(new NewOrder($order->id));
//                broadcast(new NewOrder($order->toArray()))->toOthers();
//            }
//        });
//    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
//    protected function casts(): array
//    {
//        return [
//            'status' => OrderStatus::class . ':string',
//            'document_file' => 'array',
//        ];
//    }

    /**
     * Accessor to automatically decode the document_file attribute.
     *
     * @return array
     */
    public function getDocumentFileAttribute($value)
    {
        return json_decode($value, true);
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
