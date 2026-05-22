<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IpViewLog extends Model
{
    public $timestamps = false;
    protected $table = 'ip_view_logs';
    protected $fillable = ['short_link_id','ip_address','viewed_at'];
    protected $casts = ['viewed_at' => 'datetime'];
    public $incrementing = false;
}
