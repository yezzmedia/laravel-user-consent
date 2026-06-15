<?php

declare(strict_types=1);

namespace YezzMedia\Consent\Models;

use Illuminate\Database\Eloquent\Model;

final class ConsentDecision extends Model
{
    protected $fillable = [
        'category_slug',
        'user_id',
        'guest_token',
        'granted',
        'consented_at',
        'version',
    ];

    protected function casts(): array
    {
        return [
            'granted' => 'boolean',
            'consented_at' => 'datetime',
            'version' => 'integer',
        ];
    }
}
