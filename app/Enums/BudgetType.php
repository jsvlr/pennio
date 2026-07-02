<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum BudgetType: string implements HasLabel
{
    case Reset = 'reset';
    case Rollover = 'rollover';

    public function getLabel(): string|\Illuminate\Contracts\Support\Htmlable|null
    {
        return match ($this) {
            self::Reset => 'Reset',
            self::Rollover => 'Rollover',
        };
    }
}
