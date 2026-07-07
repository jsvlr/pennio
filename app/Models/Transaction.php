<?php

namespace App\Models;

use App\Casts\MoneyCast;
use App\Enums\TransactionType;
use App\Models\Traits\BelongsToUser;
use Database\Factories\TransactionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;

class Transaction extends Model
{
    use BelongsToUser;

    /** @use HasFactory<TransactionFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bank_account_id',
        'category_id',
        'budget_id',
        'type',
        'description',
        'amount',
        'note',
        'date',
    ];

    #[Override]
    protected function casts(): array
    {
        return [
            'amount' => MoneyCast::class,
            'type' => TransactionType::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }
}
