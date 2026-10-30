<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EcommerceReview extends Model
{
    use HasFactory;

    protected $table = 'ecommerce_reviews';

    protected $fillable = [
        'company_id',
        'product_id',
        'customer_name',
        'customer_email',
        'rating',
        'title',
        'comment',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    /**
     * Produit associé à l'avis.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Filtrer les avis d'une entreprise.
     */
    public function scopeForCompany($query, $companyId)
    {
        if (!$companyId) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('company_id', $companyId);
    }
}
