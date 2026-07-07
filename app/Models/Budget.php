<?php

namespace App\Models;

use App\Casts\MoneyCast;
use App\Enums\BudgetType;
use App\Models\Traits\BelongsToUser;
use Database\Factories\BudgetFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Override;

class Budget extends Model
{
    use BelongsToUser;

    /** @use HasFactory<BudgetFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'amount',
        'type',
    ];

    #[Override]
    protected function casts(): array
    {
        return [
            'type' => BudgetType::class,
            'amount' => MoneyCast::class,
        ];
    }

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
