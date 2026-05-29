<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'key', 'name', 'subject', 'body_html', 'body_text', 'variables',
        'locale', 'is_active', 'from_name', 'from_email', 'sent_count', 'last_sent_at',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
        'last_sent_at' => 'datetime',
    ];
}
