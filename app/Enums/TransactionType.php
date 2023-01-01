<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum TransactionType: string implements HasColor, HasIcon, HasLabel
{
    case INCOME = 'income';
    case EXPENSE = 'expense';

    public function getLabel(): string
    {
        return match ($this) {
            self::INCOME => 'Pemasukan',
            self::EXPENSE => 'Pengeluaran',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::INCOME => 'success',
            self::EXPENSE => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::INCOME => 'heroicon-o-arrow-trending-up',
            self::EXPENSE => 'heroicon-o-arrow-trending-down',
        };
    }
}
