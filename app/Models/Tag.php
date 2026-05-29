<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'color', 'icon', 'description', 'usage_count', 'is_featured'];

    protected $casts = [
        'is_featured' => 'boolean',
        'usage_count' => 'integer',
    ];

    public function shortLinks(): BelongsToMany
    {
        return $this->belongsToMany(ShortLink::class, 'short_link_tag');
    }
}
