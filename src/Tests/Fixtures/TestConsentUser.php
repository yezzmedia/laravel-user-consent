<?php

declare(strict_types=1);

namespace YezzMedia\Consent\Tests\Fixtures;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

final class TestConsentUser extends Authenticatable
{
    use HasFactory;

    protected $table = 'users';

    protected static function newFactory(): TestConsentUserFactory
    {
        return new TestConsentUserFactory;
    }

    protected $fillable = [
        'name',
        'email',
        'password',
    ];
}
