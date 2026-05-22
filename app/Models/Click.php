<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Click extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = ['short_link_id','ip_address','user_agent','referer','is_valid','earnings','created_at'];
    protected $casts = ['is_valid' => 'boolean', 'created_at' => 'datetime'];

    public function shortLink(): BelongsTo { return $this->belongsTo(ShortLink::class); }
}
