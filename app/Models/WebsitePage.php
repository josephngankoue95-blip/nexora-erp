<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebsitePage extends Model
{
    protected $casts = [
    'builder_content' => 'array',
    'published_at' => 'datetime',
];

    protected $fillable = [
        'company_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'builder_content',
        'status',
        'show_in_menu',
        'sort_order',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'builder_content' => 'array',
            'show_in_menu' => 'boolean',
            'sort_order' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
