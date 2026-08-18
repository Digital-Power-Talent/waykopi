<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebhookEvent extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'source',
        'event_id',
        'event_type',
        'payload',
        'processed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'processed_at' => 'datetime',
    ];

    public static function alreadyProcessed(string $source, string $eventId): bool
    {
        return static::where('source', '=', $source, 'and')
            ->where('event_id', '=', $eventId, 'and')
            ->exists();
    }
}
