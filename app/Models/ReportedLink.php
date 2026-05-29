<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportedLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'short_link_id', 'reporter_user_id', 'reporter_email', 'reporter_ip',
        'reason', 'description', 'status', 'reviewed_by', 'reviewed_at',
        'admin_note', 'action_taken',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function shortLink(): BelongsTo
    {
        return $this->belongsTo(ShortLink::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_user_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
