<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum Condition: int implements HasColor, HasIcon, HasLabel
{
    case GOOD = 1;
    case BAD = 2;
    case BROKEN = 3;

    public function getLabel(): string
    {
        return match ($this) {
            self::GOOD => __('Baik'),
            self::BAD => __('Kurang Baik'),
            self::BROKEN => __('Rusak Berat'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::GOOD => 'success',
            self::BAD => 'warning',
            self::BROKEN => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::GOOD => 'heroicon-o-check-circle',
            self::BAD => 'heroicon-o-exclamation-circle',
            self::BROKEN => 'heroicon-o-x-circle',
        };
    }
}
