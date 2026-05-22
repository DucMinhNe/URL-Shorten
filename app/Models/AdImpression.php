<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdImpression extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = ['ad_campaign_id','short_link_id','click_id','impression_token','ip_address','was_clicked','created_at'];
    protected $casts = ['was_clicked' => 'boolean', 'created_at' => 'datetime'];

    public function adCampaign(): BelongsTo { return $this->belongsTo(AdCampaign::class); }
    public function shortLink(): BelongsTo { return $this->belongsTo(ShortLink::class); }
    public function click(): BelongsTo { return $this->belongsTo(Click::class); }
}
