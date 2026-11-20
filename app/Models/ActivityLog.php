<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    public $timestamps = false;
    const UPDATED_AT = null;

    protected $fillable = ['user_id', 'action', 'description', 'ip_address', 'created_at'];

    protected $casts = ['created_at' => 'datetime'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function record(int $userId, string $action, string $description, ?string $ip = null): void
    {
        self::create([
            'user_id'     => $userId,
            'action'      => $action,
            'description' => $description,
            'ip_address'  => $ip,
            'created_at'  => now(),
        ]);
    }
}
