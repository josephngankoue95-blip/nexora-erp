<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class WebsiteMedia extends Model
{
    protected $table = 'website_media';
    protected $fillable = ['company_id', 'original_name', 'path', 'mime_type', 'size'];
    protected $casts = ['company_id' => 'integer', 'size' => 'integer'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->path);
    }
}
