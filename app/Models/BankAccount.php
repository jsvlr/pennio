<?php

namespace App\Models;

use App\Casts\MoneyCast;
use App\Models\Traits\BelongsToUser;
use Database\Factories\BankAccountFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Override;

class BankAccount extends Model
{
    use BelongsToUser;

    /** @use HasFactory<BankAccountFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'balance',
    ];

    #[Override]
    protected function casts(): array
    {
        return [
            'balance' => MoneyCast::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
