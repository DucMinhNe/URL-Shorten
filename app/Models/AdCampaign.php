<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdCampaign extends Model
{
    use HasFactory;

    protected $fillable = ['name','placement','type','content','target_url','weight','status','start_at','end_at','impressions','clicks_count'];
    protected $casts = ['start_at' => 'datetime', 'end_at' => 'datetime'];

    public function impressionsRel(): HasMany { return $this->hasMany(AdImpression::class); }

    public function scopeActive($q)
    {
        return $q->where('status','active')
            ->where(fn($x) => $x->whereNull('start_at')->orWhere('start_at','<=', now()))
            ->where(fn($x) => $x->whereNull('end_at')->orWhere('end_at','>=', now()));
    }
}
