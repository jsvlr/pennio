<?php

namespace App\Models\Traits;

use App\Models\Scopes\UserScope;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 */
trait BelongsToUser
{
    protected static function booted(): void
    {
        static::addGlobalScope(new UserScope);
    }
}
